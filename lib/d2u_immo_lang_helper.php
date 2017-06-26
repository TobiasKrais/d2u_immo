<?php
/**
 * Offers helper functions for language issues
 */
class d2u_immo_lang_helper {
	/**
	 * @var string[] Array with german replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_english = [
		'd2u_immo_additional_costs' => 'Additional costs',
		'd2u_immo_animals' => 'Animals allowed?',
		'd2u_immo_available_from' => 'Available from',
		'd2u_immo_back_to_list' => 'Back to list',
		'd2u_immo_bath' => 'Bathroom features',
		'd2u_immo_bath_BIDET' => 'Bidet',
		'd2u_immo_bath_DUSCHE' => 'Shower',
		'd2u_immo_bath_FENSTER' => 'Window',
		'd2u_immo_bath_PISSOIR' => 'Urinal',
		'd2u_immo_bath_WANNE' => 'Tub',
		'd2u_immo_broadband_internet' => 'Broadband internet',
		'd2u_immo_cable_sat_tv' => 'Cable or satellite-TV',
		'd2u_immo_cold_rent' => 'Cold rent',
		'd2u_immo_condition' => 'Condition',
		'd2u_immo_condition_ABRISSOBJEKT' => 'demolition object',
		'd2u_immo_condition_BAUFAELLIG' => 'dilapidated',
		'd2u_immo_condition_ENTKERNT' => 'cored',
		'd2u_immo_condition_ERSTBEZUG' => 'brand new',
		'd2u_immo_condition_GEPFLEGT' => 'groomed',
		'd2u_immo_condition_MODERNISIERT' => 'modernized',
		'd2u_immo_condition_NACH_VEREINBARUNG' => 'by arrangement',
		'd2u_immo_condition_NEUWERTIG' => 'as new',
		'd2u_immo_condition_PROJEKTIERT' => 'projected',
		'd2u_immo_condition_ROHBAU' => 'shell',
		'd2u_immo_condition_SANIERUNGSBEDUERFTIG' => 'to be refurbished',
		'd2u_immo_condition_TEIL_SANIERT' => 'partly refurbished',
		'd2u_immo_condition_TEIL_VOLLRENOVIERT' => 'refurbished',
		'd2u_immo_condition_TEIL_VOLLRENOVIERUNGSBED' => 'to be refurbished',
		'd2u_immo_condition_VOLL_SANIERT' => 'completely refurbished',
		'd2u_immo_construction_year' => 'Year of construction',
		'd2u_immo_contact_person' => 'Contact Person',
		'd2u_immo_courtage' => 'Commission',
		'd2u_immo_courtage_no' => 'No commission',
		'd2u_immo_courtage_incl_vat' => 'including VAT',
		'd2u_immo_deposit' => 'Deposit',
		'd2u_immo_description' => 'Description',
		'd2u_immo_description_equipment' => 'Equipment description',
		'd2u_immo_description_location' => 'Location decription',
		'd2u_immo_description_others' => 'Further information',
		'd2u_immo_elevator' => 'Elevator',
		'd2u_immo_elevator_LASTEN' => 'Goods lift',
		'd2u_immo_elevator_PERSONEN' => 'Elevator',
		'd2u_immo_energy_pass' => 'Energy pass',
		'd2u_immo_energy_pass_incl_warm_water' => 'including warm water',
		'd2u_immo_energy_pass_BEDARF' => 'demand',
		'd2u_immo_energy_pass_VERBRAUCH' => 'comsumption',
		'd2u_immo_energy_pass_type' => 'Type',
		'd2u_immo_energy_pass_valid_until' => 'Valid until',
		'd2u_immo_energy_pass_value' => 'Value',
		'd2u_immo_equipment' => 'Equipment',
		'd2u_immo_finance_calc_calculate' => 'Calculate',
		'd2u_immo_finance_calc_equity' => 'Equity',
		'd2u_immo_finance_calc_interest_rate' => 'Interest rate',
		'd2u_immo_finance_calc_investement' => 'Investment costs',
		'd2u_immo_finance_calc_monthly_rate' => 'Monthly rate',
		'd2u_immo_finance_calc_notary_costs' => 'Notary costs',
		'd2u_immo_finance_calc_other_costs' => 'Other costs',
		'd2u_immo_finance_calc_real_estate_tax' => 'Real estate tay',
		'd2u_immo_finance_calc_repayment' => 'Repayment',
		'd2u_immo_finance_calc_required_loan' => 'Required loan',
		'd2u_immo_finance_calc_total_costs' => 'Total costs',
		'd2u_immo_firing_type' => 'Primary energy sources',
		'd2u_immo_firing_type_ALTERNATIV' => 'Alternative energy source',
		'd2u_immo_firing_type_BLOCK' => 'CHP',
		'd2u_immo_firing_type_ELEKTRO' => 'Electricity',
		'd2u_immo_firing_type_WASSER-ELEKTRO' => 'Decentralized hot water',
		'd2u_immo_firing_type_GAS' => 'Gas',
		'd2u_immo_firing_type_HOLZ' => 'Wood',
		'd2u_immo_firing_type_FERN' => 'District heating',
		'd2u_immo_firing_type_FLUESSIGGAS' => 'Liquid gas',
		'd2u_immo_firing_type_OEL' => 'Oil',
		'd2u_immo_firing_type_PELLET' => 'Pellets',
		'd2u_immo_firing_type_SOLAR' => 'Solar energy',
		'd2u_immo_firing_type_LUFTWP' => 'Air source heat pump',
		'd2u_immo_firing_type_ERDWAERME' => 'Geothermal',
		'd2u_immo_flat_sharing' => 'Suitable for flat sharing',
		'd2u_immo_floor' => 'Floor',
		'd2u_immo_floor_type' => 'Floor',
		'd2u_immo_floor_type_DIELEN' => 'Floorboards',
		'd2u_immo_floor_type_DOPPELBODEN' => 'Raised floor',
		'd2u_immo_floor_type_ESTRICH' => 'Screed',
		'd2u_immo_floor_type_FERTIGPARKETT' => 'Engineered flooring',
		'd2u_immo_floor_type_FLIESEN' => 'Tiling',
		'd2u_immo_floor_type_GRANIT' => 'Granite',
		'd2u_immo_floor_type_KUNSTSTOFF' => 'Plastic',
		'd2u_immo_floor_type_LAMINAT' => 'Laminate',
		'd2u_immo_floor_type_LINOLEUM' => 'Linoleum',
		'd2u_immo_floor_type_MARMOR' => 'Marble',
		'd2u_immo_floor_type_PARKETT' => 'Parquet',
		'd2u_immo_floor_type_STEIN' => 'Stone',
		'd2u_immo_floor_type_TEPPICH' => 'Carpet',
		'd2u_immo_floor_type_TERRAKOTTA' => 'Teracotta',
		'd2u_immo_form_address' => 'Street, No.',
		'd2u_immo_form_captcha' => 'To prevent abuse, please enter captcha.',
		'd2u_immo_form_city' => 'City',
		'd2u_immo_form_email' => 'E-mail address',
		'd2u_immo_form_message' => 'Comments',
		'd2u_immo_form_name' => 'Name',
		'd2u_immo_form_phone' => 'Phone',
		'd2u_immo_form_required' => 'Required',
		'd2u_immo_form_send' => 'Send',
		'd2u_immo_form_thanks' => 'Thank you for your message. Your request will be processed as quickly as possible.',
		'd2u_immo_form_title' => 'Request form',
		'd2u_immo_form_validate_captcha' => 'The Captcha was not read correctly.',
		'd2u_immo_form_validate_email' => 'Please enter an email address.',
		'd2u_immo_form_validate_email_false' => 'Please correct email address.',
		'd2u_immo_form_validate_name' => 'Please enter your full name.',
		'd2u_immo_form_validate_phone' => 'Please enter your phone number.',
		'd2u_immo_form_validate_title' => 'Failure sending message:',
		'd2u_immo_form_zip' => 'ZIP code',
		'd2u_immo_ground_plans' => 'Ground plans',
		'd2u_immo_kitchen' => 'Kitchen',
		'd2u_immo_kitchen_EBK' => 'Fitted kitchen',
		'd2u_immo_kitchen_OFFEN' => 'Open kitchen',
		'd2u_immo_kitchen_PANTRY' => 'Kitchenette',
		'd2u_immo_land_area' => 'Land area',
		'd2u_immo_leasehold' => 'Leasehold',
		'd2u_immo_living_area' => 'Living Area',
		'd2u_immo_location_plans' => 'Location plans',
		'd2u_immo_parking_space_duplex' => 'Number duplex parking spaces',
		'd2u_immo_parking_space_garage' => 'Number garage parking spaces',
		'd2u_immo_parking_space_simple' => 'Number parking spaces',
		'd2u_immo_parking_space_undergroundcarpark' => 'Number parking spaces underground parking',
		'd2u_immo_print' => 'Print',
		'd2u_immo_print_expose' => 'Print expose',
		'd2u_immo_print_foot' => 'Please do not hesitate to contact us for further information.',
		'd2u_immo_print_foot_greetings' => 'Yours sincerly',
		'd2u_immo_print_short_expose' => 'Print short expose',
		'd2u_immo_purchase_price' => 'Price',
		'd2u_immo_purchase_price_m2' => 'Price p m²',
		'd2u_immo_object_reserved' => 'reserved',
		'd2u_immo_object_sold' => 'sold',
		'd2u_immo_office_area' => 'Office area',
		'd2u_immo_rented' => 'Rented?',
		'd2u_immo_recommendation_privacy_policy' => 'Privacy policy: we will not save or evaluate any part of the message. It is directly sent to the receipient.',
		'd2u_immo_recommendation_message' => 'Message for Receipient',
		'd2u_immo_recommendation_receipient_mail' => 'Receipient emailaddress',
		'd2u_immo_recommendation_receipient_name' => 'Receipient name',
		'd2u_immo_recommendation_sender_mail' => 'Your emailaddress',
		'd2u_immo_recommendation_sender_name' => 'Your name',
		'd2u_immo_recommendation_thanks' => 'Your message was sent.',
		'd2u_immo_recommendation_title' => 'Recommend offer',
		'd2u_immo_recommendation_validate_message' => 'Please leave a message.',
		'd2u_immo_recommendation_validate_receipient_mail' => 'Please enter receipients correct emailaddress.',
		'd2u_immo_recommendation_validate_receipient_name' => 'Please enter receipients name.',
		'd2u_immo_recommendation_validate_sender_mail' => 'Please enter your emailaddress.',
		'd2u_immo_recommendation_validate_sender_name' => 'Please enter your name.',
		'd2u_immo_rooms' => 'Number rooms',
		'd2u_immo_tab_calculator' => 'Finance Computer',
		'd2u_immo_tab_leasing' => 'Leasing offers',
		'd2u_immo_tab_map' => 'Map',
		'd2u_immo_tab_overview' => 'Overview',
		'd2u_immo_tab_pictures' => 'Pictures',
		'd2u_immo_tab_recommendation' => 'Recommend',
		'd2u_immo_tab_rent' => 'Rent offers',
		'd2u_immo_tab_request' => 'Request',
		'd2u_immo_tab_sale' => 'Sale offers',
		'd2u_immo_tentant_information' => 'Voluntary tenant home information',
		'd2u_immo_total_area' => 'Total area',
		'd2u_immo_warm_rent' => 'Warm rent',
		'd2u_immo_wheelchair_accessable' => 'Wheelchair accessable',
		'd2u_immo_yes' => 'Yes',
	];
	/**
	 * @var string[] Array with german replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_german = [
		'd2u_immo_additional_costs' => 'Nebenkosten',
		'd2u_immo_animals' => 'Haustiere erlaubt?',
		'd2u_immo_available_from' => 'Verfügbar ab',
		'd2u_immo_back_to_list' => 'Zurück zur Übersicht',
		'd2u_immo_bath' => 'Bad mit',
		'd2u_immo_bath_BIDET' => 'Bidet',
		'd2u_immo_bath_DUSCHE' => 'Dusche',
		'd2u_immo_bath_FENSTER' => 'Fenster',
		'd2u_immo_bath_PISSOIR' => 'Pissoir',
		'd2u_immo_bath_WANNE' => 'Wanne',
		'd2u_immo_broadband_internet' => 'Breitband Internet',
		'd2u_immo_cable_sat_tv' => 'Kabel oder Satelliten-TV',
		'd2u_immo_cold_rent' => 'Kaltmiete',
		'd2u_immo_condition' => 'Zustand',
		'd2u_immo_condition_ABRISSOBJEKT' => 'Abrissobjekt',
		'd2u_immo_condition_BAUFAELLIG' => 'Baufällig',
		'd2u_immo_condition_ENTKERNT' => 'Entkernt',
		'd2u_immo_condition_ERSTBEZUG' => 'Erstbezug',
		'd2u_immo_condition_GEPFLEGT' => 'Gepflegt',
		'd2u_immo_condition_MODERNISIERT' => 'Modernisiert',
		'd2u_immo_condition_NACH_VEREINBARUNG' => 'Nach Vereinbarung',
		'd2u_immo_condition_NEUWERTIG' => 'Neuwertig',
		'd2u_immo_condition_PROJEKTIERT' => 'Projektiert',
		'd2u_immo_condition_ROHBAU' => 'Rohbau',
		'd2u_immo_condition_SANIERUNGSBEDUERFTIG' => 'Sanierungsbedürftig',
		'd2u_immo_condition_TEIL_SANIERT' => 'Zum Teil saniert',
		'd2u_immo_condition_TEIL_VOLLRENOVIERT' => 'Renoviert',
		'd2u_immo_condition_TEIL_VOLLRENOVIERUNGSBED' => 'Renovierungsbedürftig',
		'd2u_immo_condition_VOLL_SANIERT' => 'Komplett Saniert',
		'd2u_immo_construction_year' => 'Baujahr',
		'd2u_immo_contact_person' => 'Ihr Ansprechpartner',
		'd2u_immo_courtage' => 'Provision',
		'd2u_immo_courtage_no' => 'Keine Provision',
		'd2u_immo_courtage_incl_vat' => 'inklusive gesetzliche Mehrwertsteuer',
		'd2u_immo_deposit' => 'Kaution',
		'd2u_immo_description' => 'Beschreibung',
		'd2u_immo_description_equipment' => 'Beschreibung Ausstattung',
		'd2u_immo_description_location' => 'Lagebeschreibung',
		'd2u_immo_description_others' => 'Weitere Informationen',
		'd2u_immo_elevator' => 'Aufzug',
		'd2u_immo_elevator_LASTEN' => 'Lastenaufzug',
		'd2u_immo_elevator_PERSONEN' => 'Personenaufzug',
		'd2u_immo_energy_pass' => 'Energieausweis',
		'd2u_immo_energy_pass_incl_warm_water' => 'mit Warmwasser',
		'd2u_immo_energy_pass_BEDARF' => 'Bedarf',
		'd2u_immo_energy_pass_VERBRAUCH' => 'Verbrauch',
		'd2u_immo_energy_pass_type' => 'Art',
		'd2u_immo_energy_pass_valid_until' => 'gültig bis',
		'd2u_immo_energy_pass_value' => 'Kennwert',
		'd2u_immo_equipment' => 'Ausstattungsmerkmale',
		'd2u_immo_finance_calc_calculate' => 'Berechnen',
		'd2u_immo_finance_calc_equity' => 'Eigenkapital',
		'd2u_immo_finance_calc_interest_rate' => 'Zinssatz',
		'd2u_immo_finance_calc_investement' => 'Immobilieninvestition',
		'd2u_immo_finance_calc_monthly_rate' => 'Monatliche Rate',
		'd2u_immo_finance_calc_notary_costs' => 'Notar- und Grundbuchkosten',
		'd2u_immo_finance_calc_other_costs' => 'Sonstiges (z.B. Renovierungskosten)',
		'd2u_immo_finance_calc_real_estate_tax' => 'Grunderwerbsteuer',
		'd2u_immo_finance_calc_repayment' => 'Tilgung',
		'd2u_immo_finance_calc_required_loan' => 'Benötigtes Darlehen',
		'd2u_immo_finance_calc_total_costs' => 'Gesamtkosten der Immobilie',
		'd2u_immo_firing_type' => 'Energieträger',
		'd2u_immo_firing_type_ALTERNATIV' => 'Alternativer Energieträger',
		'd2u_immo_firing_type_BLOCK' => 'Blockheizkraftwerk',
		'd2u_immo_firing_type_ELEKTRO' => 'Strom',
		'd2u_immo_firing_type_WASSER-ELEKTRO' => 'Dezentrales Warmwasser aus Strom',
		'd2u_immo_firing_type_GAS' => 'Gas',
		'd2u_immo_firing_type_HOLZ' => 'Holz',
		'd2u_immo_firing_type_FERN' => 'Fernwärme',
		'd2u_immo_firing_type_FLUESSIGGAS' => 'Flüssiggas',
		'd2u_immo_firing_type_OEL' => 'Öl',
		'd2u_immo_firing_type_PELLET' => 'Pellets',
		'd2u_immo_firing_type_SOLAR' => 'Solarenergie',
		'd2u_immo_firing_type_LUFTWP' => 'Luftwärmepumpe',
		'd2u_immo_firing_type_ERDWAERME' => 'Erdwärme',
		'd2u_immo_flat_sharing' => 'WG geeignet',
		'd2u_immo_floor' => 'Etage',
		'd2u_immo_floor_type' => 'Fußboden',
		'd2u_immo_floor_type_DIELEN' => 'Dielen',
		'd2u_immo_floor_type_DOPPELBODEN' => 'Doppelboden',
		'd2u_immo_floor_type_ESTRICH' => 'Estrich',
		'd2u_immo_floor_type_FERTIGPARKETT' => 'Fertigparkett',
		'd2u_immo_floor_type_FLIESEN' => 'Fliesen',
		'd2u_immo_floor_type_GRANIT' => 'Granit',
		'd2u_immo_floor_type_KUNSTSTOFF' => 'Kunststoff',
		'd2u_immo_floor_type_LAMINAT' => 'Laminat',
		'd2u_immo_floor_type_LINOLEUM' => 'Linoleum',
		'd2u_immo_floor_type_MARMOR' => 'Marmor',
		'd2u_immo_floor_type_PARKETT' => 'Parkett',
		'd2u_immo_floor_type_STEIN' => 'Stein',
		'd2u_immo_floor_type_TEPPICH' => 'Teppich',
		'd2u_immo_floor_type_TERRAKOTTA' => 'Terakotta',
		'd2u_immo_form_address' => 'Straße, Nr.',
		'd2u_immo_form_captcha' => 'Um Missbrauch vorzubeugen bitten wir Sie das Captcha einzugeben.',
		'd2u_immo_form_city' => 'Ort',
		'd2u_immo_form_email' => 'E-Mail Adresse',
		'd2u_immo_form_message' => 'Bemerkungen',
		'd2u_immo_form_name' => 'Name',
		'd2u_immo_form_phone' => 'Telefon',
		'd2u_immo_form_required' => 'Pflichtfelder',
		'd2u_immo_form_send' => 'Abschicken',
		'd2u_immo_form_thanks' => 'Danke für Ihre Nachricht. Wir kümmern uns schnellst möglich um Ihr Anliegen.',
		'd2u_immo_form_title' => 'Anfrage zum Objekt',
		'd2u_immo_form_validate_captcha' => 'Bitte geben Sie erneut das Captcha ein.',
		'd2u_immo_form_validate_email' => 'Bitte geben Sie eine E-Mail Adresse ein unter der wir Sie erreichen können.',
		'd2u_immo_form_validate_email_false' => 'Bitte prüfen Sie die E-Mail Adresse auf Korrektheit.',
		'd2u_immo_form_validate_name' => 'Um Sie korrekt ansprechen zu können, geben Sie bitte Ihren vollständigen Namen an.',
		'd2u_immo_form_validate_phone' => 'Bitte geben Sie Ihre Telefonnummer ein.',
		'd2u_immo_form_validate_title' => 'Fehler beim Senden:',
		'd2u_immo_form_zip' => 'PLZ',
		'd2u_immo_ground_plans' => 'Grundrisse',
		'd2u_immo_kitchen' => 'Küche',
		'd2u_immo_kitchen_EBK' => 'Einbaukücke',
		'd2u_immo_kitchen_OFFEN' => 'Offene Küche',
		'd2u_immo_kitchen_PANTRY' => 'Kochnische',
		'd2u_immo_land_area' => 'Grundstücksfläche',
		'd2u_immo_leasehold' => 'Pacht',
		'd2u_immo_living_area' => 'Wohnfläche',
		'd2u_immo_location_plans' => 'Lagepläne',
		'd2u_immo_no_machines' => 'Es tut uns Leid. Im Moment haben wir keine Angebote verfügbar. Bitte versuchen Sie es später nocheinmal.',
		'd2u_immo_parking_space_duplex' => 'Duplexstellplätze',
		'd2u_immo_parking_space_garage' => 'Garagenstellplätze',
		'd2u_immo_parking_space_simple' => 'Stellplätze',
		'd2u_immo_parking_space_undergroundcarpark' => 'Stellplätze Tiefgarage',
		'd2u_immo_print' => 'Drucken',
		'd2u_immo_print_expose' => 'Exposé drucken',
		'd2u_immo_print_foot' => 'Gerne stehen wir Ihnen für weitere Fragen unverbindlich zur Verfügung.',
		'd2u_immo_print_foot_greetings' => 'Mit freundlichen Grüßen',
		'd2u_immo_print_short_expose' => 'Gekürztes Exposé drucken',
		'd2u_immo_purchase_price' => 'Kaufpreis',
		'd2u_immo_purchase_price_m2' => 'Kaufpreis pro m²',
		'd2u_immo_object_reserved' => 'reserviert',
		'd2u_immo_object_sold' => 'verkauft',
		'd2u_immo_office_area' => 'Bürofläche',
		'd2u_immo_rented' => 'Vermietet?',
		'd2u_immo_recommendation_privacy_policy' => 'Hinweis zum Datenschutz: es werden keine Daten gespeichert oder ausgewertet.',
		'd2u_immo_recommendation_message' => 'Nachricht an den Empfänger',
		'd2u_immo_recommendation_receipient_mail' => 'E-Mailadresse des Empfängers',
		'd2u_immo_recommendation_receipient_name' => 'Name des Empfängers',
		'd2u_immo_recommendation_sender_mail' => 'Ihre E-Mailadresse',
		'd2u_immo_recommendation_sender_name' => 'Ihr Name',
		'd2u_immo_recommendation_thanks' => 'Ihre Nachricht wurde gesendet.',
		'd2u_immo_recommendation_title' => 'Angebot weiterleiten',
		'd2u_immo_recommendation_validate_message' => 'Bitte schreiben Sie eine Nachricht an den Empfänger.',
		'd2u_immo_recommendation_validate_receipient_mail' => 'Bitte prüfen Sie die E-Mailadresse des Empfängers.',
		'd2u_immo_recommendation_validate_receipient_name' => 'Bitte geben Sie den Namen des Empfängers an.',
		'd2u_immo_recommendation_validate_sender_mail' => 'Bitte geben Sie Ihre E-Mailadresse ein.',
		'd2u_immo_recommendation_validate_sender_name' => 'Bitte geben Sie Ihren Namen ein.',
		'd2u_immo_rooms' => 'Anzahl Zimmer',
		'd2u_immo_tab_calculator' => 'Finanzierungsrechner',
		'd2u_immo_tab_leasehold' => 'Erbpacht',
		'd2u_immo_tab_leasing' => 'Leasingangebote',
		'd2u_immo_tab_map' => 'Karte',
		'd2u_immo_tab_overview' => 'Übersicht',
		'd2u_immo_tab_pictures' => 'Bilder',
		'd2u_immo_tab_recommendation' => 'Weiterempfehlen',
		'd2u_immo_tab_rent' => 'Mietangebote',
		'd2u_immo_tab_request' => 'Anfrage',
		'd2u_immo_tab_sale' => 'Kaufangebote',
		'd2u_immo_tentant_information' => 'Freiwillige Mieterselbstauskunft',
		'd2u_immo_total_area' => 'Gesamtfläche',
		'd2u_immo_warm_rent' => 'Warmmiete',
		'd2u_immo_wheelchair_accessable' => 'Rollstuhlgerecht',
		'd2u_immo_yes' => 'Ja',
	];
	
	/**
	 * Factory method.
	 * @return d2u_immo_lang_helper Object
	 */
	public static function factory() {
		return new d2u_immo_lang_helper();
	}
	
	/**
	 * Installs the replacement table for this addon.
	 */
	public function install() {
		$d2u_immo = rex_addon::get('d2u_immo');
		
		foreach($this->replacements_english as $key => $value) {
			$addWildcard = rex_sql::factory();

			foreach (rex_clang::getAllIds() as $clang_id) {
				// Load values for input
				if($d2u_immo->hasConfig('lang_replacement_'. $clang_id) && $d2u_immo->getConfig('lang_replacement_'. $clang_id) == 'german') {
					$value = $this->replacements_german[$key];
				}
				else { 
					$value = $this->replacements_english[$key];
				}

				if(rex_addon::get('sprog')->isAvailable()) {
					$select_pid_query = "SELECT pid FROM ". rex::getTablePrefix() ."sprog_wildcard WHERE wildcard = '". $key ."' AND clang_id = ". $clang_id;
					$select_pid_sql = rex_sql::factory();
					$select_pid_sql->setQuery($select_pid_query);
					if($select_pid_sql->getRows() > 0) {
						// Update
						$query = "UPDATE ". rex::getTablePrefix() ."sprog_wildcard SET "
							."`replace` = '". addslashes($value) ."', "
							."updatedate = '". rex_sql::datetime() ."', "
							."updateuser = '". rex::getUser()->getValue('login') ."' "
							."WHERE pid = ". $select_pid_sql->getValue('pid');
						$sql = rex_sql::factory();
						$sql->setQuery($query);						
					}
					else {
						$id = 1;
						// Before inserting: id (not pid) must be same in all langs
						$select_id_query = "SELECT id FROM ". rex::getTablePrefix() ."sprog_wildcard WHERE wildcard = '". $key ."' AND id > 0";
						$select_id_sql = rex_sql::factory();
						$select_id_sql->setQuery($select_id_query);
						if($select_id_sql->getRows() > 0) {
							$id = $select_id_sql->getValue('id');
						}
						else {
							$select_id_query = "SELECT MAX(id) + 1 AS max_id FROM ". rex::getTablePrefix() ."sprog_wildcard";
							$select_id_sql = rex_sql::factory();
							$select_id_sql->setQuery($select_id_query);
							if($select_id_sql->getValue('max_id') != NULL) {
								$id = $select_id_sql->getValue('max_id');
							}
						}
						// Save
						$query = "INSERT INTO ". rex::getTablePrefix() ."sprog_wildcard SET "
							."id = ". $id .", "
							."clang_id = ". $clang_id .", "
							."wildcard = '". $key ."', "
							."`replace` = '". addslashes($value) ."', "
							."createdate = '". rex_sql::datetime() ."', "
							."createuser = '". rex::getUser()->getValue('login') ."', "
							."updatedate = '". rex_sql::datetime() ."', "
							."updateuser = '". rex::getUser()->getValue('login') ."'";
						$sql = rex_sql::factory();
						$sql->setQuery($query);
					}
				}
			}
		}
	}

	/**
	 * Uninstalls the replacement table for this addon.
	 */
	public function uninstall() {
		foreach($this->replacements_english as $key => $value) {
			if(rex_addon::get('sprog')->isAvailable()) {
				// Delete 
				$query = "DELETE FROM ". rex::getTablePrefix() ."sprog_wildcard WHERE wildcard = '". $key ."'";
				$select = rex_sql::factory();
				$select->setQuery($query);
			}
		}
	}
}