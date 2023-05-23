<?php

namespace D2U_Immo;

use d2u_addon_backend_helper;
use Exception;
use rex;
use rex_api_exception;
use rex_clang;
use rex_config;
use rex_dir;
use rex_mailer;
use rex_media;
use rex_media_service;
use rex_path;
use SimpleXMLElement;
use ZipArchive;

use function array_key_exists;
use function count;
use function in_array;
use function is_array;

/**
 * Imports an OpenImmo ZIP file.
 */
class ImportOpenImmo
{
    /** @var string complete folder name including parent folders for extracting OpenImmo import files */
    public string $extract_cache_folder = '';

    /** @var string complete folder name including parent folders containing OpenImmo import files */
    public string $import_folder = '';

    /** @var string complete file name including parent folders containing OpenImmo log file for current import */
    public string $log_file = '';

    /**
     * Create OpenImmo import object.
     */
    public function __construct()
    {
        $this->extract_cache_folder = rex_path::addonCache('d2u_immo', 'import'). DIRECTORY_SEPARATOR;
        if (!file_exists($this->extract_cache_folder)) {
            rex_dir::create(rex_path::base($this->extract_cache_folder));
        }
        $this->import_folder = rex_path::base(trim((string) rex_config::get('d2u_immo', 'import_folder'), DIRECTORY_SEPARATOR)). DIRECTORY_SEPARATOR;
        if (!file_exists($this->import_folder)) {
            rex_dir::create(rex_path::base($this->import_folder));
        }

        $this->log_file = rex_path::addonData('d2u_immo', 'openimmo_import_'. date('Y-m-d_H-i-s', time()) .'.log');
    }

    /**
     * Automatically import OpenImmo ZIP files.
     */
    public static function autoimport(): void
    {
        $openimmoimport = new self();

        // Check folder for extract files
        $zip_filenames = $openimmoimport->getZIPFiles();

        // Extract zip file an import
        foreach ($zip_filenames as $zip_filename) {
            $openimmoimport->importZIP($zip_filename);
        }

        // send mail
        $openimmoimport->sendImportLog();
    }

    /**
     * Deletes all extracted files and the zip file itself.
     * @param string $zip_filename ZIP filesname without folder names
     * @return bool true if deletion was successful, false if failur for at least one file occured
     */
    private function cleanUp($zip_filename)
    {
        $this->log('Cleaning up import cache and file.');
        $return = true;
        // keep only the 10 latest log- and zipfiles in addon data folder, delete older ones
        $log_files = glob(rex_path::addonData('d2u_immo') .'*.log');
        if (is_array($log_files) && count($log_files) > 10) {
            for ($i = 0; $i < count($log_files) - 10; ++$i) {
                if (false === unlink($log_files[$i])) {
                    $return = false;
                    $this->log('Could not delete old file "'. $log_files[$i] .'".');
                }
                $logfile_info = pathinfo($log_files[$i]);
                if (false === unlink(rex_path::addonData('d2u_immo', $logfile_info['filename'] .'.zip'))) {
                    $return = false;
                    $this->log('Could not delete old file "'. $log_files[$i] .'".');
                }
            }
        }

        $files = glob($this->extract_cache_folder .'*');
        if (is_array($files)) {
            // delete extracted files
            foreach ($files as $file) {
                if (false === unlink($file)) {
                    $return = false;
                    $this->log('Could not delete file "'. $file .'".');
                }
            }
            // move imported ZIP file to data folder
            $logfile_info = pathinfo($this->log_file);
            if (false === rename($this->import_folder . $zip_filename, rex_path::addonData('d2u_immo', $logfile_info['filename'] .'.zip'))) {
                $this->log('Could not move import file "'. $zip_filename .'" to cache.');
                $return = false;
            }
        }
        return $return;
    }

    /**
     * Get OpenImmo XML filename from export folder.
     * @return string file name including OpenImmo XML file
     */
    public function getOpenImmoXMLFile()
    {
        $all_filenames = scandir($this->extract_cache_folder);
        if (false === $all_filenames) {
            return '';
        }
        foreach ($all_filenames as $file) {
            if ('xml' === pathinfo($file, PATHINFO_EXTENSION)) {
                $xml = simplexml_load_file($this->extract_cache_folder . $file);
                if ($xml instanceof SimpleXMLElement && false !== $xml->xpath('//openimmo')) {
                    return $file;
                }
            }
        }

        return '';
    }

    /**
     * Get ZIP file names from import folder.
     * @return array<string> file names for all zip files in import folder
     */
    public function getZIPFiles()
    {
        $zip_filenames = [];

        // find ZIP files
        $all_filenames = scandir($this->import_folder);
        if (false === $all_filenames) {
            return $zip_filenames;
        }
        foreach ($all_filenames as $file) {
            if ('zip' === pathinfo($file, PATHINFO_EXTENSION)) {
                $zip_filenames[] = $file;
            }
        }

        return $zip_filenames;
    }

    /**
     * Import an OpenImmo ZIP file.
     * @param string $zip_filename Name of the file to be imported
     * @return bool true if import is successful
     */
    public function importZIP($zip_filename)
    {
        $this->log('Start importing "'. $zip_filename .'".');
        $return = true;
        $zip = new ZipArchive();
        if (true === $zip->open($this->import_folder . $zip_filename)) {
            $zip->extractTo($this->extract_cache_folder);
            $zip->close();

            $openimmo_xml = $this->getOpenImmoXMLFile();
            if ('' === $openimmo_xml) {
                $this->log('No OpenImmo XML file in "'. $zip_filename .'" detected.');
                $return = false;
            } else {
                $this->log('ZIP file "'. $zip_filename .'" with OpenImmo content extracted.');
                // Read and import XML file
                $this->importXML($openimmo_xml);
                // Property actions: TEIL va. VOLL TODO
            }
        } else {
            $this->log('ZIP file "'. $zip_filename .'" deleted, because it was not possible to read it.');
            $return = false;
        }
        // Clean up
        $this->cleanUp($zip_filename);

        return $return;
    }

    /**
     * Perform OpenImmo XML import.
     * @param string $openimmo_xml_filename XML file name
     * @return bool true if successfull
     */
    public function importXML($openimmo_xml_filename)
    {
        $clang_id = (int) rex_config::get('d2u_immo', 'import_default_lang', rex_clang::getStartId());
        $xml_contents = file_get_contents($this->extract_cache_folder . $openimmo_xml_filename);
        if (false === $xml_contents) {
            $this->log('Error reading "'. $openimmo_xml_filename .'" file with OpenImmo content.');
            return false;
        }

        // Get new properties
        $xml = new SimpleXMLElement($xml_contents);
        $import_type = 'VOLL';
        if (count($xml->uebertragung) > 0) {
            $import_type = $xml->uebertragung['umfang']->__toString();
        }
        if (count($xml->anbieter) > 0) {
            foreach ($xml->anbieter as $xml_anbieter) {
                // Get old stuff to be able to delete it later
                $old_properties = count($xml_anbieter->openimmo_anid) > 0 ? \D2U_Immo\Property::getAllForOpenImmoAnID($xml_anbieter->openimmo_anid, $clang_id) : [];
                $old_contacts = []; // Get them later from Properties
                $old_medias = [];
                foreach ($old_properties as $old_property) {
                    // Media
                    $property_medias = array_merge($old_property->documents, $old_property->ground_plans, $old_property->location_plans, $old_property->pictures, $old_property->pictures_360);
                    if (count($property_medias) > 0) {
                        foreach ($property_medias as $property_media) {
                            if (!in_array($property_media, $old_medias, true)) {
                                $old_medias[$property_media] = $property_media;
                            }
                        }
                    }
                    // Contacts
                    if ($old_property->contact instanceof Contact && !array_key_exists($old_property->contact->contact_id, $old_contacts)) {
                        $old_contacts[$old_property->contact->contact_id] = $old_property->contact;
                        if ('' !== $old_property->contact->picture && !in_array($old_property->contact->picture, $old_medias, true)) {
                            $old_medias[$old_property->contact->picture] = $old_property->contact->picture;
                        }
                    }
                }

                if (count($xml_anbieter->immobilie) > 0) {
                    foreach ($xml_anbieter->immobilie as $xml_immobilie) {
                        $property = null;
                        if (count($xml_immobilie->verwaltung_techn) > 0 && count($xml_immobilie->verwaltung_techn[0]->openimmo_obid) > 0) {
                            $property = Property::getByOpenImmoID($xml_immobilie->verwaltung_techn[0]->openimmo_obid, $clang_id);
                            // <verwaltung_techn>
                            // <aktion aktionart="CHANGE"/>
                            // </verwaltung_techn>
                            if ($property instanceof Property && count($xml_immobilie->verwaltung_techn[0]->aktion) > 0 && 'DELETE' === (string) $xml_immobilie->verwaltung_techn[0]->aktion['aktionart']) {
                                $medias_to_delete = array_merge($property->documents, $property->ground_plans, $property->location_plans, $property->pictures, $property->pictures_360);
                                $property->delete();
                                // Delete unused old pictures
                                foreach ($medias_to_delete as $media_to_delete) {
                                    try {
                                        rex_media_service::deleteMedia($media_to_delete);
                                        unset($old_medias[$media_to_delete]);
                                        self::log('Media '. $media_to_delete .' deleted.');
                                    } catch (rex_api_exception $exception) {
                                        self::log('Media '. $media_to_delete .' deletion requested, but is in use.');
                                    }
                                }
                                continue;
                            }
                        }
                        if (null === $property) {
                            $property = Property::factory($clang_id);
                        }

                        // <openimmo_anid>...</openimmo_anid>
                        if(count($xml_anbieter->openimmo_anid) > 0) {
                            $property->openimmo_anid = $xml_anbieter->openimmo_anid;
                        }

                        // <verwaltung_techn>
                        // <objektnr_intern>123456</objektnr_intern>
                        // <openimmo_obid>OD2U202304011137030002492344769</openimmo_obid>
                        // </verwaltung_techn>
                        if (count($xml_immobilie->verwaltung_techn) > 0) {
                            $verwaltung_techn = $xml_immobilie->verwaltung_techn[0];
                            if (count($verwaltung_techn->objektnr_intern) > 0) {
                                $property->internal_object_number = $verwaltung_techn->objektnr_intern;
                            }
                            if (count($verwaltung_techn->openimmo_obid) > 0) {
                                $property->openimmo_object_id = $verwaltung_techn->openimmo_obid;
                            }
                        }

                        // <objektkategorie>
                        if (count($xml_immobilie->objektkategorie) > 0) {
                            $objektkategorie = $xml_immobilie->objektkategorie[0];

                            // <nutzungsart WOHNEN="true" GEWERBE="false" ANLAGE="false" WAZ="false"/>
                            if (count($objektkategorie->nutzungsart) > 0) {
                                foreach ($objektkategorie->nutzungsart[0]->attributes() as $attribute => $value) {
                                    if ('true' === (string) $value) {
                                        $property->type_of_use = (string) $attribute;
                                        break;
                                    }
                                }
                            }

                            // <vermarktungsart KAUF="true" MIETE_PACHT="false" ERBPACHT="false" LEASING="false"/>
                            if (count($objektkategorie->vermarktungsart) > 0) {
                                foreach ($objektkategorie->vermarktungsart[0]->attributes() as $attribute => $value) {
                                    if ('true' === (string) $value) {
                                        $property->market_type = (string) $attribute;
                                        break;
                                    }
                                }
                            }

                            // <objektart>
                            // <wohnung wohnungtyp="PENTHOUSE"/>
                            // </objektart>
                            if (count($objektkategorie->objektart) > 0) {
                                $objektart = $objektkategorie->objektart[0];
                                if (count($objektart->buero_praxen) > 0) {
                                    $property->object_type = 'BUERO_PRAXEN';
                                    $property->office_type = $objektart->buero_praxen['buero_typ'];
                                } elseif (count($objektart->grundstueck) > 0) {
                                    $property->object_type = 'GRUNDSTUECK';
                                    $property->land_type = $objektart->grundstueck['grundst_typ'];
                                } elseif (count($objektart->hallen_lager_prod) > 0) {
                                    $property->object_type = 'HALLEN_LAGER_PROD';
                                    $property->hall_warehouse_type = $objektart->grundstueck['hallen_typ'];
                                } elseif (count($objektart->haus) > 0) {
                                    $property->object_type = 'HAUS';
                                    $property->house_type = $objektart->haus['haustyp'];
                                } elseif (count($objektart->parken) > 0) {
                                    $property->object_type = 'PARKEN';
                                    $property->parking_type = $objektart->haus['parken_typ'];
                                } elseif (count($objektart->wohnung) > 0) {
                                    $property->object_type = 'WOHNUNG';
                                    $property->apartment_type = $objektart->wohnung['wohnungtyp'];
                                } elseif (count($objektart->sonstige) > 0) {
                                    $property->object_type = 'SONSTIGE';
                                    $property->other_type = $objektart->sonstige['sonstige_typ'];
                                }
                            }

                            // <geo>
                            // <plz/>
                            // <ort/>
                            // <geokoordinaten breitengrad="22.22" laengengrad="22.22"/>
                            // <strasse/>
                            // <hausnummer/>
                            // <land iso_land="DEU"/>
                            // <etage>1</etage>
                            // </geo>
                            if (count($xml_immobilie->geo) > 0) {
                                $geo = $xml_immobilie->geo[0];
                                if (count($geo->plz) > 0) {
                                    $property->zip_code = $geo->plz;
                                }
                                if (count($geo->ort) > 0) {
                                    $property->city = $geo->ort;
                                }
                                if (count($geo->geokoordinaten) > 0) {
                                    $property->latitude = (float) $geo->geokoordinaten['breitengrad']->__toString();
                                    $property->longitude = (float) $geo->geokoordinaten['laengengrad']->__toString();
                                }
                                if (count($geo->strasse) > 0) {
                                    $property->street = $geo->strasse;
                                }
                                if (count($geo->hausnummer) > 0) {
                                    $property->house_number = $geo->hausnummer;
                                }
                                if (count($geo->land) > 0) {
                                    $property->country_code = $geo->land['iso_land'];
                                }
                                if (count($geo->etage) > 0) {
                                    $property->floor = (int) $geo->etage;
                                }
                            }

                            // <kontaktperson>
                            // <email_zentrale>foo@bar.de</email_zentrale>
                            // <email_direkt>foo@bar.de</email_direkt>
                            // <tel_zentrale>1</tel_zentrale>
                            // <tel_durchw>2</tel_durchw>
                            // <tel_fax>3</tel_fax>
                            // <tel_handy>4</tel_handy>
                            // <name/>
                            // <vorname/>
                            // <anrede_brief/>
                            // <firma/>
                            // <strasse/>
                            // <hausnummer/>
                            // <plz/>
                            // <ort/>
                            // <land iso_land="DEU"/>
                            // <foto location="EXTERN">
                            // <format>JPEG</format>
                            // <daten>
                            // <pfad>abc.jpg</pfad>
                            // </daten>
                            // </foto>
                            // </kontaktperson>
                            if (count($xml_immobilie->kontaktperson) > 0) {
                                $kontaktperson = $xml_immobilie->kontaktperson[0];
                                $contact = null;
                                if (count($kontaktperson->email_direkt) > 0 || count($kontaktperson->email_zentrale) > 0) {
                                    $contact = Contact::getByMail('' !== $kontaktperson->email_direkt ? $kontaktperson->email_direkt : $kontaktperson->email_zentrale);
                                }
                                if (null === $contact) {
                                    $contact = Contact::factory();
                                }

                                if (count($kontaktperson->email_direkt) > 0 || count($kontaktperson->email_zentrale) > 0) {
                                    $contact->email = '' !== $kontaktperson->email_direkt ? $kontaktperson->email_direkt : $kontaktperson->email_zentrale;
                                }
                                if (count($kontaktperson->tel_durchw) > 0 || count($kontaktperson->tel_zentrale) > 0) {
                                    $contact->phone = '' !== $kontaktperson->tel_durchw ? $kontaktperson->tel_durchw : $kontaktperson->tel_zentrale;
                                }
                                if (count($kontaktperson->tel_fax) > 0) {
                                    $contact->fax = $kontaktperson->tel_fax;
                                }
                                if (count($kontaktperson->tel_handy) > 0) {
                                    $contact->mobile = $kontaktperson->tel_handy;
                                }
                                if (count($kontaktperson->name) > 0) {
                                    $contact->lastname = $kontaktperson->name;
                                }
                                if (count($kontaktperson->vorname) > 0) {
                                    $contact->firstname = $kontaktperson->vorname;
                                }
                                if (count($kontaktperson->firma) > 0) {
                                    $contact->company = $kontaktperson->firma;
                                }
                                if (count($kontaktperson->strasse) > 0) {
                                    $contact->street = $kontaktperson->strasse;
                                }
                                if (count($kontaktperson->hausnummer) > 0) {
                                    $contact->house_number = $kontaktperson->hausnummer;
                                }
                                if (count($kontaktperson->plz) > 0) {
                                    $contact->zip_code = $kontaktperson->plz;
                                }
                                if (count($kontaktperson->ort) > 0) {
                                    $contact->city = $kontaktperson->ort;
                                }
                                if (count($kontaktperson->land) > 0) {
                                    $contact->country_code = $kontaktperson->land['iso_land'];
                                }
                                $contact_picture_filename = '';
                                if (count($kontaktperson->foto) > 0 && count($kontaktperson->foto->daten) > 0 && count($kontaktperson->foto->daten->pfad) > 0) {
                                    $contact_picture_url = 'EXTERN' === strtoupper($kontaktperson->foto['location']) ? $this->extract_cache_folder . trim($kontaktperson->foto->daten->pfad, DIRECTORY_SEPARATOR) : $kontaktperson->foto->daten->pfad;
                                    $contact_picture_pathInfo = pathinfo($contact_picture_url);
                                    $contact_picture_filename = d2u_addon_backend_helper::getMediapoolFilename($contact_picture_pathInfo['basename']);
                                    $contact_picture = rex_media::get($contact_picture_filename);
                                    if ($contact_picture instanceof rex_media && $contact_picture->fileExists()) {
                                        // File already imported, unset in $old_medias, because remaining ones will be deleted
                                        if (in_array($contact_picture->getFileName(), $old_medias, true)) {
                                            unset($old_medias[$contact_picture->getFileName()]);
                                        }
                                        self::log('Contact picture '. $contact_picture_filename .' already available in mediapool.');
                                    } else {
                                        // File exists only in database, but no more in file system: remove it before import
                                        if ($contact_picture instanceof rex_media) {
                                            try {
                                                rex_media_service::deleteMedia($contact_picture->getFileName());
                                            } catch (Exception $e) {
                                                self::log('Contact picture not found in file system. Error deleting media from mediapool database.');
                                            }
                                        }

                                        // Import
                                        $target_picture = rex_path::media($contact_picture_pathInfo['basename']);
                                        // Copy first
                                        if (copy($contact_picture_url, $target_picture)) {
                                            chmod($target_picture, rex::getFilePerm());

                                            $data = [];
                                            $data['category_id'] = (int) rex_config::get('d2u_immo', 'import_media_category');
                                            $data['title'] = $contact->firstname .' '. $contact->lastname;
                                            $data['file'] = [
                                                'name' => $contact_picture_pathInfo['basename'],
                                                'path' => rex_path::media($contact_picture_pathInfo['basename']),
                                            ];

                                            try {
                                                $media_info = rex_media_service::addMedia($data, false);
                                                $contact_picture_filename = $media_info['filename'];
                                                $contact_picture = rex_media::get($contact_picture_filename);
                                                self::log('Contact picture '. $media_info['filename'] .' importet into mediapool.');
                                            } catch (rex_api_exception $e) {
                                                self::log('Error: Contact picture '. $contact_picture_pathInfo['basename'] .' not importet: '. $e->getMessage());
                                            }
                                        }
                                    }
                                    if ($contact_picture instanceof rex_media) {
                                        $contact->picture = $contact_picture->getFileName();
                                    }
                                }
                                if (false === $contact->save()) {
                                    if (array_key_exists($contact->contact_id, $old_contacts)) {
                                        self::log('Contact '. $contact->firstname .' '. $contact->lastname .' updated.');
                                        unset($old_contacts[$contact->contact_id]);
                                    } else {
                                        self::log('Contact '. $contact->firstname .' '. $contact->lastname .' added.');
                                    }
                                } else {
                                    self::log('Error: saving contact '. $contact->firstname .' '. $contact->lastname .' failed.');
                                }
                                $property->contact = $contact;
                                if ('' !== $contact->picture && in_array($contact->picture, $old_medias, true)) {
                                    unset($old_medias[$contact->picture]);
                                }

                                // <preise>
                                // <kaufpreis>1</kaufpreis>
                                // <nettokaltmiete>1</nettokaltmiete>
                                // <nebenkosten>1</nebenkosten>
                                // <zzg_mehrwertsteuer>true</zzg_mehrwertsteuer>
                                // <kaufpreis_pro_qm>1</kaufpreis_pro_qm>
                                // <aussen_courtage mit_mwst="false"/>
                                // <courtage_hinweis>String</courtage_hinweis>
                                // <waehrung iso_waehrung="DEM"/>
                                // <kaution>1</kaution>
                                // <stp_carport stellplatzmiete="1" stellplatzkaufpreis="1" anzahl="1"/>
                                // <stp_duplex stellplatzmiete="1" stellplatzkaufpreis="1" anzahl="1"/>
                                // <stp_freiplatz stellplatzmiete="1" stellplatzkaufpreis="1" anzahl="1"/>
                                // <stp_garage stellplatzmiete="1" stellplatzkaufpreis="1" anzahl="1"/>
                                // <stp_parkhaus stellplatzmiete="1" stellplatzkaufpreis="1" anzahl="1"/>
                                // <stp_tiefgarage stellplatzmiete="1" stellplatzkaufpreis="1" anzahl="1"/>
                                // </preise>
                                if (count($xml_immobilie->preise) > 0) {
                                    $preise = $xml_immobilie->preise[0];
                                    if (count($preise->kaufpreis) > 0) {
                                        $property->purchase_price = (int) $preise->kaufpreis;
                                    }
                                    if (count($preise->nettokaltmiete) > 0) {
                                        $property->cold_rent = (int) $preise->nettokaltmiete;
                                    }
                                    if (count($preise->nebenkosten) > 0) {
                                        $property->additional_costs = (int) $preise->nebenkosten;
                                    }
                                    if (count($preise->zzg_mehrwertsteuer) > 0) {
                                        $property->price_plus_vat = 'true' === (string) strtolower($preise->zzg_mehrwertsteuer);
                                    }
                                    if (count($preise->kaufpreis_pro_qm) > 0) {
                                        $property->purchase_price_m2 = (int) $preise->kaufpreis_pro_qm;
                                    }
                                    if (count($preise->aussen_courtage) > 0) {
                                        $property->courtage = $preise->aussen_courtage;
                                        $property->courtage_incl_vat = 'true' === (string) strtolower($preise->aussen_courtage['mit_mwst']);
                                    }
                                    if (count($preise->waehrung) > 0) {
                                        $property->currency_code = strtoupper($preise->waehrung['iso_waehrung']);
                                    }
                                    if (count($preise->kaution) > 0) {
                                        $property->deposit = (int) $preise->kaution;
                                    }
                                    if (count($preise->stp_duplex) > 0) {
                                        $property->parking_space_duplex = (int) $preise->stp_duplex['anzahl'];
                                    }
                                    if (count($preise->stp_freiplatz) > 0) {
                                        $property->parking_space_simple = (int) $preise->stp_freiplatz['anzahl'];
                                    }
                                    if (count($preise->stp_garage) > 0) {
                                        $property->parking_space_garage = (int) $preise->stp_garage['anzahl'];
                                    }
                                    if (count($preise->stp_tiefgarage) > 0) {
                                        $property->parking_space_undergroundcarpark = (int) $preise->stp_tiefgarage['anzahl'];
                                    }
                                }

                                // <flaechen>
                                // <wohnflaeche>1</wohnflaeche>
                                // <gesamtflaeche>1</gesamtflaeche>
                                // <anzahl_zimmer>1</anzahl_zimmer>
                                // </flaechen>
                                if (count($xml_immobilie->flaechen) > 0) {
                                    $flaechen = $xml_immobilie->flaechen[0];
                                    if (count($flaechen->wohnflaeche) > 0) {
                                        $property->living_area = (float) $flaechen->wohnflaeche;
                                    }
                                    if (count($flaechen->gesamtflaeche) > 0) {
                                        $property->total_area = (float) $flaechen->gesamtflaeche;
                                    }
                                    if (count($flaechen->grundstuecksflaeche) > 0) {
                                        $property->land_area = (float) $flaechen->grundstuecksflaeche;
                                    }
                                    if (count($flaechen->anzahl_zimmer) > 0) {
                                        $property->rooms = (float) $flaechen->anzahl_zimmer;
                                    }
                                }

                                // <ausstattung>
                                // <wg_geeignet>false</wg_geeignet>
                                // <bad DUSCHE="1" WANNE="1" FENSTER="1" BIDET="1" PISSOIR="1"/>
                                // <kueche EBK="1" OFFEN="1" PANTRY="1"/>
                                // <boden FLIESEN="0" STEIN="0" TEPPICH="1" PARKETT="1" FERTIGPARKETT="0" LAMINAT="0" DIELEN="0" KUNSTSTOFF="1" ESTRICH="1" DOPPELBODEN="1" LINOLEUM="1" MARMOR="1" TERRAKOTTA="1" GRANIT="0"/>
                                // <heizungsart OFEN="1" ETAGE="1" ZENTRAL="1" FERN="1" FUSSBODEN="1"/>
                                // <befeuerung OEL="0" GAS="1" ELEKTRO="0" ALTERNATIV="0" SOLAR="1" ERDWAERME="0" LUFTWP="0" FERN="0" BLOCK="0" WASSER-ELEKTRO="0" PELLET="0"/>
                                // <fahrstuhl PERSONEN="1" LASTEN="1"/>
                                // <stellplatzart GARAGE="1" TIEFGARAGE="1" CARPORT="1" FREIPLATZ="1" PARKHAUS="1" DUPLEX="1"/>
                                // <rollstuhlgerecht>false</rollstuhlgerecht>
                                // <kabel_sat_tv>false</kabel_sat_tv>
                                // <breitband_zugang DSL="true" />
                                // </ausstattung>
                                if (count($xml_immobilie->ausstattung) > 0) {
                                    $ausstattung = $xml_immobilie->ausstattung[0];
                                    if (count($ausstattung->wg_geeignet) > 0) {
                                        $property->flat_sharing_possible = 'TRUE' === strtoupper($ausstattung->wg_geeignet);
                                    }
                                    if (count($ausstattung->bad) > 0) {
                                        $property->bath = [];
                                        foreach ($ausstattung->bad[0]->attributes() as $attribute => $value) {
                                            if ('TRUE' === strtoupper($value)) {
                                                $property->bath[] = $attribute;
                                            }
                                        }
                                    }
                                    if (count($ausstattung->kueche) > 0) {
                                        $property->kitchen = [];
                                        foreach ($ausstattung->kueche[0]->attributes() as $attribute => $value) {
                                            if ('TRUE' === strtoupper($value)) {
                                                $property->kitchen[] = $attribute;
                                            }
                                        }
                                    }
                                    if (count($ausstattung->boden) > 0) {
                                        $property->floor_type = [];
                                        foreach ($ausstattung->boden[0]->attributes() as $attribute => $value) {
                                            if ('TRUE' === strtoupper($value)) {
                                                $property->floor_type[] = $attribute;
                                            }
                                        }
                                    }
                                    if (count($ausstattung->heizungsart) > 0) {
                                        $property->heating_type = [];
                                        foreach ($ausstattung->heizungsart[0]->attributes() as $attribute => $value) {
                                            if ('TRUE' === strtoupper($value)) {
                                                $property->heating_type[] = $attribute;
                                            }
                                        }
                                    }
                                    if (count($ausstattung->befeuerung) > 0) {
                                        $property->firing_type = [];
                                        foreach ($ausstattung->befeuerung[0]->attributes() as $attribute => $value) {
                                            if ('TRUE' === strtoupper($value)) {
                                                $property->firing_type[] = $attribute;
                                            }
                                        }
                                    }
                                    if (count($ausstattung->fahrstuhl) > 0) {
                                        $property->elevator = [];
                                        foreach ($ausstattung->fahrstuhl[0]->attributes() as $attribute => $value) {
                                            if ('TRUE' === strtoupper($value)) {
                                                $property->elevator[] = $attribute;
                                            }
                                        }
                                    }
                                    if (count($ausstattung->stellplatzart) > 0) {
                                        foreach ($ausstattung->stellplatzart->attributes() as $attribute => $value) {
                                            if ('DUPLEX' === strtoupper($attribute) && 'true' === strtolower($value->__toString())) {
                                                $property->parking_space_duplex = 1;
                                            } elseif ('FREIPLATZ' === strtoupper($attribute) && 'true' === strtolower($value->__toString())) {
                                                $property->parking_space_simple = 1;
                                            } elseif ('GARAGE' === strtoupper($attribute) && 'true' === strtolower($value->__toString())) {
                                                $property->parking_space_garage = 1;
                                            } elseif ('TIEFGARAGE' === strtoupper($attribute) && 'true' === strtolower($value->__toString())) {
                                                $property->parking_space_undergroundcarpark = 1;
                                            }
                                        }
                                    }
                                    if (count($ausstattung->rollstuhlgerecht) > 0) {
                                        $property->wheelchair_accessable = 'true' === $ausstattung->rollstuhlgerecht;
                                    }
                                    if (count($ausstattung->kabel_sat_tv) > 0) {
                                        $property->cable_sat_tv = 'true' === $ausstattung->kabel_sat_tv;
                                    }
                                    if (count($ausstattung->breitband_zugang) > 0) {
                                        $property->broadband_internet = [];
                                        foreach ($ausstattung->breitband_zugang[0]->attributes() as $attribute => $value) {
                                            if ('TRUE' === strtoupper($value)) {
                                                $property->broadband_internet[] = $attribute;
                                            }
                                        }
                                    }
                                }

                                // <zustand_angaben>
                                // <baujahr>1230</baujahr>
                                // <zustand zustand_art="ENTKERNT"/>
                                // <energiepass>
                                // <epart>VERBRAUCH</epart>
                                // <gueltig_bis>01-2024</gueltig_bis>
                                // <energieverbrauchkennwert>80</energieverbrauchkennwert>
                                // <mitwarmwasser>false</mitwarmwasser>
                                // <endenergiebedarf/>
                                // </energiepass>
                                // </zustand_angaben>
                                if (count($xml_immobilie->zustand_angaben) > 0) {
                                    $zustand_angaben = $xml_immobilie->zustand_angaben[0];
                                    if (count($zustand_angaben->baujahr) > 0) {
                                        $property->construction_year = (int) $zustand_angaben->baujahr;
                                    }
                                    if (count($zustand_angaben->zustand) > 0) {
                                        $property->condition_type = $zustand_angaben->zustand['zustand_art'];
                                    }
                                    if (count($zustand_angaben->energiepass) > 0) {
                                        $energiepass = $zustand_angaben->energiepass[0];
                                        if (count($energiepass->epart) > 0) {
                                            $property->energy_pass = $energiepass->epart;
                                        }
                                        if (count($energiepass->gueltig_bis) > 0) {
                                            $property->energy_pass_valid_until = $energiepass->gueltig_bis;
                                        }
                                        if (count($energiepass->energieverbrauchkennwert) > 0) {
                                            $property->energy_consumption = $energiepass->energieverbrauchkennwert;
                                        }
                                        if (count($energiepass->mitwarmwasser) > 0) {
                                            $property->including_warm_water = 'true' === (string) $energiepass->mitwarmwasser;
                                        }
                                    }
                                }

                                // <freitexte>
                                // <objekttitel>Traumwohnung</objekttitel>
                                // <dreizeiler>Mit sagenhafter Aussicht</dreizeiler>
                                // <lage>Auf dem Berg</lage>
                                // <ausstatt_beschr>Sehr spartanische Berghütte</ausstatt_beschr>
                                // <objektbeschreibung>Traumwohnung mit sagenhafter Aussicht Preise zuzüglich Mehrwertsteuer.</objektbeschreibung>
                                // <sonstige_angaben>Sonstige Angaben</sonstige_angaben>
                                // </freitexte>
                                if (count($xml_immobilie->freitexte) > 0) {
                                    $freitexte = $xml_immobilie->freitexte[0];
                                    if (count($freitexte->objekttitel) > 0) {
                                        $property->name = $freitexte->objekttitel;
                                    }
                                    if (count($freitexte->dreizeiler) > 0) {
                                        $property->teaser = $freitexte->dreizeiler;
                                    }
                                    if (count($freitexte->lage) > 0) {
                                        $property->description_location = $freitexte->lage;
                                    }
                                    if (count($freitexte->ausstatt_beschr) > 0) {
                                        $property->description_equipment = $freitexte->ausstatt_beschr;
                                    }
                                    if (count($freitexte->objektbeschreibung) > 0) {
                                        $property->description = $freitexte->objektbeschreibung;
                                    }
                                    if (count($freitexte->sonstige_angaben) > 0) {
                                        $property->description_others = $freitexte->sonstige_angaben;
                                    }
                                }

                                // <anhaenge>
                                // <anhang location="EXTERN" gruppe="TITELBILD">
                                // <anhangtitel>Hochburg</anhangtitel>
                                // <format>image/jpeg</format>
                                // <daten>
                                // <pfad>2019-10-03_16-03-02_hochburg_1.jpg</pfad>
                                // </daten>
                                // </anhang>
                                // </anhaenge>
                                if (count($xml_immobilie->anhaenge) > 0 && count($xml_immobilie->anhaenge->anhang) > 0) {
                                    $property->pictures = [];
                                    $property->pictures_360 = [];
                                    $property->ground_plans = [];
                                    $property->location_plans = [];
                                    $property->documents = [];

                                    foreach ($xml_immobilie->anhaenge->anhang as $anhang) {
                                        if (count($anhang->daten) > 0 && count($anhang->daten->pfad) > 0) {
                                            $pfad = (string) $anhang->daten->pfad;
                                            $anhang_url = 'EXTERN' === strtoupper((string) $anhang['location']) ? $this->extract_cache_folder . trim($pfad, DIRECTORY_SEPARATOR) : $pfad;
                                            $anhang_pathInfo = pathinfo($anhang_url);
                                            $anhang_filename = d2u_addon_backend_helper::getMediapoolFilename($anhang_pathInfo['basename']);
                                            $anhang_rex_media = rex_media::get($anhang_filename);
                                            if ($anhang_rex_media instanceof rex_media && $anhang_rex_media->fileExists()) {
                                                // File already imported, unset in $old_medias, because remaining ones will be deleted
                                                if (in_array($anhang_rex_media->getFileName(), $old_medias, true)) {
                                                    unset($old_medias[$anhang_rex_media->getFileName()]);
                                                }
                                                self::log('Property attachment "'. $anhang_filename .'" of type "'. $anhang['gruppe'] .'" already available in mediapool.');
                                            } else {
                                                // File exists only in database, but no more in file system: remove it before import
                                                if ($anhang_rex_media instanceof rex_media) {
                                                    try {
                                                        rex_media_service::deleteMedia($anhang_rex_media->getFileName());
                                                    } catch (Exception $e) {
                                                        self::log('Property attachment "'. $anhang_filename .'" of type"'. $anhang['gruppe'] .'" not found in file system. Error deleting media from mediapool database.');
                                                    }
                                                }

                                                // Import
                                                $target_attachment = rex_path::media($anhang_pathInfo['basename']);
                                                // Copy first
                                                if (file_exists($anhang_url) && copy($anhang_url, $target_attachment)) {
                                                    chmod($target_attachment, rex::getFilePerm());

                                                    $data = [];
                                                    $data['category_id'] = (int) rex_config::get('d2u_immo', 'import_media_category', 0);
                                                    $data['title'] = count($anhang->anhangtitel) > 0 ? (string) $anhang->anhangtitel : '';
                                                    $data['file'] = [
                                                        'name' => $anhang_pathInfo['basename'],
                                                        'path' => rex_path::media($anhang_pathInfo['basename']),
                                                    ];

                                                    try {
                                                        $media_info = rex_media_service::addMedia($data, false);
                                                        $anhang_filename = $media_info['filename'];
                                                        $anhang_rex_media = rex_media::get($anhang_filename);
                                                        self::log('Property attachment "'. $media_info['filename'] .'" of type "'. $anhang['gruppe'] .'" importet into mediapool.');
                                                    } catch (rex_api_exception $e) {
                                                        self::log('Error: Property attachment "'. $anhang_pathInfo['basename'] .'" of type "'. $anhang['gruppe'] .'"  not importet: '. $e->getMessage());
                                                    }
                                                }
                                            }
                                            if ($anhang_rex_media instanceof rex_media) {
                                                if ('TITELBILD' === (string) $anhang['gruppe']) {
                                                    array_unshift($property->pictures, $anhang_rex_media->getFileName());
                                                } elseif ('BILD' === (string) $anhang['gruppe']) {
                                                    $property->pictures[] = $anhang_rex_media->getFileName();
                                                } elseif ('PANORAMA' === (string) $anhang['gruppe']) {
                                                    $property->pictures_360[] = $anhang_rex_media->getFileName();
                                                } elseif ('GRUNDRISS' === (string) $anhang['gruppe']) {
                                                    $property->ground_plans[] = $anhang_rex_media->getFileName();
                                                } elseif ('KARTEN_LAGEPLAN' === (string) $anhang['gruppe']) {
                                                    $property->location_plans[] = $anhang_rex_media->getFileName();
                                                } elseif ('DOKUMENTE' === (string) $anhang['gruppe']) {
                                                    $property->documents[] = $anhang_rex_media->getFileName();
                                                }

                                                if (in_array($anhang_rex_media->getFileName(), $old_medias, true)) {
                                                    unset($old_medias[$anhang_rex_media->getFileName()]);
                                                }
                                            }
                                        }
                                    }
                                }

                                // <verwaltung_objekt>
                                // <objektadresse_freigeben>true</objektadresse_freigeben>
                                // <verfuegbar_ab>2023-07-01</verfuegbar_ab>
                                // <vermietet>true</vermietet>
                                // <haustiere>true</haustiere>
                                // </verwaltung_objekt>
                                if (count($xml_immobilie->verwaltung_objekt) > 0) {
                                    $verwaltung_objekt = $xml_immobilie->verwaltung_objekt[0];
                                    if (count($verwaltung_objekt->objektadresse_freigeben) > 0) {
                                        $property->publish_address = 'true' === (string) $verwaltung_objekt->objektadresse_freigeben;
                                    }
                                    if (count($verwaltung_objekt->verfuegbar_ab) > 0) {
                                        $property->available_from = $verwaltung_objekt->verfuegbar_ab;
                                    }
                                    if (count($verwaltung_objekt->vermietet) > 0) {
                                        $property->rented = 'true' === (string) $verwaltung_objekt->vermietet;
                                    }
                                    if (count($verwaltung_objekt->haustiere) > 0) {
                                        $property->animals = 'true' === (string) $verwaltung_objekt->haustiere;
                                    }
                                }
                            }
                        }

                        $property->category = new Category((int) rex_config::get('d2u_immo', 'import_category_id', 0), $clang_id);
                        $property->online_status = 'online';
                        $property->translation_needs_update = 'no';

                        if (false === $property->save()) {
                            if (array_key_exists($property->property_id, $old_properties)) {
                                self::log('Property '. $property->name .' updated.');
                                unset($old_properties[$property->property_id]);
                            } else {
                                self::log('Property '. $property->name .' added.');
                            }
                        } else {
                            self::log('Error: saving property '. $property->name .' failed.');
                        }
                    }
                }
                
                // Delete unused old properties
                if('VOLL' === $import_type) {
                    foreach ($old_properties as $old_property) {
                        $old_property->delete(true);
                        self::log('Property '. $old_property->name .' deleted.');
                    }
                    // Delete unused old pictures
                    foreach ($old_medias as $old_picture) {
                        try {
                            rex_media_service::deleteMedia($old_picture);
                            self::log('Media '. $old_picture .' deleted.');
                        } catch (rex_api_exception $exception) {
                            self::log('Media '. $old_picture .' deletion requested, but is in use.');
                        }
                    }
                }

                // Delete unused old contacts
                foreach ($old_contacts as $old_contact) {
                    if(!$old_contact->hasProperties()) {
                        $old_contact->delete();
                        self::log('Contact '. $old_contact->firstname .' deleted.');
                    }
                }
            }
        }

        return true;
    }

    /**
     * Logs message.
     * @param string $message Message to be logged
     */
    private function log($message): void
    {
        $log = file_exists($this->log_file) ? file_get_contents($this->log_file) : '';

        $microtime = microtime(true);
        $log .= date('d.m.Y H:i:s', time()). '.' . sprintf('%03d', ($microtime - floor($microtime)) * 1000) .': '. $message . PHP_EOL;

        // Write to log
        file_put_contents($this->log_file, $log);
    }

    /**
     * Send log file. Recipient is set in addon settings.
     * @return bool true if email was sent, otherwise false
     */
    private function sendImportLog()
    {
        $mail = new rex_mailer();
        $mail->isHTML(false);
        $mail->CharSet = 'utf-8';
        $mail->addAddress((string) rex_config::get('d2u_immo', 'import_email'));

        $mail->Subject = 'D2U Immo Importbericht';

        $log_content = file_exists($this->log_file) ? file_get_contents($this->log_file) : '';
        if (false !== $log_content) {
            $mail->Body = $log_content;
            try {
                return $mail->send();
            } catch (Exception $e) {
                $this->log('Error sending log file via email: '. $e->getMessage());
            }
        }

        return false;
    }
}
