<?php
/**
 * OpenImmo export class
 */
class OpenImmo extends AFTPExport {
	/**
	 * @var int Maximum number of attachments per property. Don't know how many are allowed.
	 */
	private $max_pics = 15;
	
	/**
	 * @var string Filename of the XML file for this export. EuropeMachinery
	 * does not require a special name.
	 */
	protected $xml_filename = "opim-data.xml";
	
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
	 * Creates a OpenImmo XML file
	 * @return string containing error information, if occured
	 */
	function createXML() {
		// <?xml version="1.0" encoding="UTF-8">
		$xml = new DOMDocument("1.0", "UTF-8");
		$xml->formatOutput = true;
		$openimmo = $xml->createElement("openimmo");
		$xml->appendChild($openimmo);

		// <uebertragung art="" umfang="" modus="" version="" sendersoftware="" techn_email="" />
		$uebertragung = $xml->createElement("uebertragung");
		$uebertragung_art = $xml->createAttribute("art");
		$uebertragung_art->appendChild($xml->createTextNode("ONLINE"));
		$uebertragung->appendChild($uebertragung_art);
		$uebertragung_umfang = $xml->createAttribute("umfang");
		$uebertragung_umfang->appendChild($xml->createTextNode("VOLL"));
		$uebertragung->appendChild($uebertragung_umfang);
		$uebertragung_version = $xml->createAttribute("version");
		$uebertragung_version->appendChild($xml->createTextNode("1.2.7"));
		$uebertragung->appendChild($uebertragung_version);
		$uebertragung_sendersoftware = $xml->createAttribute("sendersoftware");
		$uebertragung_sendersoftware->appendChild($xml->createTextNode("D2U_IMMO"));
		$uebertragung->appendChild($uebertragung_sendersoftware);
		$uebertragung_senderversion = $xml->createAttribute("senderversion");
		$uebertragung_senderversion->appendChild($xml->createTextNode("1.0"));
		$uebertragung->appendChild($uebertragung_senderversion);
		if($this->provider->company_email != "") {
			$uebertragung_techn_email = $xml->createAttribute("techn_email");
			$uebertragung_techn_email->appendChild($xml->createTextNode($this->provider->company_email));
			$uebertragung->appendChild($uebertragung_techn_email);
		}
		$uebertragung_timestamp = $xml->createAttribute("timestamp");
		$uebertragung_timestamp->appendChild($xml->createTextNode(date("Y-m-d\TH:i:s", time())));
		$uebertragung->appendChild($uebertragung_timestamp);
		$openimmo->appendChild($uebertragung);

		// <anbieter>
		$anbieter = $xml->createElement("anbieter");

		// <anbieternr>Nummer</anbieternr>
		$anbieternr = $xml->createElement("anbieternr");
		$anbieternr->appendChild($xml->createTextNode($this->provider->customer_number));
		$anbieter->appendChild($anbieternr);
		// <firma>www.design-to-use.de</firma>
		$firma = $xml->createElement("firma");
		$firma->appendChild($xml->createTextNode($this->provider->company_name));
		$anbieter->appendChild($firma);
		// <openimmo_anid>Lange ID</openimmo_anid>
		$openimmo_anid = $xml->createElement("openimmo_anid");
		$openimmo_anid->appendChild($xml->createTextNode("AD2U20170611000000000design2use"));
		$anbieter->appendChild($openimmo_anid);
		// TODO: <xsd:element ref="lizenzkennung" minOccurs="0"/>

		foreach($this->export_properties as $export_property) {
			if($export_property->export_action == "delete") {
				// Only full export supported. Do not include properties with action "delete"
				continue;
			}
			
			$property = new Property($export_property->property_id, $this->provider->clang_id);
			// <immobilie>
			$immobilie = $xml->createElement("immobilie");

			// <objektkategorie>
			$objektkategorie = $xml->createElement("objektkategorie");
			// <nutzungsart WOHNEN="true" GEWERBE="false"/>
			$nutzungsart = $xml->createElement("nutzungsart");
			$nutzungsarten = ["WOHNEN", "GEWERBE", "ANLAGE", "WAZ"];
			foreach($nutzungsarten as $type_of_use) {
				$nutzungsart_art = $xml->createAttribute($type_of_use);
				if(stristr($property->type_of_use, $type_of_use) != false) {
					$nutzungsart_art->appendChild($xml->createTextNode("true"));
				}
				else {	
					$nutzungsart_art->appendChild($xml->createTextNode("false"));
				}
				$nutzungsart->appendChild($nutzungsart_art);
			}
			$objektkategorie->appendChild($nutzungsart);
			// <vermarktungsart KAUF="1" />
			$vermarktungsart = $xml->createElement("vermarktungsart");
			$vermarktungsarten = ["KAUF", "MIETE_PACHT", "ERBPACHT", "LEASING"];
			foreach($vermarktungsarten as $market_type) {
				$vermarktungsart_art = $xml->createAttribute($market_type);
				if(stristr($property->market_type, $market_type) != false) {
					$vermarktungsart_art->appendChild($xml->createTextNode("true"));
				}
				else {	
					$vermarktungsart_art->appendChild($xml->createTextNode("false"));
				}
				$vermarktungsart->appendChild($vermarktungsart_art);
			}
			$objektkategorie->appendChild($vermarktungsart);
			// <objektart>
			$objektart = $xml->createElement("objektart");
			//<wohnung wohnungtyp="ERDGESCHOSS" />
			$objektart_sub = $xml->createElement(strtolower($property->object_type));
			$objektart_sub_typ = '';
			if(strtolower($property->object_type) == "wohnung") {
				$objektart_sub_typ = $xml->createAttribute("wohnungtyp");
				$objektart_sub_typ->appendChild($xml->createTextNode(strtoupper($property->apartment_type)));
			}
			else if(strtolower($property->object_type) == "haus") {
				$objektart_sub_typ = $xml->createAttribute("haustyp");
				$objektart_sub_typ->appendChild($xml->createTextNode(strtoupper($property->house_type)));
			}
			else if(strtolower($property->object_type) == "grundstueck") {
				$objektart_sub_typ = $xml->createAttribute("grundst_typ");
				$objektart_sub_typ->appendChild($xml->createTextNode(strtoupper($property->land_type)));
			}
			else if(strtolower($property->object_type) == "buero_praxen") {
				$objektart_sub_typ = $xml->createAttribute("buero_typ");
				$objektart_sub_typ->appendChild($xml->createTextNode(strtoupper($property->office_type)));
			}
			else if(strtolower($property->object_type) == "sonstige") {
				$objektart_sub_typ = $xml->createAttribute("sonstige_typ");
				$objektart_sub_typ->appendChild($xml->createTextNode(strtoupper($property->other_type)));
			}
			// TODO: add more if needed, see XSD file
			$objektart_sub->appendChild($objektart_sub_typ);
			$objektart->appendChild($objektart_sub);
			// </objektart>
			$objektkategorie->appendChild($objektart);
			// </objektkategorie>
			$immobilie->appendChild($objektkategorie);

			// <geo>
			$geo = $xml->createElement("geo");
			// <plz>77700</plz>
			$objekt_plz = $xml->createElement("plz");
			$objekt_plz->appendChild($xml->createTextNode($property->zip_code));
			$geo->appendChild($objekt_plz);
			// <ort>Hamburg</ort>
			$objekt_ort = $xml->createElement("ort");
			$objekt_ort->appendChild($xml->createTextNode($property->city));
			$geo->appendChild($objekt_ort);
			// <geokoordinaten breitengrad="" laengengrad=""/>
			if(strlen($property->latitude) > 0 && strlen($property->longitude) > 0) {
				$geokoordinaten = $xml->createElement("geokoordinaten");
				$breitengrad = $xml->createAttribute("breitengrad");
				$breitengrad->appendChild($xml->createTextNode($property->latitude));
				$geokoordinaten->appendChild($breitengrad);
				$laengengrad = $xml->createAttribute("laengengrad");
				$laengengrad->appendChild($xml->createTextNode($property->longitude));
				$geokoordinaten->appendChild($laengengrad);
				$geo->appendChild($geokoordinaten);
			}
			// <strasse>Immostr.</strasse>
			$objekt_strasse = $xml->createElement("strasse");
			$objekt_strasse->appendChild($xml->createTextNode($property->street));
			$geo->appendChild($objekt_strasse);
			// <hausnummer>7</hausnummer>
			$objekt_hausnummer = $xml->createElement("hausnummer");
			$objekt_hausnummer->appendChild($xml->createTextNode($property->house_number));
			$geo->appendChild($objekt_hausnummer);
			// <land iso_land="DEU" />
			$objekt_land = $xml->createElement("land");
			$objekt_land_iso = $xml->createAttribute("iso_land");
			$objekt_land_iso->appendChild($xml->createTextNode($property->country_code));
			$objekt_land->appendChild($objekt_land_iso);
			$geo->appendChild($objekt_land);
			// TODO: <xsd:element ref="gemeindecode" minOccurs="0"/>
			// TODO: <xsd:element ref="flur" minOccurs="0"/>
			// TODO: <xsd:element ref="flurstueck" minOccurs="0"/>
			// TODO: <xsd:element ref="gemarkung" minOccurs="0"/>
			// <etage>0</etage>
			if($property->floor > 0) {
				$objekt_etage = $xml->createElement("etage");
				$objekt_etage->appendChild($xml->createTextNode($property->floor));
				$geo->appendChild($objekt_etage);
			}
			// TODO: <xsd:element ref="anzahl_etagen" minOccurs="0"/>
			// TODO: <xsd:element ref="lage_im_bau" minOccurs="0"/>
			// TODO: <xsd:element ref="wohnungsnr" minOccurs="0"/>
			// TODO: <xsd:element ref="lage_gebiet" minOccurs="0"/>
			// <regionaler_zusatz>Ortsteil Brombach</regionaler_zusatz>
			// $objekt_regionaler_zusatz = $xml->createElement("regionaler_zusatz");
			// $objekt_regionaler_zusatz->appendChild($xml->createTextNode($objekt_regionaler_zusatz_text));
			// $geo->appendChild($objekt_regionaler_zusatz);
			// TODO: <xsd:element name="karten_makro" type="xsd:boolean" minOccurs="0"/>
			// TODO: <xsd:element name="karten_mikro" type="xsd:boolean" minOccurs="0"/>
			// TODO: <xsd:element name="virtuelletour" type="xsd:boolean" minOccurs="0"/>
			// TODO: <xsd:element name="luftbildern" type="xsd:boolean" minOccurs="0"/>
			// TODO: <xsd:element ref="user_defined_simplefield" minOccurs="0" maxOccurs="unbounded"/>
			// TODO: <xsd:element ref="user_defined_anyfield" minOccurs="0" maxOccurs="unbounded"/>
			// TODO: <xsd:element ref="user_defined_extend" minOccurs="0" maxOccurs="unbounded"/>
			// </geo>
			$immobilie->appendChild($geo);

			// <kontaktperson>
			$kontakt = $xml->createElement("kontaktperson");
			// <email_direkt>tk@design-to-use.de</email_direkt>
			$kontakt_email = $xml->createElement("email_direkt");
			$kontakt_email->appendChild($xml->createTextNode($property->contact->email));
			$kontakt->appendChild($kontakt_email);
			// <tel_durchwahl>07621-9161022</tel_durchwahl>
			if(strlen($property->contact->phone) > 0) {
				$kontakt_tel_durchwahl = $xml->createElement("tel_durchw");
				$kontakt_tel_durchwahl->appendChild($xml->createTextNode($property->contact->phone));
				$kontakt->appendChild($kontakt_tel_durchwahl);
			}
			// <tel_fax>07621-9161022</tel_fax>
			if(strlen($property->contact->fax) > 0) {
				$kontakt_fax = $xml->createElement("tel_fax");
				$kontakt_fax->appendChild($xml->createTextNode($property->contact->fax));
				$kontakt->appendChild($kontakt_fax);
			}
			// <tel_handy>07621-9161022</tel_handy>
			if(strlen($property->contact->mobile) > 0) {
				$kontakt_handy = $xml->createElement("tel_handy");
				$kontakt_handy->appendChild($xml->createTextNode($property->contact->mobile));
				$kontakt->appendChild($kontakt_handy);
			}
			// <name>Krais</name>
			$kontakt_name = $xml->createElement("name");
			$kontakt_name->appendChild($xml->createTextNode($property->contact->lastname));
			$kontakt->appendChild($kontakt_name);
			// <vorname>Tobias</vorname>
			$kontakt_vorname = $xml->createElement("vorname");
			$kontakt_vorname->appendChild($xml->createTextNode($property->contact->firstname));
			$kontakt->appendChild($kontakt_vorname);
			// TODO: <xsd:element ref="titel" minOccurs="0"/>
			// TODO: <anrede>Herr</anrede>
			// $kontakt_anrede = $xml->createElement("anrede");
			// if($property->contact->salutation == "0") {
			//	$kontakt_anrede->appendChild($xml->createTextNode($I18N_REXIMMO_EXPORT->msg("herr")));
			// }
			// else if($property->contact->salutation == "1") {
			//	$kontakt_anrede->appendChild($xml->createTextNode($I18N_REXIMMO_EXPORT->msg("frau")));
			// }
			//$kontakt->appendChild($kontakt_anrede);
			// TODO: <xsd:element ref="anrede_brief" minOccurs="0"/>
			// <firma>www.design-to-use.de</firma>
			$kontakt_firma = $xml->createElement("firma");
			$kontakt_firma->appendChild($xml->createTextNode($property->contact->company));
			$kontakt->appendChild($kontakt_firma);
			// TODO: <xsd:element ref="zusatzfeld" minOccurs="0"/>
			// <strasse>Steinsack</strasse>
			$kontakt_strasse = $xml->createElement("strasse");
			$kontakt_strasse->appendChild($xml->createTextNode($property->contact->street));
			$kontakt->appendChild($kontakt_strasse);
			// <hausnummer>10</hausnummer>
			$kontakt_hausnummer = $xml->createElement("hausnummer");
			$kontakt_hausnummer->appendChild($xml->createTextNode($property->contact->house_number));
			$kontakt->appendChild($kontakt_hausnummer);
			// <plz>79541</plz>
			$kontakt_plz = $xml->createElement("plz");
			$kontakt_plz->appendChild($xml->createTextNode($property->contact->zip_code));
			$kontakt->appendChild($kontakt_plz);
			// <ort>Lörrach</ort>
			$kontakt_ort = $xml->createElement("ort");
			$kontakt_ort->appendChild($xml->createTextNode($property->contact->city));
			$kontakt->appendChild($kontakt_ort);
			// TODO: <xsd:element ref="postfach" minOccurs="0"/>
			// TODO: <xsd:element ref="postf_plz" minOccurs="0"/>
			// TODO: <xsd:element ref="postf_ort" minOccurs="0"/>
			// <land iso_land="DEU" />
			$kontakt_land = $xml->createElement("land");
			$kontakt_land_iso = $xml->createAttribute("iso_land");
			$kontakt_land_iso->appendChild($xml->createTextNode($property->contact->country_code));
			$kontakt_land->appendChild($kontakt_land_iso);
			$kontakt->appendChild($kontakt_land);
			// TODO: <xsd:element ref="tel_zentrale"/>
			// TODO: <xsd:element ref="email_zentrale"/>
			// TODO: <xsd:element ref="email_privat" minOccurs="0"/>
			// TODO: <xsd:element ref="email_sonstige" minOccurs="0" maxOccurs="unbounded"/>
			// TODO: <xsd:element ref="tel_privat" minOccurs="0"/>
			// TODO: <xsd:element ref="tel_sonstige" minOccurs="0" maxOccurs="unbounded"/>
			// TODO: <url>http://www.design-to-use.de</url>
			// if($property->contact->url != "") {
			//	$kontakt_url = $xml->createElement("url");
			//	$kontakt_url->appendChild($xml->createTextNode($property->contact->));
			//	$kontakt->appendChild($kontakt_url);
			// }
			// TODO: <xsd:element ref="adressfreigabe" minOccurs="0"/>
			// TODO: <xsd:element ref="personennummer" minOccurs="0"/>
			// TODO: <xsd:element ref="freitextfeld" minOccurs="0"/>
			// TODO: <xsd:element ref="user_defined_simplefield" minOccurs="0" maxOccurs="unbounded"/>
			// TODO: <xsd:element ref="user_defined_anyfield" minOccurs="0" maxOccurs="unbounded"/>
			// TODO: <xsd:element ref="user_defined_extend" minOccurs="0" maxOccurs="unbounded"/>
			// </kontaktperson>
			$immobilie->appendChild($kontakt);

			// TODO:
			/*
			<xsd:element name="weitere_adresse">
			<xsd:complexType>
			<xsd:sequence>
			<xsd:element ref="vorname" minOccurs="0"/>
			<xsd:element ref="name" minOccurs="0"/>
			<xsd:element ref="titel" minOccurs="0"/>
			<xsd:element ref="anrede" minOccurs="0"/>
			<xsd:element ref="anrede_brief" minOccurs="0"/>
			<xsd:element ref="firma" minOccurs="0"/>
			<xsd:element ref="zusatzfeld" minOccurs="0"/>
			<xsd:element ref="strasse" minOccurs="0"/>
			<xsd:element ref="hausnummer" minOccurs="0"/>
			<xsd:element ref="plz" minOccurs="0"/>
			<xsd:element ref="ort" minOccurs="0"/>
			<xsd:element ref="postfach" minOccurs="0"/>
			<xsd:element ref="postf_plz" minOccurs="0"/>
			<xsd:element ref="postf_ort" minOccurs="0"/>
			<xsd:element ref="land" minOccurs="0"/>
			<xsd:element ref="email_zentrale" minOccurs="0"/>
			<xsd:element ref="email_direkt" minOccurs="0"/>
			<xsd:element ref="email_privat" minOccurs="0"/>
			<xsd:element ref="email_sonstige" minOccurs="0" maxOccurs="unbounded"/>
			<xsd:element ref="tel_durchw" minOccurs="0"/>
			<xsd:element ref="tel_zentrale" minOccurs="0"/>
			<xsd:element ref="tel_handy" minOccurs="0"/>
			<xsd:element ref="tel_fax" minOccurs="0"/>
			<xsd:element ref="tel_privat" minOccurs="0"/>
			<xsd:element ref="tel_sonstige" minOccurs="0" maxOccurs="unbounded"/>
			<xsd:element ref="url" minOccurs="0"/>
			<xsd:element ref="adressfreigabe" minOccurs="0"/>
			<xsd:element ref="personennummer" minOccurs="0"/>
			<xsd:element ref="freitextfeld" minOccurs="0"/>
			<xsd:element ref="user_defined_simplefield" minOccurs="0" maxOccurs="unbounded"/>
			<xsd:element ref="user_defined_anyfield" minOccurs="0" maxOccurs="unbounded"/>
			<xsd:element ref="user_defined_extend" minOccurs="0" maxOccurs="unbounded"/>
			</xsd:sequence>
			<xsd:attribute name="adressart" type="xsd:string" use="required">
			<!-- Frei Angabe über die Art der Adresse z.B. Hausmeister, Kontakt -->
			</xsd:attribute>
			</xsd:complexType>
			</xsd:element>
			*/

			// <preise>
			$preise = $xml->createElement("preise");
			// <kaufpreis>100000.00</kaufpreis>
			if($property->purchase_price > 0) {
				$kaufpreis = $xml->createElement("kaufpreis");
				$kaufpreis->appendChild($xml->createTextNode($property->purchase_price));
				$preise->appendChild($kaufpreis);
			}
			// <nettokaltmiete>500.00</nettokaltmiete>
			if($property->cold_rent > 0) {
				$nettokaltmiete = $xml->createElement("nettokaltmiete");
				$nettokaltmiete->appendChild($xml->createTextNode($property->nettokaltmiete));
				$preise->appendChild($nettokaltmiete);
			}
			// TODO: <xsd:element ref="kaltmiete" minOccurs="0"/>
			// <warmmiete>600.00</warmmiete>
			if($property->cold_rent > 0 && $property->additional_costs > 0) {
				$warmmiete = $xml->createElement("warmmiete");
				$warmmiete->appendChild($xml->createTextNode($property->cold_rent + $property->additional_costs));
				$preise->appendChild($warmmiete);
			}
			// <nebenkosten>200.00</nebenkosten>
			if($property->additional_costs > 0) {
				$nebenkosten = $xml->createElement("nebenkosten");
				$nebenkosten->appendChild($xml->createTextNode($property->additional_costs));
				$preise->appendChild($nebenkosten);
			}
			// TODO: <xsd:element ref="heizkosten_enthalten" minOccurs="0"/>
			// TODO: <xsd:element ref="heizkosten" minOccurs="0"/>
			// TODO: <xsd:element ref="zzg_mehrwertsteuer" minOccurs="0"/>
			// TODO: <xsd:element ref="mietzuschlaege" minOccurs="0"/>
			// TODO: <xsd:element ref="pacht" minOccurs="0"/>
			// TODO: <xsd:element ref="erbpacht" minOccurs="0"/>
			// TODO: <hausgeld>20.00</hausgeld>
			// if($property->hausgeld != "" && $property->hausgeld != "0.00") {
			//	$hausgeld = $xml->createElement("hausgeld");
			//	$hausgeld->appendChild($xml->createTextNode($property->hausgeld));
			//	$preise->appendChild($hausgeld);
			// }
			// TODO: <xsd:element ref="abstand" minOccurs="0"/>
			// TODO: <xsd:element ref="preis_zeitraum_von" minOccurs="0"/>
			// TODO: <xsd:element ref="preis_zeitraum_bis" minOccurs="0"/>
			// TODO: <xsd:element ref="preis_zeiteinheit" minOccurs="0"/>
			// TODO: <xsd:element ref="mietpreis_pro_qm" minOccurs="0"/>
			// <kaufpreis_pro_qm>100.99</kaufpreis_pro_qm>
			if($property->purchase_price_m2 > 0) {
				$kaufpreis_pro_qm = $xml->createElement("kaufpreis_pro_qm");
				$kaufpreis_pro_qm->appendChild($xml->createTextNode($property->purchase_price_m2));
				$preise->appendChild($kaufpreis_pro_qm);
			}
			// TODO: <xsd:element ref="innen_courtage" minOccurs="0"/>
			// <aussen_courtage mit_mwst="true">provisionsfrei</aussen_courtage>
			if($property->courtage != "") {
				$aussen_courtage = $xml->createElement("aussen_courtage");
				$aussen_courtage_mwst = $xml->createAttribute("mit_mwst");
				if($property->courtage_incl_vat) {
					$aussen_courtage_mwst->appendChild($xml->createTextNode("true"));
				}
				else {
					$aussen_courtage_mwst->appendChild($xml->createTextNode("false"));
				}
				$aussen_courtage->appendChild($aussen_courtage_mwst);
				$aussen_courtage->appendChild($xml->createTextNode($property->courtage));
				$preise->appendChild($aussen_courtage);
			}
			// <waehrung iso_waehrung="EUR" />
			$waehrung = $xml->createElement("waehrung");
			$waehrung_iso = $xml->createAttribute("iso_waehrung");
			$waehrung_iso->appendChild($xml->createTextNode($property->currency_code));
			$waehrung->appendChild($waehrung_iso);
			$preise->appendChild($waehrung);
			// TODO: <xsd:element ref="mwst_satz" minOccurs="0"/>
			// TODO: <xsd:element ref="freitext_preis" minOccurs="0"/>
			// TODO: <xsd:element ref="x_fache" minOccurs="0"/>
			// TODO: <xsd:element ref="nettorendite" minOccurs="0"/>
			// TODO: <xsd:element ref="mieteinnahmen_ist" minOccurs="0"/>
			// TODO: <xsd:element ref="mieteinnahmen_soll" minOccurs="0"/>
			// TODO: <xsd:element ref="erschliessungskosten" minOccurs="0"/>
			// <kaution>1</kaution>
			if(strlen($property->deposit) > 0) {
				$kaution = $xml->createElement("kaution");
				$kaution->appendChild($xml->createTextNode($property->deposit));
				$preise->appendChild($kaution);
			}
			// TODO: <xsd:element ref="geschaeftsguthaben" minOccurs="0"/>
			// TODO: <xsd:element ref="stp_carport" minOccurs="0"/>
			// <stp_duplex stellplatzmiete="1" stellplatzkaufpreis="1" anzahl="1"/>
			if($property->parking_space_duplex > 0) {
				$stp_duplex = $xml->createElement("stp_duplex");
				$stp_duplex_anzahl = $xml->createAttribute("anzahl");
				$stp_duplex_anzahl->appendChild($xml->createTextNode($property->parking_space_duplex));
				$stp_duplex->appendChild($stp_duplex_anzahl);
				$preise->appendChild($stp_duplex);
			}
			// <stp_freiplatz stellplatzmiete="1" stellplatzkaufpreis="1" anzahl="1"/>
			if($property->parking_space_simple > 0) {
				$stp_freiplatz = $xml->createElement("stp_freiplatz");
				$stp_freiplatz_anzahl = $xml->createAttribute("anzahl");
				$stp_freiplatz_anzahl->appendChild($xml->createTextNode($property->parking_space_simple));
				$stp_freiplatz->appendChild($stp_freiplatz_anzahl);
				$preise->appendChild($stp_freiplatz);
			}
			// <stp_garage stellplatzmiete="1" stellplatzkaufpreis="1" anzahl="1"/>
			if($property->parking_space_garage > 0) {
				$stp_garage = $xml->createElement("stp_garage");
				$stp_garage_anzahl = $xml->createAttribute("anzahl");
				$stp_garage_anzahl->appendChild($xml->createTextNode($property->parking_space_garage));
				$stp_garage->appendChild($stp_garage_anzahl);
				$preise->appendChild($stp_garage);
			}
			// TODO: <xsd:element ref="stp_parkhaus" minOccurs="0"/>
			// <stp_tiefgarage stellplatzmiete="1" stellplatzkaufpreis="1" anzahl="1"/>
			if($property->parking_space_undergroundcarpark > 0) {
				$stp_tiefgarage = $xml->createElement("stp_tiefgarage");
				$stp_tiefgarage_anzahl = $xml->createAttribute("anzahl");
				$stp_tiefgarage_anzahl->appendChild($xml->createTextNode($property->parking_space_undergroundcarpark));
				$stp_tiefgarage->appendChild($stp_tiefgarage_anzahl);
				$preise->appendChild($stp_tiefgarage);
			}
			// TODO: <xsd:element ref="stp_sonstige" minOccurs="0" maxOccurs="unbounded"/>
			// TODO: <xsd:element ref="user_defined_simplefield" minOccurs="0" maxOccurs="unbounded"/>
			// TODO: <xsd:element ref="user_defined_anyfield" minOccurs="0" maxOccurs="unbounded"/>
			// TODO: <xsd:element ref="user_defined_extend" minOccurs="0" maxOccurs="unbounded"/>
			// </preise>
			$immobilie->appendChild($preise);

			// TODO:
			/*
			<xsd:element name="bieterverfahren">
			<xsd:annotation>
			<xsd:documentation>Angaben zum Bieterverfahren  ($V12) </xsd:documentation>
			</xsd:annotation>
			<xsd:complexType>
			<xsd:sequence>
			<xsd:element name="beginn_angebotsphase" type="xsd:date" minOccurs="0"/>
			<xsd:element name="besichtigungstermin" type="xsd:date" minOccurs="0"/>
			<xsd:element name="besichtigungstermin_2" type="xsd:date" minOccurs="0"/>
			<xsd:element name="beginn_bietzeit" type="xsd:dateTime" minOccurs="0"/>
			<xsd:element name="ende_bietzeit" type="xsd:dateTime" minOccurs="0"/>
			<xsd:element name="hoechstgebot_zeigen" type="xsd:boolean" minOccurs="0"/>
			<xsd:element name="mindestpreis" type="xsd:decimal" minOccurs="0"/>
			<xsd:element ref="user_defined_simplefield" minOccurs="0" maxOccurs="unbounded"/>
			<xsd:element ref="user_defined_anyfield" minOccurs="0" maxOccurs="unbounded"/>
			<xsd:element ref="user_defined_extend" minOccurs="0" maxOccurs="unbounded"/>
			</xsd:sequence>
			</xsd:complexType>
			</xsd:element>
			*/

			// <flaechen>
			$flaechen = $xml->createElement("flaechen");
			// <wohnflaeche>52.08</wohnflaeche>
			if($property->living_area > 0) {
				$wohnflaeche = $xml->createElement("wohnflaeche");
				$wohnflaeche->appendChild($xml->createTextNode($property->living_area));
				$flaechen->appendChild($wohnflaeche);
			}
			// TODO: <xsd:element ref="nutzflaeche" minOccurs="0"/>
			// Nutzflaeche = Gesamtflaeche - Wohnflaeche (Keller, Nebenraume)
			if($property->total_area > 0 && $property->living_area > 0) {
				$nutzflache_qm = $property->total_area - $property->living_area;
				$nutzflaeche = $xml->createElement("nutzflaeche");
				$nutzflaeche->appendChild($xml->createTextNode(number_format($nutzflache_qm, 2, ".", "")));
				$flaechen->appendChild($nutzflaeche);
			}
			// <gesamtflaeche>100.99</gesamtflaeche>
			if($property->total_area > 0) {
				$gesamtflaeche = $xml->createElement("gesamtflaeche");
				$gesamtflaeche->appendChild($xml->createTextNode($property->total_area));
				$flaechen->appendChild($gesamtflaeche);
			}
			// TODO: <xsd:element ref="ladenflaeche" minOccurs="0"/>
			// TODO: <xsd:element ref="lagerflaeche" minOccurs="0"/>
			// TODO: <xsd:element ref="verkaufsflaeche" minOccurs="0"/>
			// TODO: <xsd:element ref="freiflaeche" minOccurs="0"/>
			// TODO: <xsd:element ref="bueroflaeche" minOccurs="0"/>
			// TODO: <xsd:element ref="bueroteilflaeche" minOccurs="0"/>
			// TODO: <xsd:element ref="fensterfront" minOccurs="0"/>
			// TODO: <xsd:element ref="verwaltungsflaeche" minOccurs="0"/>
			// TODO: <xsd:element ref="gastroflaeche" minOccurs="0"/>
			// TODO: <xsd:element ref="grz" minOccurs="0"/>
			// TODO: <xsd:element ref="gfz" minOccurs="0"/>
			// TODO: <xsd:element ref="bmz" minOccurs="0"/>
			// TODO: <xsd:element ref="bgf" minOccurs="0"/>
			// <grundstuecksflaeche>100.99</grundstuecksflaeche>
			if($property->land_area > 0) {
				$grundstuecksflaeche = $xml->createElement("grundstuecksflaeche");
				$grundstuecksflaeche->appendChild($xml->createTextNode($property->land_area));
				$flaechen->appendChild($grundstuecksflaeche);
			}
			// TODO: <xsd:element ref="sonstflaeche" minOccurs="0"/>
			// <anzahl_zimmer>5</anzahl_zimmer>
			if($property->rooms > 0) {
				$anzahl_zimmer = $xml->createElement("anzahl_zimmer");
				$anzahl_zimmer->appendChild($xml->createTextNode($property->rooms));
				$flaechen->appendChild($anzahl_zimmer);
			}
			// TODO: <xsd:element ref="anzahl_schlafzimmer" minOccurs="0"/>
			// TODO: <xsd:element ref="anzahl_badezimmer" minOccurs="0"/>
			// TODO: <xsd:element ref="anzahl_sep_wc" minOccurs="0"/>
			// TODO: <xsd:element ref="anzahl_balkon_terrassen" minOccurs="0"/>
			// TODO: <xsd:element ref="balkon_terrasse_flaeche" minOccurs="0"/>
			// TODO: <xsd:element ref="anzahl_wohn_schlafzimmer" minOccurs="0"/>
			// TODO: <xsd:element ref="gartenflaeche" minOccurs="0"/>
			// TODO: <xsd:element ref="kellerflaeche" minOccurs="0"/>
			// TODO: <xsd:element ref="fensterfront_qm" minOccurs="0"/>
			// TODO: <xsd:element ref="grundstuecksfront" minOccurs="0"/>
			// TODO: <xsd:element ref="dachbodenflaeche" minOccurs="0"/>
			// TODO: <xsd:element ref="teilbar_ab" minOccurs="0"/>
			// TODO: <xsd:element ref="beheizbare_flaeche" minOccurs="0"/>
			// TODO: <xsd:element ref="anzahl_stellplaetze" minOccurs="0"/>
			// TODO: <xsd:element ref="plaetze_gastraum" minOccurs="0"/>
			// TODO: <xsd:element ref="anzahl_betten" minOccurs="0"/>
			// TODO: <xsd:element ref="anzahl_tagungsraeume" minOccurs="0"/>
			// TODO: <xsd:element ref="vermietbare_flaeche" minOccurs="0"/>
			// TODO: <xsd:element ref="anzahl_wohneinheiten" minOccurs="0"/>
			// TODO: <xsd:element ref="anzahl_gewerbeeinheiten" minOccurs="0"/>
			// TODO: <xsd:element ref="einliegerwohnung" minOccurs="0"/>
			// TODO: <xsd:element ref="user_defined_simplefield" minOccurs="0" maxOccurs="unbounded"/>
			// TODO: <xsd:element ref="user_defined_anyfield" minOccurs="0" maxOccurs="unbounded"/>
			// TODO: <xsd:element ref="user_defined_extend" minOccurs="0" maxOccurs="unbounded"/>
			// </flaechen>
			$immobilie->appendChild($flaechen);

			// <ausstattung>
			$ausstattung = $xml->createElement("ausstattung");
			// <wg_geeignet>true</wg_geeignet>
			if($property->flat_sharing_possible) {
				$wg_geeignet = $xml->createElement("wg_geeignet");
				$wg_geeignet->appendChild($xml->createTextNode("true"));
				$ausstattung->appendChild($wg_geeignet);
			}

			// TODO: <xsd:element ref="raeume_veraenderbar" minOccurs="0"/>
			//<bad DUSCHE="true" />
			if(count($property->bath) > 0) {
				$bad = $xml->createElement("bad");
				foreach($property->bath as $bath_option) {
					$option = $xml->createAttribute(strtoupper(trim($bath_option)));
					$option->appendChild($xml->createTextNode("true"));
					$bad->appendChild($option);
				}
				$ausstattung->appendChild($bad);
			}
			//<kueche EBK="true" />
			if(count($property->kitchen) > 0) {
				$kueche = $xml->createElement("kueche");
				foreach($property->kitchen as $kitchen_option) {
					$option = $xml->createAttribute(strtoupper(trim($kitchen_option)));
					$option->appendChild($xml->createTextNode("true"));
					$kueche->appendChild($option);
				}
				$ausstattung->appendChild($kueche);
			}
			//<boden STEIN="true" TEPPICH="false" />
			if(count($property->floor_type) > 0) {
				$boden = $xml->createElement("boden");
				foreach($property->floor_type as $floor_option) {
					$option = $xml->createAttribute(strtoupper(trim($floor_option)));
					$option->appendChild($xml->createTextNode("true"));
					$boden->appendChild($option);
				}
				$ausstattung->appendChild($boden);
			}
			// TODO: <xsd:element ref="kamin" minOccurs="0"/>
			//<heizungsart ZENTRAL="true" />
			if(count($property->heating_type) > 0) {
				$heizungsart = $xml->createElement("heizungsart");
				foreach($property->heating_type as $heating_option) {
					$option = $xml->createAttribute(strtoupper(trim($heating_option)));
					$option->appendChild($xml->createTextNode("true"));
					$heizungsart->appendChild($option);
				}
				$ausstattung->appendChild($heizungsart);
			}
			//<befeuerung OEL="true" />
			if(count($property->firing_type) > 0) {
				$befeuerungsart = $xml->createElement("befeuerung");
				foreach($property->firing_type as $firing_option) {
					$option = $xml->createAttribute(strtoupper(trim($firing_option)));
					$option->appendChild($xml->createTextNode("true"));
					$befeuerungsart->appendChild($option);
				}
				$ausstattung->appendChild($befeuerungsart);
			}
			// TODO: <xsd:element ref="klimatisiert" minOccurs="0"/>
			//<fahrstuhl ZENTRAL="true" />
			if(count($property->elevator) > 0) {
				$fahrstuhl = $xml->createElement("fahrstuhl");
				foreach($property->elevator as $elevator_option) {
					$option = $xml->createAttribute(strtoupper(trim($elevator_option)));
					$option->appendChild($xml->createTextNode("true"));
					$fahrstuhl->appendChild($option);
				}
				$ausstattung->appendChild($fahrstuhl);
			}
			//<stellplatzart GARAGE="true" />
			if($property->parking_space_duplex > 0 || $property->parking_space_simple > 0 ||
					$property->parking_space_garage > 0 || $property->parking_space_undergroundcarpark > 0) {
				$stellplatzart = $xml->createElement("stellplatzart");
				if($property->parking_space_duplex > 0) {
					$option = $xml->createAttribute("DUPLEX");
					$option->appendChild($xml->createTextNode("true"));
					$stellplatzart->appendChild($option);				
				}
				if($property->parking_space_simple > 0) {
					$option = $xml->createAttribute("FREIPLATZ");
					$option->appendChild($xml->createTextNode("true"));
					$stellplatzart->appendChild($option);				
				}
				if($property->parking_space_garage > 0) {
					$option = $xml->createAttribute("GARAGE");
					$option->appendChild($xml->createTextNode("true"));
					$stellplatzart->appendChild($option);				
				}
				if($property->parking_space_undergroundcarpark > 0) {
					$option = $xml->createAttribute("TIEFGARAGE");
					$option->appendChild($xml->createTextNode("true"));
					$stellplatzart->appendChild($option);				
				}
				$ausstattung->appendChild($stellplatzart);
			}
			// TODO: <xsd:element ref="gartennutzung" minOccurs="0"/>
			// TODO: <xsd:element ref="ausricht_balkon_terrasse" minOccurs="0"/>
			// TODO: <xsd:element ref="moebliert" minOccurs="0"/>
			// <rollstuhlgerecht>true</rollstuhlgerecht>
			$rollstuhlgerecht = $xml->createElement("rollstuhlgerecht");
			if($property->wheelchair_accessable) {
				$rollstuhlgerecht->appendChild($xml->createTextNode("true"));
			}
			else {
				$rollstuhlgerecht->appendChild($xml->createTextNode("false"));
			}
			$ausstattung->appendChild($rollstuhlgerecht);		

			// <kabel_sat_tv>true</kabel_sat_tv>
			$kabel_sat_tv = $xml->createElement("kabel_sat_tv");
			if($property->cable_sat_tv) {
				$kabel_sat_tv->appendChild($xml->createTextNode("true"));
			}
			else {
				$kabel_sat_tv->appendChild($xml->createTextNode("false"));
			}
			$ausstattung->appendChild($kabel_sat_tv);
			// TODO: <xsd:element ref="dvbt" minOccurs="0"/>
			// TODO: <xsd:element ref="barrierefrei" minOccurs="0"/>
			// TODO: <xsd:element ref="sauna" minOccurs="0"/>
			// TODO: <xsd:element ref="swimmingpool" minOccurs="0"/>
			// TODO: <xsd:element ref="wasch_trockenraum" minOccurs="0"/>
			// TODO: <xsd:element ref="dv_verkabelung" minOccurs="0"/>
			// TODO: <xsd:element ref="rampe" minOccurs="0"/>
			// TODO: <xsd:element ref="hebebuehne" minOccurs="0"/>
			// TODO: <xsd:element ref="kran" minOccurs="0"/>
			// TODO: <xsd:element ref="gastterrasse" minOccurs="0"/>
			// TODO: <xsd:element ref="stromanschlusswert" minOccurs="0"/>
			// TODO: <xsd:element ref="kantine_cafeteria" minOccurs="0"/>
			// TODO: <xsd:element ref="teekueche" minOccurs="0"/>
			// TODO: <xsd:element ref="hallenhoehe" minOccurs="0"/>
			// TODO: <xsd:element ref="angeschl_gastronomie" minOccurs="0"/>
			// TODO: <xsd:element ref="brauereibindung" minOccurs="0"/>
			// TODO: <xsd:element ref="sporteinrichtungen" minOccurs="0"/>
			// TODO: <xsd:element ref="wellnessbereich" minOccurs="0"/>
			//<serviceleistungen BETREUTES_WOHNEN="true" />
			// if($property->serviceleistungen != "") {
			// 	$serviceleistungen = $xml->createElement("serviceleistungen");
			//	foreach($property->serviceleistungen as $serviceleistungen_option) {
			//		$option = $xml->createAttribute(strtoupper(trim($serviceleistungen_option)));
			//		$option->appendChild($xml->createTextNode("true"));
			//		$serviceleistungen->appendChild($option);
			//	}
			//	$ausstattung->appendChild($serviceleistungen);
			// }
			// TODO: <xsd:element ref="telefon_ferienimmobilie" minOccurs="0"/>
			//<breitband_zugang DSL="true" />
			if($property->broadband_internet != "") {
				$breitband_zugang = $xml->createElement("breitband_zugang");
				foreach($property->broadband_internet as $broadband_option) {
					$option = $xml->createAttribute(strtoupper(trim($broadband_option)));
					$option->appendChild($xml->createTextNode("true"));
					$breitband_zugang->appendChild($option);
				}
				$ausstattung->appendChild($breitband_zugang);
			}
			// TODO: <xsd:element ref="umts_empfang" minOccurs="0"/>
			// TODO: <xsd:element ref="sicherheitstechnik" minOccurs="0"/>
			// TODO: <xsd:element ref="unterkellert" minOccurs="0"/>
			// TODO: <xsd:element name="abstellraum" type="xsd:boolean" minOccurs="0"/>
			// TODO: <xsd:element name="fahrradraum" type="xsd:boolean" minOccurs="0"/>
			// TODO: <xsd:element name="rolladen" type="xsd:boolean" minOccurs="0"/>
			// TODO: <xsd:element ref="user_defined_simplefield" minOccurs="0" maxOccurs="unbounded"/>
			// TODO: <xsd:element ref="user_defined_anyfield" minOccurs="0" maxOccurs="unbounded"/>
			// TODO: <xsd:element ref="user_defined_extend" minOccurs="0" maxOccurs="unbounded"/>
			// </ausstattung>
			$immobilie->appendChild($ausstattung);

			// <zustand_angaben>
			$zustand_angaben = $xml->createElement("zustand_angaben");
			// <baujahr>1970</baujahr>
			$baujahr = $xml->createElement("baujahr");
			$baujahr->appendChild($xml->createTextNode($property->construction_year));
			$zustand_angaben->appendChild($baujahr);
			// <zustand zustand_art="BAUFAELLIG" />
			if($property->condition_type != "") {
				$zustand = $xml->createElement("zustand");
				$zustand_art = $xml->createAttribute("zustand_art");
				$zustand_art->appendChild($xml->createTextNode($property->condition_type));
				$zustand->appendChild($zustand_art);
				$zustand_angaben->appendChild($zustand);
			}
			// <alter alter_attr="NEUBAU" />
			if($property->construction_year > 0) {
				$alter = $xml->createElement("alter");
				$alter_attr = $xml->createAttribute("alter_attr");
				if($property->construction_year > 1945) {
					$alter_attr->appendChild($xml->createTextNode("NEUBAU"));
				}
				else {
					$alter_attr->appendChild($xml->createTextNode("ALTBAU"));
				}
				$alter->appendChild($alter_attr);
				$zustand_angaben->appendChild($alter);
			}
			// TODO: <xsd:element ref="bebaubar_nach" minOccurs="0"/>
			// TODO: <xsd:element ref="erschliessung" minOccurs="0"/>
			// TODO: <xsd:element ref="altlasten" minOccurs="0"/>
			// <energiepass>
			if(strlen($property->energy_pass) > 1) {
				$energiepass = $xml->createElement("energiepass");
				// <epart>BEDARF</epart>
				$energiepass_art = $xml->createElement("epart");
				$energiepass_art->appendChild($xml->createTextNode($property->energy_pass));
				$energiepass->appendChild($energiepass_art);
				// <gueltig_bis>2010-12-31</gueltig_bis>
				$energiepass_gueltig_bis = $xml->createElement("gueltig_bis");
				if($property->energy_pass_valid_until != "") {
					$energiepass_gueltig_bis->appendChild($xml->createTextNode($property->energy_pass_valid_until));
				}
				else {
					$energiepass_gueltig_bis->appendChild($xml->createTextNode("Wert fehlt."));
				}
				$energiepass->appendChild($energiepass_gueltig_bis);
				// <energieverbrauchkennwert>97</energieverbrauchkennwert>
				$energiepass_kennwert = $xml->createElement("energieverbrauchkennwert");
				if($property->energy_pass == "BEDARF") {
					$energiepass_kennwert = $xml->createElement("endenergiebedarf");
				}
				$energiepass_kennwert->appendChild($xml->createTextNode($property->energy_consumption));
				$energiepass->appendChild($energiepass_kennwert);
				// <mitwarmwasser>1</mitwarmwasser>
				$energiepass_mitwarmwasser = $xml->createElement("mitwarmwasser");
				if($property->including_warm_water) {
					$energiepass_mitwarmwasser->appendChild($xml->createTextNode("true"));
				}
				else {
					$energiepass_mitwarmwasser->appendChild($xml->createTextNode("false"));
				}
				$energiepass->appendChild($energiepass_mitwarmwasser);
				// <primaerenergietraeger>GAS</primaerenergietraeger>
				$energiepass_primaerenergietraeger = $xml->createElement("primaerenergietraeger");
				$energiepass_primaerenergietraeger->appendChild($xml->createTextNode(implode(" ", $property->firing_type)));
				$energiepass->appendChild($energiepass_primaerenergietraeger);
				// </energiepass>
				$zustand_angaben->appendChild($energiepass);
			}
			// TODO: <xsd:element ref="user_defined_simplefield" minOccurs="0" maxOccurs="unbounded"/>
			// TODO: <xsd:element ref="user_defined_anyfield" minOccurs="0" maxOccurs="unbounded"/>
			// TODO: <xsd:element ref="user_defined_extend" minOccurs="0" maxOccurs="unbounded"/>
			// </zustand_angaben>
			$immobilie->appendChild($zustand_angaben);

			// TODO:
			/*
			<xsd:element name="bewertung">
			<xsd:annotation>
			<xsd:documentation>
			Container für detailierte Bewertungs Parmater ($V12)
			</xsd:documentation>
			</xsd:annotation>
			<xsd:complexType>
			<xsd:sequence>
			<xsd:element name="feld" minOccurs="0" maxOccurs="unbounded">
			<xsd:complexType>
			<xsd:sequence>
			<xsd:element name="name" type="xsd:string"/>
			<xsd:element name="wert" type="xsd:string"/>
			<xsd:element name="typ" type="xsd:string" minOccurs="0" maxOccurs="unbounded"/>
			<xsd:element name="modus" type="xsd:string" minOccurs="0" maxOccurs="unbounded"/>
			</xsd:sequence>
			</xsd:complexType>
			</xsd:element>
			</xsd:sequence>
			</xsd:complexType>
			</xsd:element>

			<xsd:element name="infrastruktur">
			<xsd:complexType>
			<xsd:sequence>
			<xsd:element ref="zulieferung" minOccurs="0"/>
			<xsd:element ref="ausblick" minOccurs="0"/>
			<xsd:element ref="distanzen" minOccurs="0" maxOccurs="unbounded"/>
			<xsd:element ref="distanzen_sport" minOccurs="0" maxOccurs="unbounded"/>
			<xsd:element ref="user_defined_simplefield" minOccurs="0" maxOccurs="unbounded"/>
			<xsd:element ref="user_defined_anyfield" minOccurs="0" maxOccurs="unbounded"/>
			<xsd:element ref="user_defined_extend" minOccurs="0" maxOccurs="unbounded"/>
			</xsd:sequence>
			</xsd:complexType>
			</xsd:element>
			*/
			// <freitexte>
			$freitexte = $xml->createElement("freitexte");
			// <objekttitel>Überschrift</objekttitel>
			$objekttitel = $xml->createElement("objekttitel");
			$objekttitel->appendChild($xml->createTextNode($property->name));
			$freitexte->appendChild($objekttitel);
			// <dreizeiler>max. dreizeilige Kurzbeschreibung</dreizeiler>
			$dreizeiler = $xml->createElement("dreizeiler");
			$dreizeiler->appendChild($xml->createTextNode($property->teaser));
			$freitexte->appendChild($dreizeiler);
			// <lage>Traumhafte Lage</lage>
			if($property->description_location != "") {
				$lage = $xml->createElement("lage");
				$lage->appendChild($xml->createTextNode(strip_tags($property->description_location)));
				$freitexte->appendChild($lage);
			}
			// <ausstatt_beschr>Beschreibung Austattung</ausstatt_beschr>
			if($property->description_equipment != "") {
				$ausstatt_beschr = $xml->createElement("ausstatt_beschr");
				$ausstatt_beschr->appendChild($xml->createTextNode(strip_tags($property->description_equipment)));
				$freitexte->appendChild($ausstatt_beschr);
			}
			// <objektbeschreibung>Objektbeschreibung</objektbeschreibung>
			$objektbeschreibung = $xml->createElement("objektbeschreibung");
			$objektbeschreibung->appendChild($xml->createTextNode(strip_tags($property->description)));
			$freitexte->appendChild($objektbeschreibung);
			// <sonstige_angaben>Sonstige Angaben</sonstige_angaben>
			if($property->description_others != "") {
				$sonstige_angaben = $xml->createElement("sonstige_angaben");
				$sonstige_angaben->appendChild($xml->createTextNode(strip_tags($property->description_others)));
				$freitexte->appendChild($sonstige_angaben);
			}
			// TODO: <xsd:element ref="user_defined_simplefield" minOccurs="0" maxOccurs="unbounded"/>
			// TODO: <xsd:element ref="user_defined_anyfield" minOccurs="0" maxOccurs="unbounded"/>
			// </freitexte>
			$immobilie->appendChild($freitexte);
			
			// <anhaenge>
			$anhaenge = $xml->createElement("anhaenge");
			$objekt_anhaenge = [];
			// Titelbild auslesen
			$is_title_pic = TRUE;
			$zaehler = 0;
			foreach($property->pictures as $bild) {
				if(strlen($bild) > 3) {
					$objekt_anhaenge[$zaehler] = ["TITELBILD" => $bild];
					$zaehler++;
					break;
				}
			}
			// Grundrisse auslesen
			foreach($property->ground_plans as $grundriss) {
				if(strlen($grundriss) > 3 && $zaehler < $this->max_pics) {
					$objekt_anhaenge[$zaehler] = ["GRUNDRISS" => $grundriss];
					$zaehler++;
				}
			}
			// Bilder auslesen
			foreach($property->pictures as $bild) {
				if($is_title_pic) {
					$is_title_pic = FALSE;
					continue;
				}
				else if(strlen($bild) > 3 && $zaehler < $this->max_pics) {
					$objekt_anhaenge[$zaehler] = ["BILD" => $bild];
					$zaehler++;
				}
			}
			// Lageplaene auslesen
			foreach($property->location_plans as $lageplan) {
				if(strlen($lageplan) > 3 && $zaehler < $this->max_pics) {
					$objekt_anhaenge[$zaehler] = ["KARTEN_LAGEPLAN" => $lageplan];
					$zaehler++;
				}
			}
			// Dokumente auslesen
			foreach($property->documents as $dokument) {
				if(strlen($dokument) > 3 && $zaehler < $this->max_pics) {
					$objekt_anhaenge[$zaehler] = ["DOKUMENTE" => $dokument];
					$zaehler++;
				}
			}

			foreach($objekt_anhaenge as $objekt_anhang) {
				foreach($objekt_anhang as $media_type => $filename) {
					$anhang_media = rex_media::get($filename);
					// Pruefen, ob Datei in Datenbank existiert
					if($anhang_media instanceof rex_media) {
						//<anhang location="EXTERN" gruppe="BILD">
						$anhang = $xml->createElement("anhang");
						$anhang_location = $xml->createAttribute("location");
						$anhang_location->appendChild($xml->createTextNode("EXTERN"));
						$anhang->appendChild($anhang_location);
						$anhang_gruppe = $xml->createAttribute("gruppe");
						if(strpos($anhang_media->getType(), "image") !== FALSE && $media_type == "DOKUMENTE") {
							// Falls Bilder im Dokumentenbereich eingefuegt wurden
							$anhang_gruppe->appendChild($xml->createTextNode("BILD"));
						}
						else {
							$anhang_gruppe->appendChild($xml->createTextNode($media_type));
						}
						$anhang->appendChild($anhang_gruppe);
						// <anhangtitel>Ohne Titel</anhangtitel>
						$anhangtitel = $xml->createElement("anhangtitel");
						$anhangtitel->appendChild($xml->createTextNode($anhang_media->getTitle()));
						$anhang->appendChild($anhangtitel);
						// <format>image/jpeg</format>
						$anhangformat = $xml->createElement("format");
						$anhangformat->appendChild($xml->createTextNode($anhang_media->getType()));
						$anhang->appendChild($anhangformat);
						// <daten>
						$anhangdaten = $xml->createElement("daten");
						// <pfad>Ge-4436-3-c.jpg</pfad>
						$anhangpfad = $xml->createElement("pfad");
						$anhangpfad->appendChild($xml->createTextNode(trim($filename)));
						$anhangdaten->appendChild($anhangpfad);
						// </daten>
						$anhang->appendChild($anhangdaten);
						// </anhang>
						$anhaenge->appendChild($anhang);
					}
				}
			}
			// TODO: <xsd:element ref="user_defined_simplefield" minOccurs="0" maxOccurs="unbounded"/>
			// TODO: <xsd:element ref="user_defined_anyfield" minOccurs="0" maxOccurs="unbounded"/>
			// </anhaenge>
			$immobilie->appendChild($anhaenge);

			// <verwaltung_objekt>
			$verwaltung_objekt = $xml->createElement("verwaltung_objekt");
			// <objektadresse_freigeben>true</objektadresse_freigeben>
			$objektadresse_freigeben = $xml->createElement("objektadresse_freigeben");
			if($property->publish_address) {
				$objektadresse_freigeben->appendChild($xml->createTextNode("true"));
			}
			else {
				$objektadresse_freigeben->appendChild($xml->createTextNode("false"));
			}
			$verwaltung_objekt->appendChild($objektadresse_freigeben);
			// <verfuegbar_ab>2010-01-26</verfuegbar_ab>
			if($property->available_from != "") {
				$verfuegbar_ab = $xml->createElement("verfuegbar_ab");
				$verfuegbar_ab->appendChild($xml->createTextNode($property->available_from));
				$verwaltung_objekt->appendChild($verfuegbar_ab);
			}
			// TODO: <xsd:element ref="abdatum" minOccurs="0"/>
			// TODO: <xsd:element ref="bisdatum" minOccurs="0"/>
			// TODO: <xsd:element ref="min_mietdauer" minOccurs="0"/>
			// TODO: <xsd:element ref="max_mietdauer" minOccurs="0"/>
			// TODO: <xsd:element ref="versteigerungstermin" minOccurs="0"/>
			// TODO: <xsd:element ref="wbs_sozialwohnung" minOccurs="0"/>
			// <vermietet>true</vermietet>
			$vermietet = $xml->createElement("vermietet");
			if($property->rented) {
				$vermietet->appendChild($xml->createTextNode("true"));
			}
			else {
				$vermietet->appendChild($xml->createTextNode("false"));
			}
			$verwaltung_objekt->appendChild($vermietet);
			// TODO: <xsd:element ref="gruppennummer" minOccurs="0"/>
			// TODO: <xsd:element ref="zugang" minOccurs="0"/>
			// TODO: <xsd:element ref="laufzeit" minOccurs="0"/>
			// TODO: <xsd:element ref="max_personen" minOccurs="0"/>
			// TODO: <xsd:element ref="nichtraucher" minOccurs="0"/>
			// <haustiere>true</haustiere>
			if($property->animals) {
				$haustiere = $xml->createElement("haustiere");
				$haustiere->appendChild($xml->createTextNode("true"));
				$verwaltung_objekt->appendChild($haustiere);
			}
			// TODO: <xsd:element ref="geschlecht" minOccurs="0"/>
			// TODO: <xsd:element ref="denkmalgeschuetzt" minOccurs="0"/>
			// TODO: <xsd:element ref="als_ferien" minOccurs="0"/>
			// TODO: <xsd:element ref="gewerbliche_nutzung" minOccurs="0"/>
			// TODO: <xsd:element ref="branchen" minOccurs="0"/>
			// TODO: <xsd:element ref="hochhaus" minOccurs="0"/>
			// TODO: <xsd:element ref="user_defined_simplefield" minOccurs="0" maxOccurs="unbounded"/>
			// TODO: <xsd:element ref="user_defined_anyfield" minOccurs="0" maxOccurs="unbounded"/>
			// TODO: <xsd:element ref="user_defined_extend" minOccurs="0" maxOccurs="unbounded"/>
			// </verwaltung_objekt>
			$immobilie->appendChild($verwaltung_objekt);

			// <verwaltung_techn>
			$verwaltung_techn = $xml->createElement("verwaltung_techn");
			// <objektnr_intern>Ge-971-16</objektnr_intern>
			$objektnr_intern = $xml->createElement("objektnr_intern");
			$objektnr_intern->appendChild($xml->createTextNode($property->internal_object_number));
			$verwaltung_techn->appendChild($objektnr_intern);
			// <objektnr_extern>Ge-971-16</objektnr_extern>
			$objektnr_extern = $xml->createElement("objektnr_extern");
			$objektnr_extern->appendChild($xml->createTextNode($property->internal_object_number));
			$verwaltung_techn->appendChild($objektnr_extern);
			// <aktion />
			$aktion = $xml->createElement("aktion");
			if($export_property->export_action == "" || $export_property->export_action == "update" || $property->aktion == "delete") {
				$aktion_art = $xml->createAttribute("aktionart");
				if($export_property->export_action == "" || $export_property->export_action == "update") {
					$aktion_art->appendChild($xml->createTextNode(strtoupper("CHANGE")));
				}
				else if($property->aktion == "delete") {
					$aktion_art->appendChild($xml->createTextNode(strtoupper("DELETE")));
				}
				$aktion->appendChild($aktion_art);
			}
			$verwaltung_techn->appendChild($aktion);
			// <openimmo_obid>Od2u20100428135935000RexImmoD2U</openimmo_obid>
			$openimmo_obid = $xml->createElement("openimmo_obid");
			$openimmo_obid->appendChild($xml->createTextNode($property->openimmo_object_id));
			$verwaltung_techn->appendChild($openimmo_obid);
			// TODO: <xsd:element ref="kennung_ursprung" minOccurs="0"/>
			// <kennung_ursprung />
			$kennung_ursprung = $xml->createElement("kennung_ursprung");
			$verwaltung_techn->appendChild($kennung_ursprung);
			// <stand_vom>2010-01-26</stand_vom>
			$stand_vom = $xml->createElement("stand_vom");
			$stand_vom->appendChild($xml->createTextNode(date("Y-m-d", time())));
			$verwaltung_techn->appendChild($stand_vom);
			// TODO: <xsd:element ref="weitergabe_generell" minOccurs="0"/>
			// TODO: <xsd:element ref="weitergabe_positiv" minOccurs="0"/>
			// TODO: <xsd:element ref="weitergabe_negativ" minOccurs="0"/>
			// TODO: <xsd:element ref="user_defined_simplefield" minOccurs="0" maxOccurs="unbounded"/>
			// TODO: <xsd:element ref="user_defined_anyfield" minOccurs="0" maxOccurs="unbounded"/>
			// TODO: <xsd:element ref="user_defined_extend" minOccurs="0" maxOccurs="unbounded"/>
			// </verwaltung_techn>
			$immobilie->appendChild($verwaltung_techn);

			// TODO: <xsd:element ref="user_defined_simplefield" minOccurs="0" maxOccurs="unbounded"/>
			// TODO: <xsd:element ref="user_defined_anyfield" minOccurs="0" maxOccurs="unbounded"/>
			// TODO: <xsd:element ref="user_defined_extend" minOccurs="0" maxOccurs="unbounded"/>

			// </immobilie>
			$anbieter->appendChild($immobilie);
		}

		// TODO: <xsd:element ref="impressum" minOccurs="0"/>
		// TODO: <xsd:element ref="impressum_strukt" minOccurs="0"/>
		// TODO: <xsd:element ref="user_defined_simplefield" minOccurs="0" maxOccurs="unbounded"/>
		// TODO: <xsd:element ref="user_defined_anyfield" minOccurs="0" maxOccurs="unbounded"/>
		// TODO: <xsd:element ref="user_defined_extend" minOccurs="0" maxOccurs="unbounded"/>

		// </anbieter>
		$openimmo->appendChild($anbieter);

		// write XML file
		try {
			if($xml->save($this->cache_path . $this->xml_filename) === FALSE) {
				return rex_i18n::msg('d2u_immo_export_xml_cannot_create');
			}
			else {
				return "";
			}
		}
		catch(Exception $e) {
			return rex_i18n::msg('d2u_machinery_export_xml_cannot_create') . " - ". $e->getMessage();
		}
	}
}