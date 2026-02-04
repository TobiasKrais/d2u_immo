<?php

namespace D2U_Immo;

use rex_i18n;
use rex_path;
use rex_url;
use ZipArchive;

use function count;
use function in_array;
use function strlen;

/**
 * Defines methods each export provider has to implement.
 */
abstract class AFTPExport extends AExport
{
    /** @var string Path to cache of the plugin. Initialized in constructor. */
    protected string $cache_path = '';

    /** @var array<string> list of documents that need to be added to ZIP export file */
    protected array $documents_for_zip = [];

    /** @var array<string> list of files that need to be added to ZIP export file */
    protected array $files_for_zip = [];

    /** @var string filename of the ZIP file for this export */
    protected string $zip_filename = '';

    /**
     * Constructor. Initializes variables.
     * @param Provider $provider Export Provider
     */
    public function __construct($provider)
    {
        parent::__construct($provider);

        // Set exported properties without export action to action "update"
        foreach ($this->export_properties as $exported_property) {
            if ('' === $exported_property->export_action) {
                $exported_property->export_action = 'update';
                $exported_property->save();
            }
        }
        $this->export_properties = ExportedProperty::getAll($this->provider);

        // Check if cache path exists - if not create it
        $this->cache_path = rex_path::pluginCache('d2u_immo', 'export');
        if (!is_dir($this->cache_path)) {
            mkdir($this->cache_path, 0o777, true);
        }
    }

    /**
     * Creates the filename for the zip file.
     * @return string zip filename
     */
    protected function getZipFileName()
    {
        if ('' !== $this->provider->ftp_filename) {
            $this->zip_filename = $this->provider->ftp_filename;
        } elseif ('' === $this->zip_filename) {
            $this->zip_filename = preg_replace('/[^a-zA-Z0-9]/', '', $this->provider->name) .'_'
                . trim($this->provider->customer_number) .'_'. $this->provider->type .'.zip';
        }
        return $this->zip_filename;
    }

    /**
     * Prepares all documents for export.
     * @param int $max_attachments Maximum number of attatchments per property
     */
    protected function prepareDocuments($max_attachments): void
    {
        foreach ($this->export_properties as $exported_property) {
            if ('add' === $exported_property->export_action || 'update' === $exported_property->export_action) {
                $property = new Property($exported_property->property_id, $this->provider->clang_id);
                $number_free_docs = $max_attachments - count($property->pictures) - count($property->ground_plans) - count($property->location_plans);
                if ($max_attachments > $number_free_docs) {
                    foreach ($property->documents as $document) {
                        if (strlen($document) > 3 && $number_free_docs > 0 && !in_array($document, $this->documents_for_zip, true)) {
                            $this->documents_for_zip[] = $document;
                            --$number_free_docs;
                        }
                    }
                }
            }
        }
    }

    /**
     * Creates the scaled image.
     * @param string $pic Picture filename
     * @return string Cached picture filename
     */
    protected function preparePicture($pic)
    {
        $cached_pic = parent::preparePicture($pic);
        if (!in_array($cached_pic, $this->files_for_zip, true)) {
            $this->files_for_zip[$pic] = $cached_pic;
        }
        return $cached_pic;
    }

    /**
     * Prepares all pictures for export.
     * @param int $max_pics Maximum number of pictures, default is 10
     */
    protected function preparePictures($max_pics = 10): void
    {
        foreach ($this->export_properties as $exported_property) {
            $pics_counter = 0;
            if ('add' === $exported_property->export_action || 'update' === $exported_property->export_action) {
                $property = new Property($exported_property->property_id, $this->provider->clang_id);
                foreach ($property->pictures as $picture) {
                    if (strlen($picture) > 3 && $pics_counter < $max_pics) {
                        $this->preparePicture($picture);
                        ++$pics_counter;
                    }
                }
                if ($this->provider->ftp_supports_360_pictures) {
                    foreach ($property->pictures_360 as $picture_360) {
                        if (strlen($picture_360) > 3 && $pics_counter < $max_pics) {
                            $this->preparePicture($picture_360);
                            ++$pics_counter;
                        }
                    }
                }
                foreach ($property->ground_plans as $groundplan) {
                    if (strlen($groundplan) > 3 && $pics_counter < $max_pics) {
                        $this->preparePicture($groundplan);
                        ++$pics_counter;
                    }
                }
                foreach ($property->location_plans as $location_plan) {
                    if (strlen($location_plan) > 3 && $pics_counter < $max_pics) {
                        $this->preparePicture($location_plan);
                        ++$pics_counter;
                    }
                }
            }
        }
    }

    /**
     * Uploads the zip file using FTP.
     * @return string error message
     */
    protected function upload()
    {
        // Establish connection and ...
        $connection_id = ftp_ssl_connect($this->provider->ftp_server);
        if (!$connection_id instanceof \FTP\Connection) {
            return rex_i18n::msg('d2u_immo_export_ftp_error_connection');
        }

        // ... login
        $login_result = ftp_login($connection_id, $this->provider->ftp_username, $this->provider->ftp_password);
        if (!$login_result) {
            // login failed
            if (extension_loaded('ssh2')) {
                // try to connect via SSH
                $connection_id_ssh = ssh2_connect($this->provider->ftp_server);
                ssh2_auth_password($connection_id_ssh, $this->provider->ftp_username, $this->provider->ftp_password);
                if (!ssh2_scp_send($connection_id_ssh, $this->cache_path . $this->getZipFileName(), $this->zip_filename, 0644)) {
                    return rex_i18n::msg('d2u_immo_export_ftp_error_upload');
                }
                // Close SSH connection
                ssh2_disconnect($connection_id_ssh);

                return '';
            }
            // try to connect without SSL
            $connection_id = ftp_connect($this->provider->ftp_server);
            if (!$connection_id instanceof \FTP\Connection) {
                return rex_i18n::msg('d2u_immo_export_ftp_error_connection');
            }
            $login_result = ftp_login($connection_id, $this->provider->ftp_username, $this->provider->ftp_password);
            if (!$login_result) {
                return rex_i18n::msg('d2u_immo_export_ftp_error_connection');
            }
        }
        // Passive mode
        ftp_pasv($connection_id, true);

        // Upload
        if (!ftp_put($connection_id, $this->zip_filename, $this->cache_path . $this->getZipFileName(), FTP_BINARY)) {
            return rex_i18n::msg('d2u_immo_export_ftp_error_upload');
        }

        // Close connection
        ftp_close($connection_id);

        return '';
    }

    /**
     * ZIPs pictures and property filename.
     * @param string $filename
     * @return string error message or empty if no errors occur
     */
    protected function zip($filename)
    {
        // Create ZIP
        $zip = new ZipArchive();
        if (true !== $zip->open($this->cache_path . $this->getZipFileName(), ZipArchive::CREATE)) {
            return rex_i18n::msg('d2u_immo_export_zip_cannot_create');
        }
        $zip->addFile($this->cache_path . $filename, $filename);
        foreach ($this->files_for_zip as $original_filename => $cachefilename) {
            $zip->addFile($cachefilename, $original_filename);
        }
        foreach ($this->documents_for_zip as $document_filename) {
            $zip->addFile(rex_url::media($document_filename), $document_filename);
        }
        $zip->close();
        return '';
    }
}
