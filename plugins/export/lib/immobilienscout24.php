<?php
namespace D2U_Immo;

/**
 * OpenImmo export class
 */
class ImmobilienScout24 extends AFTPExport {
	/**
	 * @var int Maximum number of attachments per property. ImmobilienScout24 
	 * allows only 15 pictures.
	 */
	private $max_pics = 15;
	
	/**
	 * @var string Filename of the XML file for this export. EuropeMachinery
	 * does not require a special name.
	 */
	protected $xml_filename = "is24immotransfer.xml";
	
	/**
	 * Perform the Export.
	 * @return string error message - if no errors occured, empty string is returned.
	 */
	public function export() {
		// Cleanup old export ZIP file
		if(file_exists($this->cache_path . $this->getZipFileName())) {
			unlink($this->cache_path . $this->getZipFileName());
		}
		
		// Prepare pictures
		$this->preparePictures($this->max_pics);
		$this->files_for_zip = array_unique($this->files_for_zip);
		// Prepare documents
		$this->prepareDocuments($this->max_pics);

		// Create XML file
		$error = $this->createXML();
		if($error != "") {
			return $error;
		}
		
		// Create ZIP
		$this->zip($this->xml_filename);
		
		// Cleanup xml file
		unlink($this->cache_path . $this->xml_filename);
		
		// Upload
		$error = $this->upload();
		if($error != "") {
			return $error;
		}
		
		// Save results in database
		$this->saveExportedProperties();
		
		return "";
	}
		
	/**
	 * Creates a ImmobilienScout24 XML file
	 * @return string containing error information, if occured
	 */
	function createXML() {
		// Wenn false, Daten werden auf ImmoScout nicht veroeffentlicht
		$publish = TRUE;

		// <?xml version="1.0" encoding="UTF-8">
		$xml = new \DOMDocument("1.0", "UTF-8");
		$xml->formatOutput = true;
		// <IS24ImmobilienTransfer xmlns="http://www.immobilienscout24.de/immobilientransfer" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.immobilienscout24.de/immobilientransfer is24immotransfer.xsd" EmailBeiFehler="schnittstellen@is24.de" ErstellerSoftware="SUPER-Makler" ErstellerSoftwareVersion="HOUSE-OF-CREAKTIV">
		$is24 = $xml->createElement("IS24ImmobilienTransfer");
		$is24_xmlns = $xml->createAttribute("xmlns");
		$is24_xmlns->appendChild($xml->createTextNode("http://www.immobilienscout24.de/immobilientransfer"));
		$is24->appendChild($is24_xmlns);
		$is24_xmlns_xsi = $xml->createAttribute("xmlns:xsi");
		$is24_xmlns_xsi->appendChild($xml->createTextNode("http://www.w3.org/2001/XMLSchema-instance"));
		$is24->appendChild($is24_xmlns_xsi);
		$is24_xsi_schema = $xml->createAttribute("xsi:schemaLocation");
		$is24_xsi_schema->appendChild($xml->createTextNode("http://www.immobilienscout24.de/immobilientransfer is24immotransfer.xsd"));
		$is24->appendChild($is24_xsi_schema);
		$is24_mail = $xml->createAttribute("EmailBeiFehler");
		$is24_mail->appendChild($xml->createTextNode($this->provider->company_email));
		$is24->appendChild($is24_mail);
		$is24_ersteller = $xml->createAttribute("ErstellerSoftware");
		$is24_ersteller->appendChild($xml->createTextNode("D2U_IMMO"));
		$is24->appendChild($is24_ersteller);
		$is24_ersteller_version = $xml->createAttribute("ErstellerSoftwareVersion");
		$is24_ersteller_version->appendChild($xml->createTextNode("1.0"));
		$is24->appendChild($is24_ersteller_version);
		$xml->appendChild($is24);

		// <Anbieter ScoutKundenID="IS24_Patricia">
		$anbieter = $xml->createElement("Anbieter");
		$anbieterID = $xml->createAttribute("ScoutKundenID");
		$anbieterID->appendChild($xml->createTextNode($this->provider->customer_number));
		$anbieter->appendChild($anbieterID);

		foreach($this->export_properties as $export_property) {
			if($export_property->export_action == "delete") {
				// Only full export supported. Do not include properties with action "delete"
				continue;
			}

			$property = new Property($export_property->property_id, $this->provider->clang_id);
			// <WohnungMiete Importmodus="importieren" AnbieterObjektID="(0) WHG-MIETE" GruppierungsID="1" Ueberschrift="(0) WHG-MIETE" Wohnflaeche="70" Nutzflaeche="87" Zimmer="3.0" Aufzug="true" BalkonTerrasse="true" Provisionspflichtig="true" Provision="2,2 MM" Provisionshinweis="-" Waehrung="EUR" AnzahlBadezimmer="1" AnzahlGaragenStellplaetze="1" AnzahlSchlafzimmer="1" Ausstattungsqualitaet="Gehoben" Barrierefrei="true" Baujahr="1988" BetreutesWohnen="false" Einbaukueche="false" Etage="2" Etagenzahl="5" Foerderung="false" FreiAb="Mitte 2009" GaesteWC="false" GartenBenutzung="false" Haustiere="erlaubt" WohnungKategorie="Etagenwohnung" Heizungsart="Zentralheizung" JahrLetzteModernisierung="2001" Keller="true" Objektzustand="Gepflegt" Parkplatz="Carport" StatusHP="aktiv" StatusIS24="aktiv" StatusVBM="aktiv" AktiveGruppen="9;34;51;23;58;59" Adressdruck="true">
			$objekttyp_wert = "";
			if(strtoupper($property->object_type) == "WOHNUNG" && strtoupper($property->market_type) == "KAUF") {
				$objekttyp_wert = "WohnungKauf";
			}
			else if(strtoupper($property->object_type) == "WOHNUNG" &&
					(strtoupper($property->market_type) == "MIETE_PACHT" || strtoupper($property->market_type) == "ERBPACHT" || strtoupper($property->market_type) == "LEASING")) {
				$objekttyp_wert = "WohnungMiete";
			}
			else if(strtoupper($property->object_type) == "HAUS" && strtoupper($property->market_type) == "KAUF") {
				$objekttyp_wert = "HausKauf";
			}
			else if(strtoupper($property->object_type) == "HAUS" &&
						(strtoupper($property->market_type) == "MIETE_PACHT" || strtoupper($property->market_type) == "ERBPACHT" || strtoupper($property->market_type) == "LEASING")) {
				$objekttyp_wert = "HausMiete";
			}
			else if(strtoupper($immo->type_of_use) == "ZIMMER") {
				$objekttyp_wert = "WGZimmer";
			}
			else if(strtoupper($property->object_type) == "GRUNDSTUECK") {
				if(strtoupper($property->type_of_use) == "WOHNEN" && strtoupper($property->market_type) == "KAUF") {
					$objekttyp_wert = "GrundstueckWohnenKauf";
				}
				else if(strtoupper($property->type_of_use) == "WOHNEN" && 
						(strtoupper($property->market_type) == "MIETE_PACHT" || strtoupper($property->market_type) == "ERBPACHT" || strtoupper($property->market_type) == "LEASING")) {
					$objekttyp_wert = "GrundstueckWohnenMiete";
				}
				else if(strtoupper($property->type_of_use) == "GEWERBE") {
					$objekttyp_wert = "GrundstueckGewerbe";
				}
			}
			else if(strtoupper($property->object_type) == "BUERO_PRAXEN") {
				$objekttyp_wert = "BueroPraxis";
			}
			// Garagen
			else if(strtoupper($property->object_type) == "SONSTIGE" && strtoupper($property->other_type) == "SONSTIGE") {
				if(strtoupper($property->market_type) == "KAUF") {
					$objekttyp_wert = "GarageKauf";
				}
				else if(strtoupper($property->market_type) == "MIETE_PACHT" || strtoupper($property->market_type) == "ERBPACHT" || strtoupper($property->market_type) == "LEASING") {
					$objekttyp_wert = "GarageMiete";
				}
			}

			if($objekttyp_wert == "") {
				// Combination of object type and market type not implemented
				continue;
			}
			$objekttyp = $xml->createElement($objekttyp_wert);

			/*
			 * Folgende Werte kommen in allen implementierten Objekttypen vor
			 */
			$objekttyp_importmodus = $xml->createAttribute("Importmodus");
			if($export_property->export_action == "add") {
				$objekttyp_importmodus->appendChild($xml->createTextNode("importieren"));
			}
			else if($export_property->export_action == "update") {
				$objekttyp_importmodus->appendChild($xml->createTextNode("aktualisieren"));
			}
			else if($export_property->export_action == "delete") {
				$objekttyp_importmodus->appendChild($xml->createTextNode("loeschen"));
			}
			else {
				$objekttyp_importmodus->appendChild($xml->createTextNode("ignorieren"));
			}
			$objekttyp->appendChild($objekttyp_importmodus);

			// TODO <xs:attribute name="ScoutObjektID" type="Zahl20Typ" use="optional"/>

			$objekttyp_anbieter_obid = $xml->createAttribute("AnbieterObjektID");
			$objekttyp_anbieter_obid->appendChild($xml->createTextNode($property->internal_object_number));
			$objekttyp->appendChild($objekttyp_anbieter_obid);

			// TODO: <xs:attribute name="GruppierungsID" type="Zahl10Typ" use="optional"/>

			// In: WohnungMiete, WohnungKauf, HausMiete, HausKauf, GrundstueckWohnenKauf
			$objekttyp_ueberschrift = $xml->createAttribute("Ueberschrift");
			$objekttyp_ueberschrift->appendChild($xml->createTextNode($property->name));
			$objekttyp->appendChild($objekttyp_ueberschrift);

			if($property->courtage != "") {
				if(strtoupper($property->market_type) == "MIETE_PACHT" || $objekttyp_wert == "HausKauf") {
					$objekttyp_provisionspflicht = $xml->createAttribute("Provisionspflichtig");
					$objekttyp_provisionspflicht->appendChild($xml->createTextNode("true"));
					$objekttyp->appendChild($objekttyp_provisionspflicht);
				}

				$objekttyp_provision = $xml->createAttribute("Provision");
				$objekttyp_provision->appendChild($xml->createTextNode($property->courtage));
				$objekttyp->appendChild($objekttyp_provision);

				// <xs:attribute name="Provisionshinweis" type="Text200Typ" use="optional"/>
				if($property->courtage_incl_vat) {
					$objekttyp_provisionshinweis = $xml->createAttribute("Provisionshinweis");
					$objekttyp_provisionshinweis->appendChild($xml->createTextNode("inkl. gesetzlicher Mehrwertsteuer"));
					$objekttyp->appendChild($objekttyp_provisionshinweis);
				}
			}

			$objekttyp_waehrung = $xml->createAttribute("Waehrung");
			$objekttyp_waehrung->appendChild($xml->createTextNode($property->currency_code));
			$objekttyp->appendChild($objekttyp_waehrung);

			// Werden folgende 2 Attribute auf inaktiv gesetzt sind sie nur im
			// Backend von ImmobilienScout zu sehen.
			// <xs:attribute name="StatusHP" type="StatusTyp" use="optional" default="aktiv">
			$statusHP = $xml->createAttribute("StatusHP");
			if($publish) {
				$statusHP->appendChild($xml->createTextNode("aktiv"));
			}
			else {
				$statusHP->appendChild($xml->createTextNode("inaktiv"));
			}
			$objekttyp->appendChild($statusHP);
			// <xs:attribute name="StatusIS24" type="StatusTyp" use="optional" default="aktiv"/>
			$statusIS24 = $xml->createAttribute("StatusIS24");
			if($publish) {
				$statusIS24->appendChild($xml->createTextNode("aktiv"));
			}
			else {
				$statusIS24->appendChild($xml->createTextNode("inaktiv"));
			}
			$objekttyp->appendChild($statusIS24);
			// TODO: <xs:attribute name="StatusVBM" type="StatusTyp" use="optional" default="aktiv"/>
			// TODO: <xs:attribute name="AktiveGruppen" type="IntList30Typ" use="optional">

			$objekttyp_addr_freigeben = $xml->createAttribute("Adressdruck");
			if($property->publish_address) {
				$objekttyp_addr_freigeben->appendChild($xml->createTextNode("true"));
			}
			else {
				$objekttyp_addr_freigeben->appendChild($xml->createTextNode("false"));
			}
			$objekttyp->appendChild($objekttyp_addr_freigeben);

			// <Adresse Strasse="Magazinstr." Hausnummer="15-16" Postleitzahl="10179" Ort="Berlin" Laenderkennzeichen="DEU"/>
			$objekt_adresse = $xml->createElement("Adresse");

			if($property->street != "") {
				$objekt_strasse = $xml->createAttribute("Strasse");
				$objekt_strasse->appendChild($xml->createTextNode($property->street));
				$objekt_adresse->appendChild($objekt_strasse);
			}

			if($property->house_number != "") {
				$objekt_hausnummer = $xml->createAttribute("Hausnummer");
				$objekt_hausnummer->appendChild($xml->createTextNode($property->house_number));
				$objekt_adresse->appendChild($objekt_hausnummer);
			}

			$objekt_plz = $xml->createAttribute("Postleitzahl");
			$objekt_plz->appendChild($xml->createTextNode($property->zip_code));
			$objekt_adresse->appendChild($objekt_plz);

			$objekt_ort = $xml->createAttribute("Ort");
			$objekt_ort->appendChild($xml->createTextNode($property->city));
			$objekt_adresse->appendChild($objekt_ort);

			if($property->country_code != "") {
				$objekt_land = $xml->createAttribute("Laenderkennzeichen");
				$objekt_land->appendChild($xml->createTextNode($property->country_code));
				$objekt_adresse->appendChild($objekt_land);

				// TODO Im Ausland muss der Regionalcode rein.
				if($property->country_code != "DEU") {
					$objekt_region = $xml->createAttribute("InternationaleRegion");
					$objekt_region->appendChild($xml->createTextNode("Unknown"));
					$objekt_adresse->appendChild($objekt_region);
				}
			}

			$objekttyp->appendChild($objekt_adresse);

			// <Kontaktperson Anrede="Herr" Vorname="Martin" Nachname="Mustermann" Strasse="Musterstr." Hausnummer="22" Ort="Musterhausen" Postleitzahl="12345" Laenderkennzeichen="DEU" Telefon="0123-43627272872" Mobiltelefon="0179-534538729238" Telefax="0123-43276728298" Homepage="www.makler-wer.de" EMail="info@wer.de"/>
			$kontakt_adresse = $xml->createElement("Kontaktperson");

			// TODO Anrede
			// if(strlen($property->contact->anrede) > 0) {
			//	$kontakt_anrede = $xml->createAttribute("Anrede");
			//	if($property->contact->anrede == "0") {
			//		$kontakt_anrede->appendChild($xml->createTextNode($I18N_REXIMMO->msg("herr")));
			//	}
			//	else if($property->contact->anrede == "1") {
			//		$kontakt_anrede->appendChild($xml->createTextNode($I18N_REXIMMO->msg("frau")));
			//	}
			//	$kontakt_adresse->appendChild($kontakt_anrede);
			// }

			if(strlen($property->contact->firstname) > 0) {
				$kontakt_vorname = $xml->createAttribute("Vorname");
				$kontakt_vorname->appendChild($xml->createTextNode($property->contact->firstname));
				$kontakt_adresse->appendChild($kontakt_vorname);
			}

			$kontakt_nachname = $xml->createAttribute("Nachname");
			$kontakt_nachname->appendChild($xml->createTextNode($property->contact->lastname));
			$kontakt_adresse->appendChild($kontakt_nachname);

			if(strlen($property->contact->street) > 0) {
				$kontakt_strasse = $xml->createAttribute("Strasse");
				$kontakt_strasse->appendChild($xml->createTextNode($property->contact->street));
				$kontakt_adresse->appendChild($kontakt_strasse);
			}

			if(strlen($property->contact->house_number) > 0) {
				$kontakt_hausnummer = $xml->createAttribute("Hausnummer");
				$kontakt_hausnummer->appendChild($xml->createTextNode($property->contact->house_number));
				$kontakt_adresse->appendChild($kontakt_hausnummer);
			}

			if(strlen($property->contact->city) > 0) {
				$kontakt_ort = $xml->createAttribute("Ort");
				$kontakt_ort->appendChild($xml->createTextNode($property->contact->city));
				$kontakt_adresse->appendChild($kontakt_ort);
			}

			if(strlen($property->contact->zip_code) > 0) {
				$kontakt_plz = $xml->createAttribute("Postleitzahl");
				$kontakt_plz->appendChild($xml->createTextNode($property->contact->zip_code));
				$kontakt_adresse->appendChild($kontakt_plz);
			}

			if(strlen($property->contact->country_code) > 0) {
				$kontakt_land = $xml->createAttribute("Laenderkennzeichen");
				$kontakt_land->appendChild($xml->createTextNode($property->contact->country_code));
				$kontakt_adresse->appendChild($kontakt_land);
			}

			if(strlen($property->contact->phone) > 0) {
				$kontakt_tel = $xml->createAttribute("Telefon");
				$kontakt_tel->appendChild($xml->createTextNode($property->contact->phone));
				$kontakt_adresse->appendChild($kontakt_tel);
			}

			if(strlen($property->contact->mobile) > 0) {
				$kontakt_handy = $xml->createAttribute("Mobiltelefon");
				$kontakt_handy->appendChild($xml->createTextNode($property->contact->mobile));
				$kontakt_adresse->appendChild($kontakt_handy);
			}

			if(strlen($property->contact->fax) > 0) {
				$kontakt_fax = $xml->createAttribute("Telefax");
				$kontakt_fax->appendChild($xml->createTextNode($property->contact->fax));
				$kontakt_adresse->appendChild($kontakt_fax);
			}

			// if(strlen($property->contact->url) > 0) {
			//	$kontakt_url = $xml->createAttribute("Homepage");
			//	$kontakt_url->appendChild($xml->createTextNode($property->contact->url));
			//	$kontakt_adresse->appendChild($kontakt_url);
			// }

			if(strlen($property->contact->email) > 0) {
				$kontakt_email = $xml->createAttribute("EMail");
				$kontakt_email->appendChild($xml->createTextNode($property->contact->email));
				$kontakt_adresse->appendChild($kontakt_email);
			}

			$objekttyp->appendChild($kontakt_adresse);

			// <Objektbeschreibung>ruhige, schön geschnittene, helle Wohung in attraktivem Haus, Aufzug, Parkdeck und Grünfläche</Objektbeschreibung>
			$objektbeschreibung = $xml->createElement("Objektbeschreibung");
			if(strlen($property->description) > 0) {
				$objektbeschreibung->appendChild($xml->createTextNode(\d2u_addon_frontend_helper::prepareEditorField($property->description)));
			}
			else {
				$objektbeschreibung->appendChild($xml->createTextNode("Keine Objektbeschreibung eingegeben."));
			}
			if($property->rent_plus_vat) {
				$objektbeschreibung .= "<p>". \Sprog\Wildcard::get('d2u_immo_rent_plus_vat', $property->clang_id) ."</p>";
			}
			$objekttyp->appendChild($objektbeschreibung);

			// <Lage>zentral Nähe Hauptbahnhof</Lage>
			if($property->description_location != "") {
				$lage = $xml->createElement("Lage");
				$lage->appendChild($xml->createTextNode(\d2u_addon_frontend_helper::prepareEditorField($property->description_location)));
				$objekttyp->appendChild($lage);
			}

			// <Ausstattung>vollständig gefließt, kompl. einger. offene EBK (Cerankochfeld,Spülmaschine), Wannenbad/WC, S/W-Balkon, KabelTV, Waschmaschine auf Wunsch vorhanden</Ausstattung>
			if($property->description_equipment != "") {
				$ausstatt = $xml->createElement("Ausstattung");
				$ausstatt->appendChild($xml->createTextNode(\d2u_addon_frontend_helper::prepareEditorField($property->description_equipment)));
				$objekttyp->appendChild($ausstatt);
			}

			//<SonstigeAngaben>Fahrradkeller und Trockenboden vorhanden</SonstigeAngaben>
			if($property->description_others != "") {
				$sonstige_angaben = $xml->createElement("SonstigeAngaben");
				$sonstige_angaben->appendChild($xml->createTextNode(\d2u_addon_frontend_helper::prepareEditorField($property->description_others)));
				$objekttyp->appendChild($sonstige_angaben);
			}

			// <MultimediaAnhang AnhangArt="video" Titel="Video" Dateityp=".MPG" Abspieldauer="22" Dateiname="video1.mgg"/>
			$objekt_anhaenge = [];
			$zaehler = 0;
			// Titelbild und danach Bilder auslesen
			foreach($property->pictures as $bild) {
				if(strlen($bild) > 3) {
					$objekt_anhaenge[$zaehler] = ["bild" => $bild];
					$zaehler++;
				}
			}
			// Grundrisse auslesen
			foreach($property->ground_plans as $grundriss) {
				if(strlen($grundriss) > 3 && $zaehler < $this->max_pics) {
					$objekt_anhaenge[$zaehler] = ["grundrissBild" => $grundriss];
					$zaehler++;
				}
			}
			// Lageplaene auslesen
			foreach($property->location_plans as $lageplan) {
				if(strlen($lageplan) > 3 && $zaehler < $this->max_pics) {
					$objekt_anhaenge[$zaehler] = ["bild" => $lageplan];
					$zaehler++;
				}
			}
			// Dokumente auslesen
			foreach($property->documents as $dokument) {
				$anhang_media = \rex_media::get($dokument);
				// Pruefen, ob Datei in Datenbank existiert
				if($anhang_media instanceof \rex_media && $zaehler < $this->max_pics) {
					if(strpos($anhang_media->getType(), "pdf") !== FALSE) {
						$objekt_anhaenge[$zaehler] = ["video" => $dokument];
					}
					else if (strpos($anhang_media->getType(), "image") !== FALSE) {
						$objekt_anhaenge[$zaehler] = ["bild" => $dokument];
					}
					else {
						// Kann nur Bilder und PDFs als Dokument uebertragen
						continue;
					}
					$zaehler++;
				}
			}

			// TODO implement Videos auslesen
			// foreach($property->videos as $video) {
			//	if(strlen($video) > 3) {
			//		$objekt_anhaenge[$zaehler] = ["video" => $video];
			//	}
			// }

			foreach($objekt_anhaenge as $objekt_anhang) {
				foreach($objekt_anhang as $media_type => $filename) {
					$anhang_media = \rex_media::get($filename);
					// Pruefen, ob Datei in Datenbank existiert
					if($anhang_media instanceof \rex_media) {
						$anhang = $xml->createElement("MultimediaAnhang");
						$anhang_art = $xml->createAttribute("AnhangArt");
						$anhang_art->appendChild($xml->createTextNode($media_type));
						$anhang->appendChild($anhang_art);
						if(strlen($anhang_media->getTitle()) > 0) {
							$anhang_titel = $xml->createAttribute("Titel");
							$anhang_titel->appendChild($xml->createTextNode(substr($anhang_media->getTitle(), 0, 30)));
							$anhang->appendChild($anhang_titel);
						}
						$anhang_typ = $xml->createAttribute("Dateityp");
						$aPathinfo = pathinfo($filename);
						$anhang_typ->appendChild($xml->createTextNode(strtoupper($aPathinfo['extension'])));
						$anhang->appendChild($anhang_typ);
						// TODO:<xs:attribute name="Abspieldauer" type="Zahl5Typ" use="optional"/>
						$anhang_name = $xml->createAttribute("Dateiname");
						$anhang_name->appendChild($xml->createTextNode(trim($filename)));
						$anhang->appendChild($anhang_name);
						$objekttyp->appendChild($anhang);
					}
				}
			}
			// TODO: <xs:attribute name="FreiAb" type="Text50Typ" use="optional"/>
			/*
			 * ENDE Wert kommen in allen Objekttypen vor
			 */

			if(strtoupper($property->object_type) == "HAUS" || strtoupper($property->object_type) == "WOHNUNG") {
				// In: WohnungMiete, WohnungKauf, HausMiete, HausKauf
				$objekttyp_wohnflaeche = $xml->createAttribute("Wohnflaeche");
				if($property->living_area > 0) {
					$objekttyp_wohnflaeche->appendChild($xml->createTextNode(number_format($property->living_area, 0, ".", "")));
				}
				else {
					$objekttyp_wohnflaeche->appendChild($xml->createTextNode("1"));
				}
				$objekttyp->appendChild($objekttyp_wohnflaeche);

				// In: WohnungMiete, WohnungKauf, HausMiete, HausKauf
				// Nutzflaeche = Gesamtflaeche - Wohnflaeche (Keller, Nebenraume)
				if($property->total_area > 0 && $property->living_area > 0) {
					$nutzflache_qm = $property->total_area - $property->living_area;
					$objekttyp_nutzflaeche = $xml->createAttribute("Nutzflaeche");
					$objekttyp_nutzflaeche->appendChild($xml->createTextNode(number_format($nutzflache_qm, 2, ".", "")));
					$objekttyp->appendChild($objekttyp_nutzflaeche);
				}

				// In: WohnungMiete, WohnungKauf, HausMiete, HausKauf
				$objekttyp_zimmer = $xml->createAttribute("Zimmer");
				if($immo->zimmer > 0.5) {
					$objekttyp_zimmer->appendChild($xml->createTextNode($immo->rooms));
				}
				else {
					$objekttyp_zimmer->appendChild($xml->createTextNode("0.5"));
				}
				$objekttyp->appendChild($objekttyp_zimmer);
	
				// In: WohnungMiete, WohnungKauf
				if(count($property->elevator) > 0 && strtoupper($property->object_type) == "WOHNUNG") {
					$objekttyp_aufzug = $xml->createAttribute("Aufzug");
					$objekttyp_aufzug->appendChild($xml->createTextNode("true"));
					$objekttyp->appendChild($objekttyp_aufzug);
				}

				// In: WohnungMiete, WohnungKauf
				// TODO: <xs:attribute name="BalkonTerrasse" type="xs:boolean" use="optional"/>

				// In: WohnungKauf, HausKauf
				// TODO: <xs:attribute name="Vermietet" type="xs:boolean" use="optional"/>
				if($property->rented) {
					$objekttyp_vermietet = $xml->createAttribute("Vermietet");
					$objekttyp_vermietet->appendChild($xml->createTextNode("true"));
					$objekttyp->appendChild($objekttyp_vermietet);
				}

				// In: WohnungKauf, HausKauf
				// TODO: <xs:attribute name="AlsFerienwohnungGeeignet" type="xs:boolean" use="optional"/>
				// In: WohnungMiete, WohnungKauf, HausMiete, HausKauf
				// TODO: <xs:attribute name="AnzahlBadezimmer" type="Zahl2Typ" use="optional"/>

				// In: WohnungMiete, WohnungKauf, HausMiete, HausKauf
				if($property->parking_space_garage > 0) {
					$objekttyp_stp_garage = $xml->createAttribute("AnzahlGaragenStellplaetze");
					$objekttyp_stp_garage->appendChild($xml->createTextNode($property->parking_space_garage));
					$objekttyp->appendChild($objekttyp_stp_garage);
				}

				// In: WohnungMiete, WohnungKauf, HausMiete, HausKauf
				// TODO: <xs:attribute name="AnzahlSchlafzimmer" type="Zahl2Typ" use="optional"/>

				// In: WohnungMiete, WohnungKauf, HausMiete
				// if(stristr(strtoupper($property->serviceleistungen), "BETREUTES_WOHNEN") != false && $objekttyp_wert != "HausKauf") {
				//	$objekttyp_bet_wohnen = $xml->createAttribute("BetreutesWohnen");
				//	$objekttyp_bet_wohnen->appendChild($xml->createTextNode("true"));
				//	$objekttyp->appendChild($objekttyp_bet_wohnen);
				// }

				// In: WohnungMiete, WohnungKauf, HausMiete
				if(in_array("EBK", $property->kitchen) && $objekttyp_wert != "HausKauf") {
					$objekttyp_ebk = $xml->createAttribute("Einbaukueche");
					$objekttyp_ebk->appendChild($xml->createTextNode("true"));
					$objekttyp->appendChild($objekttyp_ebk);
				}

				// In: WohnungMiete, WohnungKauf, HausMiete
				if($property->floor > 0 && $objekttyp_wert != "HausKauf") {
					$objekttyp_etage = $xml->createAttribute("Etage");
					$objekttyp_etage->appendChild($xml->createTextNode($property->floor));
					$objekttyp->appendChild($objekttyp_etage);
				}

				// In: WohnungMiete
				// TODO: <xs:attribute name="Foerderung" type="xs:boolean" use="optional"/>
				// In: WohnungMiete, WohnungKauf, HausMiete, HausKauf
				// TODO: <xs:attribute name="GaesteWC" type="xs:boolean" use="optional"/>

				// In: WohnungMiete, WohnungKauf
				// TODO: <xs:attribute name="GartenBenutzung" type="xs:boolean" use="optional"/>

				// In: WohnungMiete, HausMiete
				if($property->animals && strtoupper($property->market_type) == "MIETE_PACHT") {
					$objekttyp_haustiere = $xml->createAttribute("Haustiere");
					$objekttyp_haustiere->appendChild($xml->createTextNode("erlaubt"));
					$objekttyp->appendChild($objekttyp_haustiere);
				}

				if(strtoupper($property->object_type) == "WOHNUNG" && $property->apartment_type != "") {
					// In: WohnungMiete, WohnungKauf
					$objekttyp_kategorie = $xml->createAttribute("WohnungKategorie");
					if(strtoupper($property->apartment_type) == "DACHGESCHOSS") {
						$objekttyp_kategorie->appendChild($xml->createTextNode("Dachgeschoss"));
					}
					else if(strtoupper($property->apartment_type) == "MAISONETTE") {
						$objekttyp_kategorie->appendChild($xml->createTextNode("Maisonette"));
					}
					else if(strtoupper($property->apartment_type) == "LOFT-STUDIO-ATELIER") {
						$objekttyp_kategorie->appendChild($xml->createTextNode("Loft"));
					}
					else if(strtoupper($property->apartment_type) == "PENTHOUSE") {
						$objekttyp_kategorie->appendChild($xml->createTextNode("Penthouse"));
					}
					else if(strtoupper($property->apartment_type) == "TERRASSEN") {
						$objekttyp_kategorie->appendChild($xml->createTextNode("Terrassenwohnung"));
					}
					else if(strtoupper($property->apartment_type) == "ETAGE") {
						$objekttyp_kategorie->appendChild($xml->createTextNode("Etagenwohnung"));
					}
					else if(strtoupper($property->apartment_type) == "ERDGESCHOSS") {
						$objekttyp_kategorie->appendChild($xml->createTextNode("Erdgeschoss"));
					}
					else if(strtoupper($property->apartment_type) == "SOUTERRAIN") {
						$objekttyp_kategorie->appendChild($xml->createTextNode("Souterrain"));
					}
					else {
						$objekttyp_kategorie->appendChild($xml->createTextNode("keineAngabe"));
					}
					$objekttyp->appendChild($objekttyp_kategorie);
				}
				else if(strtoupper($property->object_type) == "HAUS" && $property->house_type != "") {
					// In: HausMiete, HausKauf
					$objekttyp_kategorie = $xml->createAttribute("HausKategorie");
					if(strtoupper($property->house_type) == "REIHENEND" || strtoupper($property->house_type) == "REIHENECK") {
						$objekttyp_kategorie->appendChild($xml->createTextNode("Reiheneckhaus"));
					}
					else if(strtoupper($property->house_type) == "DOPPELHAUSHAELFTE") {
						$objekttyp_kategorie->appendChild($xml->createTextNode("Doppelhaushaelfte"));
					}
					else if(strtoupper($property->house_type) == "EINFAMILIENHAUS") {
						$objekttyp_kategorie->appendChild($xml->createTextNode("Einfamilienhaus"));
					}
					else if(strtoupper($property->house_type) == "BUNGALOW") {
						$objekttyp_kategorie->appendChild($xml->createTextNode("Bungalow"));
					}
					else if(strtoupper($property->house_type) == "VILLA") {
						$objekttyp_kategorie->appendChild($xml->createTextNode("Villa"));
					}
					else if(strtoupper($property->house_type) == "BAUERNHAUS") {
						$objekttyp_kategorie->appendChild($xml->createTextNode("Bauernhaus"));
					}
					else if(strtoupper($property->house_type) == "SCHLOSS") {
						$objekttyp_kategorie->appendChild($xml->createTextNode("BurgSchloss"));
					}
					else if(strtoupper($property->house_type) == "ZWEIFAMILIENHAUS" || strtoupper($property->house_type) == "MEHRFAMILIENHAUS") {
						$objekttyp_kategorie->appendChild($xml->createTextNode("Mehrfamilienhaus"));
					}
					else {
						$objekttyp_kategorie->appendChild($xml->createTextNode("Sonstiges"));
					}
					$objekttyp->appendChild($objekttyp_kategorie);
				}

				// In: HausMiete, HausKauf
				// TODO: <xs:attribute name="Bauphase" type="BauphaseTyp" use="optional"/>
				// In: HausKauf
				// TODO: <xs:attribute name="MitEinliegerwohnung" type="xs:boolean" use="optional"/>

				// In: WohnungMiete, WohnungKauf, HausMiete, HausKauf
				$objekttyp_parkplatz = $xml->createAttribute("Parkplatz");
				if($property->parking_space_garage > 0) {
					$objekttyp_parkplatz->appendChild($xml->createTextNode("Garage"));
				}
				else if($property->parking_space_undergroundcarpark > 0) {
					$objekttyp_parkplatz->appendChild($xml->createTextNode("Tiefgarage"));
				}
				else if($property->parking_space_simple > 0) {
					$objekttyp_parkplatz->appendChild($xml->createTextNode("AussenStellplatz"));
				}
				else if($property->parking_space_duplex > 0) {
					$objekttyp_parkplatz->appendChild($xml->createTextNode("Duplex"));
				}
				else {
					$objekttyp_parkplatz->appendChild($xml->createTextNode("keineAngabe"));
				}
				$objekttyp->appendChild($objekttyp_parkplatz);

				if(strtoupper($property->market_type) == "MIETE_PACHT") {
					// <Mietpreise Kaltmiete="3456.00" Heizkosten="55.50" Nebenkosten="85.00" Kaution="3 MM" StellplatzMiete="45.00" HeizkostenInWarmmieteEnthalten="false"/>
					$mietpreis = $xml->createElement("Mietpreise");
					// In: WohnungMiete, HausMiete
					$kaltmiete = $xml->createAttribute("Kaltmiete");
					$kaltmiete->appendChild($xml->createTextNode($property->cold_rent));
					$mietpreis->appendChild($kaltmiete);
					// In: WohnungMiete, HausMiete
					// TODO: <xs:attribute name="Heizkosten" type="Zahl152Typ" use="optional">
					// In: WohnungMiete, HausMiete
					$nebenkosten = $xml->createAttribute("Nebenkosten");
					$nebenkosten->appendChild($xml->createTextNode($property->additional_costs));
					$mietpreis->appendChild($nebenkosten);
					// In: WohnungMiete, HausMiete
					if(strlen($property->deposit) > 0) {
						$kaution = $xml->createAttribute("Kaution");
						$kaution->appendChild($xml->createTextNode($property->deposit));
						$mietpreis->appendChild($kaution);
					}
					// In: WohnungMiete, HausMiete
					// TODO: <xs:attribute name="StellplatzMiete" type="Zahl152Typ" use="optional"/>
					// In: WohnungMiete, HausMiete
					$heizung_in_warmmiete = $xml->createAttribute("HeizkostenInWarmmieteEnthalten");
					$heizung_in_warmmiete->appendChild($xml->createTextNode("true"));
					$mietpreis->appendChild($heizung_in_warmmiete);
					$objekttyp->appendChild($mietpreis);
				}
				else if(strtoupper($property->market_type) == "KAUF") {
					// <Kaufpreise Kaufpreis="24578383.00" MieteinnahmenProMonat="3455" StellplatzKaufpreis="435353" Wohngeld="300.50"/>
					$kaufpreise = $xml->createElement("Kaufpreise");
					// In: WohnungKauf, HausKauf
					$kaufpreis = $xml->createAttribute("Kaufpreis");
					if($property->purchase_price > 0) {
						$kaufpreis->appendChild($xml->createTextNode($property->purchase_price));
					}
					else {
						$kaufpreis->appendChild($xml->createTextNode("1.00"));
					}
					$kaufpreise->appendChild($kaufpreis);
					// In: WohnungKauf, HausKauf
					// TODO: <xs:attribute name="MieteinnahmenProMonat" type="Zahl152Typ" use="optional"/>
					// In: WohnungKauf, HausKauf
					// TODO: <xs:attribute name="StellplatzKaufpreis" type="Zahl152Typ" use="optional"/>
					// In: WohnungKauf, HausKauf
					// TODO Hausgeld
					// if(is_numeric($property->hausgeld) && $property->hausgeld > 0) {
					//	$wohngeld = $xml->createAttribute("Wohngeld");
					//	$wohngeld->appendChild($xml->createTextNode($property->hausgeld));
					//	$kaufpreise->appendChild($wohngeld);
					// }
					$objekttyp->appendChild($kaufpreise);
				}
			}

			// In: WohnungMiete, WohnungKauf, HausMiete, HausKauf, BueroPraxis, Einzelhandel
			if(strtoupper($property->object_type) == "HAUS" || strtoupper($property->object_type) == "WOHNUNG" || strtoupper($property->object_type) == "BUERO_PRAXEN" || strtoupper($property->object_type) == "EINZELHANDEL") {
				// TODO: <xs:attribute name="Ausstattungsqualitaet" type="AusstattungsqualitaetsTyp" use="optional" default="KeineAngabe"/>

				if($property->construction_year != "") {
					$objekttyp_baujahr = $xml->createAttribute("Baujahr");
					$objekttyp_baujahr->appendChild($xml->createTextNode($property->construction_year));
					$objekttyp->appendChild($objekttyp_baujahr);
				}

				// In: WohnungKauf, HausKauf, BueroPraxis
				// TODO: <xs:attribute name="Denkmalschutzobjekt" type="xs:boolean" use="optional"/>
				// In: WohnungMiete, WohnungKauf, HausKauf, BueroPraxis
				// TODO: <xs:attribute name="Etagenzahl" type="Zahl3Typ" use="optional"/>
				// In: WohnungMiete, WohnungKauf, HausMiete, HausKauf, BueroPraxis

				// Mehrauswahl moeglich  ||ZENTRAL||FUSSBODEN||
				$objekttyp_heizung = $xml->createAttribute("Heizungsart");

				if(in_array("OFEN", $property->heating_type)) {
					$objekttyp_heizung->appendChild($xml->createTextNode("Ofenheizung"));
				}
				else if(in_array("ETAGE", $property->heating_type)) {
					$objekttyp_heizung->appendChild($xml->createTextNode("Etagenheizung"));
				}
				else if(in_array("ZENTRAL", $property->heating_type)) {
					$objekttyp_heizung->appendChild($xml->createTextNode("Zentralheizung"));
				}
				else {
					$objekttyp_heizung->appendChild($xml->createTextNode("keineAngabe"));
				}
				$objekttyp->appendChild($objekttyp_heizung);

				// In: WohnungMiete, WohnungKauf, HausMiete, HausKauf, BueroPraxis
				// TODO: <xs:attribute name="JahrLetzteModernisierung" type="Zahl4Typ" use="optional">
				// In: WohnungMiete, WohnungKauf, HausMiete, HausKauf, BueroPraxis
				// TODO: <xs:attribute name="Keller" type="xs:boolean" use="optional"/>

				// In: WohnungMiete, WohnungKauf, HausMiete
				$objekttyp_zustand = $xml->createAttribute("Objektzustand");
				if(strtoupper($property->condition_type) == "ERSTBEZUG") {
					$objekttyp_zustand->appendChild($xml->createTextNode("Erstbezug"));
				}
				else if(strtoupper($property->condition_type) == "NEUWERTIG") {
					$objekttyp_zustand->appendChild($xml->createTextNode("Neuwertig"));
				}
				else if(strtoupper($property->condition_type) == "TEIL_VOLLRENOVIERT") {
					$objekttyp_zustand->appendChild($xml->createTextNode("VollstaendigReonviert"));
				}
				else if(strtoupper($property->condition_type) == "TEIL_VOLLRENOVIERUNGSBED") {
					$objekttyp_zustand->appendChild($xml->createTextNode("Renovierungsbeduerftig"));
				}
				else if(strtoupper($property->condition_type) == "NACH_VEREINBARUNG") {
					$objekttyp_zustand->appendChild($xml->createTextNode("NachVereinbarung"));
				}
				else if(strtoupper($property->condition_type) == "GEPFLEGT") {
					$objekttyp_zustand->appendChild($xml->createTextNode("Gepflegt"));
				}
				else if(strtoupper($property->condition_type) == "TEIL_VOLLSANIERT") {
					$objekttyp_zustand->appendChild($xml->createTextNode("Saniert"));
				}
				else {
					$objekttyp_zustand->appendChild($xml->createTextNode("keineAngabe"));
				}
				$objekttyp->appendChild($objekttyp_zustand);

				// <BefeuerungsArt> OEL, GAS, ELEKTRO, ALTERNATIV, SOLAR, ERDWAERME, LUFTWP, FERN, BLOCK, WASSER-ELEKTRO
				$befeuerungsart = $xml->createElement("BefeuerungsArt");
				if(in_array("FERN", $property->firing_type) || in_array("OEL", $property->firing_type) || in_array("GAS", $property->firing_type) || in_array("ELEKTRO", $property->firing_type) ||
						in_array("SOLAR", $property->firing_type) || in_array("ERDWAERME", $property->firing_type)) {
					if(in_array("FERN", $property->firing_type)) {
						// <Fernwaerme/>
						$befeuerungsart_typ = $xml->createElement("Fernwaerme");
						$befeuerungsart->appendChild($befeuerungsart_typ);
					}
					if(in_array("OEL", $property->firing_type)) {
						// <Oel/>
						$befeuerungsart_typ = $xml->createElement("Oel");
						$befeuerungsart->appendChild($befeuerungsart_typ);
					}
					if(in_array("GAS", $property->firing_type)) {
						// <Gas/>
						$befeuerungsart_typ = $xml->createElement("Gas");
						$befeuerungsart->appendChild($befeuerungsart_typ);
					}
					if(in_array("ELEKTRO", $property->firing_type)) {
						// <Strom/>
						$befeuerungsart_typ = $xml->createElement("Strom");
						$befeuerungsart->appendChild($befeuerungsart_typ);
					}
					if(in_array("SOLAR", $property->firing_type)) {
						// <Solarheizung/>
						$befeuerungsart_typ = $xml->createElement("Solarheizung");
						$befeuerungsart->appendChild($befeuerungsart_typ);
					}
					if(in_array("ERDWAERME", $property->firing_type)) {
						// <Erdwaerme/>
						$befeuerungsart_typ = $xml->createElement("Erdwaerme");
						$befeuerungsart->appendChild($befeuerungsart_typ);
					}
				}
				else {
					// <KeineAngabe/>
					$befeuerungsart_typ = $xml->createElement("KeineAngabe");
					$befeuerungsart->appendChild($befeuerungsart_typ);
				}
				// </BefeuerungsArt>
				$objekttyp->appendChild($befeuerungsart);

				// <Energieausweis Energieausweistyp="Energieverbrauchskennwert" Energieverbrauchskennwert="73.4" WarmwasserEnthalten="true"/>
				if((strtoupper($property->energy_pass) == "VERBRAUCH" || strtoupper($property->energy_pass_valid_until) == "BEDARF") && strlen($property->energy_consumption) > 0) {
					$energieausweis = $xml->createElement("Energieausweis");
					$energieausweis_typ = $xml->createAttribute("Energieausweistyp");
					if(strtoupper($property->energy_pass) == "VERBRAUCH") {
						$energieausweis_typ->appendChild($xml->createTextNode("Energieverbrauchskennwert"));
					}
					else if(strtoupper($property->energy_pass) == "BEDARF") {
						$energieausweis_typ->appendChild($xml->createTextNode("Endenergiebedarf"));
					}
					$energieausweis->appendChild($energieausweis_typ);
					$energieausweis_kennwert = $xml->createAttribute("Energieverbrauchskennwert");
					$energieausweis_kennwert->appendChild($xml->createTextNode(number_format($property->energy_consumption, 2, ".", "")));
					$energieausweis->appendChild($energieausweis_kennwert);
					if(strtoupper($property->energy_pass) == "VERBRAUCH") {
						$energieausweis_mit_warmwasser = $xml->createAttribute("WarmwasserEnthalten");
						if($property->including_warm_water) {
							$energieausweis_mit_warmwasser->appendChild($xml->createTextNode("true"));
						}
						else {
							$energieausweis_mit_warmwasser->appendChild($xml->createTextNode("false"));
						}
						$energieausweis->appendChild($energieausweis_mit_warmwasser);
					}
					$objekttyp->appendChild($energieausweis);
				}
			} // ENDE In: Wohnung, Haus, BueroPraxis, Einzelhandel

			// In: WohnungMiete, WohnungKauf, HausMiete, HausKauf, BueroPraxis
			if(strtoupper($property->object_type) == "HAUS" || strtoupper($property->object_type) == "WOHNUNG" || strtoupper($property->object_type) == "BUERO_PRAXEN" || strtoupper($property->object_type) == "EINZELHANDEL") {
				if($property->wheelchair_accessable) {
					$objekttyp_barrierefrei = $xml->createAttribute("Barrierefrei");
					$objekttyp_barrierefrei->appendChild($xml->createTextNode("true"));
					$objekttyp->appendChild($objekttyp_barrierefrei);
				}
			} // ENDE In: Wohnung, Haus, BueroPraxis

			// In: BueroPraxis, Einzelhandel
			if(strtoupper($property->object_type) == "BUERO_PRAXEN" || strtoupper($property->object_type) == "EINZELHANDEL") {
				if($property->total_area > 0) {
					$objekttyp_nutzflaeche = $xml->createAttribute("Gesamtflaeche");
					$objekttyp_nutzflaeche->appendChild($xml->createTextNode($property->total_area));
					$objekttyp->appendChild($objekttyp_nutzflaeche);
				}

				$anzahl_parkplaetze = $property->parking_space_garage + $property->parking_space_undergroundcarpark + $property->parking_space_simple + $property->parking_space_duplex;
				if($anzahl_parkplaetze > 0) {
					$objekttyp_parkplatz = $xml->createAttribute("AnzahlParkflaechen");
					$objekttyp_parkplatz->appendChild($xml->createTextNode($anzahl_parkplaetze));
					$objekttyp->appendChild($objekttyp_parkplatz);
				}

				$objekttyp_bodenbelag = $xml->createAttribute("Bodenbelag");
				if(in_array("FLIESEN", $property->floor_type)) {
					$objekttyp_bodenbelag->appendChild($xml->createTextNode("Fliesen"));
				}
				else if(in_array("STEIN", $property->floor_type)) {
					$objekttyp_bodenbelag->appendChild($xml->createTextNode("Stein"));
				}
				else if(in_array("TEPPICH", $property->floor_type)) {
					$objekttyp_bodenbelag->appendChild($xml->createTextNode("Teppichboden"));
				}
				else if(in_array("PARKETT", $property->floor_type) || in_array("FERTIGPARKETT", $property->floor_type)) {
					$objekttyp_bodenbelag->appendChild($xml->createTextNode("Parkett"));
				}
				else if(in_array("DIELEN", $property->floor_type)) {
					$objekttyp_bodenbelag->appendChild($xml->createTextNode("Dielen"));
				}
				else if(in_array("KUNSTSTOFF", $property->floor_type)) {
					$objekttyp_bodenbelag->appendChild($xml->createTextNode("PVC"));
				}
				else if(in_array("ESTRICH", $property->floor_type)) {
					$objekttyp_bodenbelag->appendChild($xml->createTextNode("Beton"));
				}
				else if(in_array("LAMINAT", $property->floor_type)) {
					$objekttyp_bodenbelag->appendChild($xml->createTextNode("Laminat"));
				}
				else {
					$objekttyp_bodenbelag->appendChild($xml->createTextNode("keineAngabe"));
				}
				$objekttyp->appendChild($objekttyp_bodenbelag);

				// TODO: <xs:attribute name="FusswegOeNV" type="Zahl2Typ" use="optional"/>
				// TODO: <xs:attribute name="FahrzeitBHf" type="Zahl2Typ" use="optional"/>
				// TODO: <xs:attribute name="FahrzeitBAB" type="Zahl3Typ" use="optional"/>
				// TODO: <xs:attribute name="FahrzeitFlughafen" type="Zahl3Typ" use="optional"/>
				// TODO: <xs:attribute name="Nebenflaeche" type="Zahl102Typ" use="optional"/>

				if(in_array("PERSONEN", $property->elevator)) {
					$objekttyp_aufzug = $xml->createAttribute("Personenaufzug");
					$objekttyp_aufzug->appendChild($xml->createTextNode("true"));
					$objekttyp->appendChild($objekttyp_aufzug);
				}

				// TODO: <xs:attribute name="TeilbarAb" type="Zahl102Typ" use="optional"/>

				// <Vermarktung>
				$vermarktung = $xml->createElement("Vermarktung");
				// <Miete Kaltmiete="45463" Kaution="2 MM" Pro="Monat"/>
				if(strtoupper($property->market_type) == "MIETE_PACHT" || strtoupper($property->market_type) == "LEASING" || strtoupper($property->market_type) == "ERBPACHT") {
					// rkemmere: 20.08.2012 Fix
					//$miete = $xml->createAttribute("Miete");
					$miete = $xml->createElement("Miete");
					$kaltmiete = $xml->createAttribute("Kaltmiete");
					if($property->cold_rent > 0) {
						$kaltmiete->appendChild($xml->createTextNode($property->cold_rent));
					}
					else {
						$kaltmiete->appendChild($xml->createTextNode("1.00"));
					}
					$miete->appendChild($kaltmiete);
					// TODO: <xs:attribute name="Pro" type="MieteEinheitTyp" use="optional" default="Monat"/>
					if(strlen($property->deposit) > 0) {
						$kaution = $xml->createAttribute("Kaution");
						$kaution->appendChild($xml->createTextNode($property->deposit));
						$miete->appendChild($kaution);
					}
					$vermarktung->appendChild($miete);
				}				
				else if(strtoupper($property->market_type) == "KAUF") {
					$kauf = $xml->createElement("Kauf");
					$kaufpreis = $xml->createAttribute("Preis");
					if($property->purchase_price > 0) {
						$kaufpreis->appendChild($xml->createTextNode($property->purchase_price));
					}
					else {
						$kaufpreis->appendChild($xml->createTextNode("1.00"));
					}
					$kauf->appendChild($kaufpreis);

					$vermarktung->appendChild($kauf);
								}
				// </Vermarktung>
				$objekttyp->appendChild($vermarktung);
			} // ENDE In: BueroPraxis, Einzelhandel

			// In: BueroPraxis
			if(strtoupper($property->object_type) == "BUERO_PRAXEN") {
				$objekttyp_bueroflaeche = $xml->createAttribute("BueroPraxisFlaeche");
				$objekttyp_bueroflaeche->appendChild($xml->createTextNode(number_format($property->living_area, 2, ".", "")));
				$objekttyp->appendChild($objekttyp_bueroflaeche);

				// TODO: <xs:attribute name="Objektkategorie2" type="BueroPraxisKategorienTyp" use="optional" default="keineAngabe"/>
				// TODO: <xs:attribute name="DatenVerkabelung" type="DatenVerkabelungsTyp" use="optional" default="keineAngabe"/>
				// TODO: <xs:attribute name="Kantine" type="xs:boolean" use="optional"/>
				// TODO: <xs:attribute name="Klimaanlage" type="JaNeinVereinbarungTyp" use="optional" default="keineAngabe"/>

				if(count($property->kitchen)) {
					$objekttyp_kueche = $xml->createAttribute("KuecheVorhanden");
					$objekttyp_kueche->appendChild($xml->createTextNode("true"));
					$objekttyp->appendChild($objekttyp_kueche);
				}

				// TODO: <xs:attribute name="Starkstrom" type="xs:boolean" use="optional"/>
			} // ENDE In: BueroPraxis

			// In: Haus und Grundstuecke (Pflicht bei letzterem)
			if(strtoupper($property->object_type) == "HAUS" || strtoupper($property->object_type) == "GRUNDSTUECK") {
				$grundstuecksflaeche = $xml->createAttribute("GrundstuecksFlaeche");
				if($property->land_area > 0) {
					$grundstuecksflaeche->appendChild($xml->createTextNode(number_format($property->land_area, 2, ".", "")));
				}
				else {
					$grundstuecksflaeche->appendChild($xml->createTextNode("1.0"));
				}
				$objekttyp->appendChild($grundstuecksflaeche);
			} // ENDE Haus und Grunstuecke

			// In: Einzelhandel
			if(strtoupper($property->object_type) == "EINZELHANDEL") {
				// TODO: <xs:attribute name="Objektkategorie2" type="EinzelhandelKategorienTyp" use="optional" default="keineAngabe"/>

				$objekttyp_verkaufsflaeche = $xml->createAttribute("Verkaufsflaeche");
				$objekttyp_verkaufsflaeche->appendChild($xml->createTextNode(number_format($property->living_area, 2, ".", "")));
				$objekttyp->appendChild($objekttyp_verkaufsflaeche);

				// TODO: <xs:attribute name="Deckenlast" type="Zahl72Typ" use="optional">
				// TODO: <xs:attribute name="Lageart" type="LageartTyp" use="optional"/>
				// TODO: <xs:attribute name="Rampe" type="xs:boolean" use="optional"/>
				// TODO: <xs:attribute name="Zulieferung" type="ZulieferungTyp" use="optional"/>
				// TODO: <xs:attribute name="Schaufensterfront" type="Zahl52Typ" use="optional">
			} // ENDE Einzelhandel

			//In: Grundstuecke
			if(strtoupper($property->object_type) == "GRUNDSTUECK") {
				$objekttyp_kategorie2 = $xml->createAttribute("Objektkategorie2");
				if(strtoupper($property->type_of_use) == "WOHNEN") {
					$objekttyp_kategorie2->appendChild($xml->createTextNode("Wohnen"));
				}
				else if(strtoupper($property->type_of_use) == "GEWERBE") {
					$objekttyp_kategorie2->appendChild($xml->createTextNode("Gewerbe"));
				}
				else {
					$objekttyp_kategorie2->appendChild($xml->createTextNode("keineAngabe"));
				}
				$objekttyp->appendChild($objekttyp_kategorie2);

				// TODO: <xs:attribute name="AbrissErforderlich" type="xs:boolean" use="optional"/>
				// TODO: <xs:attribute name="BaugenehmigungVorhanden" type="xs:boolean" use="optional"/>
				// TODO: <xs:attribute name="BebaubarNach" type="BebaubarNachTyp" use="optional"/>
				// TODO: <xs:attribute name="GRZ" type="Zahl32Typ" use="optional"/>
				// TODO: <xs:attribute name="GFZ" type="Zahl32Typ" use="optional"/>
				// TODO: <xs:attribute name="Erschliessungszustand" type="ErschliessungszustandTyp" use="optional"/>
				// TODO: <xs:attribute name="KurzfristigBebaubar" type="xs:boolean" use="optional"/>

				// <Vermarktung Preis="23445">
				$vermarktung = $xml->createElement("Vermarktung");
				$kaufpreis = $xml->createAttribute("Preis");
				if($property->purchase_price > 0) {
					$kaufpreis->appendChild($xml->createTextNode($property->purchase_price));
				}
				else {
					$kaufpreis->appendChild($xml->createTextNode("1.00"));
				}
				$vermarktung->appendChild($kaufpreis);
				if(strtoupper($property->market_type) == "MIETE_PACHT" || strtoupper($property->market_type) == "LEASING" || strtoupper($property->market_type) == "ERBPACHT") {
					// <Miete/>
					$miete = $xml->createElement("Miete");
					$vermarktung->appendChild($miete);
				}
				else if(strtoupper($property->market_type) == "KAUF") {
					// <Kauf/>
					$kauf = $xml->createElement("Kauf");
					$vermarktung->appendChild($kauf);
				}
				$objekttyp->appendChild($vermarktung);

				// TODO: <xs:element name="BebaubarMit" type="GrundstueckGewerbeEmpfohleneNutzung" minOccurs="0"/>
			}

			// In: GarageMiete, Garagekauf
			if(strtoupper($property->object_type) == "SONSTIGE" && strtoupper($property->objektart_subtyp) == "SONSTIGE") {
				if(strtoupper($property->market_type) == "KAUF") {
					// <Kaufpreise Kaufpreis="24578"/>
					$kaufpreise = $xml->createElement("Kaufpreise");
					// In: WohnungMiete, HausMiete
					$kaufpreis = $xml->createAttribute("Kaufpreis");
					$kaufpreis->appendChild($xml->createTextNode($property->purchase_price));
					$kaufpreise->appendChild($kaufpreis);
					$objekttyp->appendChild($kaufpreise);
				}
				else if(strtoupper($property->market_type) == "MIETE_PACHT" || strtoupper($property->market_type) == "ERBPACHT" || strtoupper($property->market_type) == "LEASING") {
					// <Mietpreise Miete="3456.00"/>
					$mietpreis = $xml->createElement("Mietpreise");
					// In: WohnungMiete, HausMiete
					$miete = $xml->createAttribute("Miete");
					$miete->appendChild($xml->createTextNode($property->cold_rent));
					$mietpreis->appendChild($miete);
					$objekttyp->appendChild($mietpreis);

					if($property->available_from != "") {
						$verfuegbar_ab = $xml->createElement("VerfuegbarAb");
						$verfuegbar_ab->appendChild($xml->createTextNode($property->available_from));
						$objekttyp->appendChild($verfuegbar_ab);
					}
					// TODO VerfuegbarBis="2011-12-31"
				}

				$objekttyp_kategorie2 = $xml->createAttribute("Objektkategorie2");
				$objekttyp_kategorie2->appendChild($xml->createTextNode("Garage"));
				$objekttyp->appendChild($objekttyp_kategorie2);

				$objekttyp_zustand = $xml->createAttribute("Objektzustand");
				if(strtoupper($property->condition_type) == "ERSTBEZUG") {
					$objekttyp_zustand->appendChild($xml->createTextNode("Erstbezug"));
				}
				else if(strtoupper($property->condition_type) == "NEUWERTIG") {
					$objekttyp_zustand->appendChild($xml->createTextNode("Neuwertig"));
				}
				else if(strtoupper($property->condition_type) == "TEIL_VOLLRENOVIERT") {
					$objekttyp_zustand->appendChild($xml->createTextNode("VollstaendigReonviert"));
				}
				else if(strtoupper($property->condition_type) == "TEIL_VOLLRENOVIERUNGSBED") {
					$objekttyp_zustand->appendChild($xml->createTextNode("Renovierungsbeduerftig"));
				}
				else if(strtoupper($property->condition_type) == "NACH_VEREINBARUNG") {
					$objekttyp_zustand->appendChild($xml->createTextNode("NachVereinbarung"));
				}
				else if(strtoupper($property->condition_type) == "GEPFLEGT") {
					$objekttyp_zustand->appendChild($xml->createTextNode("Gepflegt"));
				}
				else if(strtoupper($property->condition_type) == "TEIL_VOLLSANIERT") {
					$objekttyp_zustand->appendChild($xml->createTextNode("Saniert"));
				}
				else {
					$objekttyp_zustand->appendChild($xml->createTextNode("keineAngabe"));
				}
				$objekttyp->appendChild($objekttyp_zustand);

				if($property->construction_year != "") {
					$objekttyp_baujahr = $xml->createAttribute("Baujahr");
					$objekttyp_baujahr->appendChild($xml->createTextNode($property->construction_year));
					$objekttyp->appendChild($objekttyp_baujahr);
				}
				// TODO Breite="4.4"
				// TODO Flaeche="66.45"
				// TODO Hoehe="4.25"
				// TODO JahrLetzteModernisierung="1989"
				// TODO Laenge="15.45"
			}

			$anbieter->appendChild($objekttyp);

			// </WohnungMiete>
			$anbieter->appendChild($objekttyp);
		}

		// </anbieter>
		$is24->appendChild($anbieter);

		// write XML file
		try {
			if($xml->save($this->cache_path . $this->xml_filename) === FALSE) {
				return \rex_i18n::msg('d2u_immo_export_xml_cannot_create');
			}
			else {
				return "";
			}
		}
		catch(Exception $e) {
			return \rex_i18n::msg('d2u_machinery_export_xml_cannot_create') . " - ". $e->getMessage();
		}
	}
}