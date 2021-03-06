<?php
namespace D2U_Immo;

/**
 * Property objects.
 */
class Property implements \D2U_Helper\ITranslationHelper {
	/**
	 * @var int Probperty ID.
	 */
	var $property_id = 0;
	
	/**
	 * @var string Internal project number.
	 */
	var $internal_object_number = "";
	
	/**
	 * @var int Sort priority.
	 */
	var $priority = 0;
	
	/**
	 * @var Contact Contact object.
	 */
	var $contact = FALSE;
	
	/**
	 * @var Category Category object. 
	 */
	var $category = FALSE;
	
	/**
	 * @var string Type of use. Values are defined in OpenImmo definition of
	 * value "nutzungsart".
	 */
	var $type_of_use = "";
	
	/**
	 * @var string Type of market, either KAUF, MIETE_PACHT, ERBPACHT, LEASING.
	 * Values are defined in OpenImmo definition of value "vermarktungsart".
	 */
	var $market_type = "";
	
	/**
	 * @var string Type of object. Values are defined in OpenImmo definition
	 * of value "objektart".
	 */
	var $object_type = "";
	
	/**
	 * @var string Type of apartment. Values are defined in OpenImmo definition
	 * of value "wohnungtyp".
	 */
	var $apartment_type = "";
	
	/**
	 * @var string Type of house. Values are defined in OpenImmo definition
	 * of value "haustyp".
	 */
	var $house_type = "";
	
	/**
	 * @var string Type of land. Values are defined in OpenImmo definition
	 * of value "grundst_typ".
	 */
	var $land_type = "";
	
	/**
	 * @var string Type of office. Values are defined in OpenImmo definition
	 * of value "buero_typ".
	 */
	var $office_type = "";

	/**
	 * @var string Type of hall / warehouse. Values are defined in OpenImmo definition
	 * of value "hallen_lager_prod".
	 */
	var $hall_warehouse_type = "";
	
	/**
	 * @var string Type of car parking places. Values are defined in OpenImmo definition
	 * of value "parken".
	 */
	var $parking_type = "";
	
	/**
	 * @var string Type of office. Values are defined in OpenImmo definition
	 * of value "sonstige_typ".
	 */
	var $other_type = "";
	
	/**
	 * @var string Street.
	 */
	var $street = "";
	
	/**
	 * @var string House number.
	 */
	var $house_number = "";
	
	/**
	 * @var string ZIP code.
	 */
	var $zip_code = "";
	
	/**
	 * @var string City
	 */
	var $city = "";
	
	/**
	 * @var string Three char ISO country code
	 */
	var $country_code = "";
	
	/**
	 * @var string Longitude
	 */
	var $longitude = "";
	
	/**
	 * @var string Latitude
	 */
	var $latitude = "";
	
	/**
	 * @var int Floor
	 */
	var $floor = 0;
	
	/**
	 * @var boolean TRUE if property addess may be published
	 */
	var $publish_address = TRUE;

	/**
	 * @var int Price if object can be bought.
	 */
	var $purchase_price = 0;
	
	/**
	 * @var int Price per square meter
	 */
	var $purchase_price_m2 = 0;
	
	/**
	 * @var int Monthly cold rent if object is for rent
	 */
	var $cold_rent = 0;
	
	/**
	 * @var boolean TRUE if rent has additional VAT
	 */
	var $price_plus_vat = FALSE;

	/**
	 * @var boolean TRUE if rent has additional VAT
	 * @deprecated since version 1.1.1
	 */
	var $rent_plus_vat = FALSE;
	
	/**
	 * @var int Additional monthly costs for rent
	 */
	var $additional_costs = 0;
	
	/**
	 * @var string Deposit
	 */
	var $deposit = 0;
	
	/**
	 * @var string Courtage
	 */
	var $courtage = "";
	
	/**
	 * @var boolean TRUE if VAT is included in courtage
	 */
	var $courtage_incl_vat = TRUE;
	
	/**
	 * @var string Three digit ISO currency code.
	 */
	var $currency_code = "EUR";
	
	/**
	 * @var int Number of duplex parking spaces
	 */
	var $parking_space_duplex = 0;
	
	/**
	 * @var int Number of parking space
	 */
	var $parking_space_simple = 0;
	
	/**
	 * @var int Number of garage parking space
	 */
	var $parking_space_garage = 0;
	
	/**
	 * @var int Number of underground car park spaces
	 */
	var $parking_space_undergroundcarpark = 0;
	
	/**
	 * @var float Square meters of living area
	 */
	var $living_area = 0;
	
	/**
	 * @var float Square meters of total area
	 */
	var $total_area = 0;
	
	/**
	 * @var float Square meters of land area
	 */
	var $land_area = 0;
	
	/**
	 * @var float Number of rooms
	 */
	var $rooms = 0;
	
	/**
	 * @var int Year of construction
	 */
	var $construction_year = 0;
	
	/**
	 * @var boolean TRUE if property can be used
	 */
	var $flat_sharing_possible = 0;
	
	/**
	 * @var string[] Bath room features as described in OpenImmo value of
	 * definition "bad".
	 */
	var $bath = [];
	
	/**
	 * @var string[] Kitchen features as described in OpenImmo value of definition
	 * "kueche"
	 */
	var $kitchen = [];
	
	/**
	 * @var string[] Floor type as described in OpenImmo value of definition "boden".
	 */
	var $floor_type = [];
	
	/**
	 * @var string[] Heating type as described in OpenImmo value of definition "heizungsart".
	 */
	var $heating_type = [];
	
	/**
	 * @var string[] Firing type as described in OpenImmo value of definition "befeuerung".
	 */
	var $firing_type = [];
	
	/**
	 * @var string[] Elevator type as described in OpenImmo value of definition "fahrstuhl".
	 */
	var $elevator = [];
	
	/**
	 * @var boolean TRUE if home is wheelchair accessable.
	 */
	var $wheelchair_accessable = FALSE;
	
	/**
	 * @var boolean TRUE if cable or sat tv is available
	 */
	var $cable_sat_tv = TRUE;
	
	/**
	 * @var string[] Broadband type as described in OpenImmo value of definition "breitband_zugang".
	 */
	var $broadband_internet = [];
	
	/**
	 * @var string Condition type as described in OpenImmo value of definition "zustand".
	 */
	var $condition_type = "";
	
	/**
	 * @var string Type of energy pass. Either "BEDARF" or "VERBRAUCH"
	 */
	var $energy_pass = "";
	
	/**
	 * @var string Energy pass is valid until date.
	 */
	var $energy_pass_valid_until = "";
	
	/**
	 * @var string Energy pass consumption value.
	 */
	var $energy_consumption = "";
	
	/**
	 * @var boolean Energy pass including warm water
	 */
	var $including_warm_water = TRUE;
	
	/**
	 * @var string[] Picture filenames
	 */
	var $pictures = [];
	
	/**
	 * @var string[] Filenames of available ground plans.
	 */
	var $ground_plans = [];
	
	/**
	 * @var string[] Filenames of location plans.
	 */
	var $location_plans = [];
	
	/**
	 * @var string Starting from when on is property available.
	 */
	var $available_from = "";
	
	/**
	 * @var boolean TRUE if property rented.
	 */
	var $rented = FALSE;
	
	/**
	 * @var boolean Are animals permittet? If TRUE, yes.
	 */
	var $animals = TRUE;
	
	/**
	 * @var boolean TRUE if property is reserved.
	 */
	var $object_reserved = FALSE;
	
	/**
	 * @var	boolean TRUE if property is already sold.
	 */
	var $object_sold = FALSE;
	
	/**
	 * @var string Unique OpenImmo property identifier
	 */
	var $openimmo_object_id = "";
	
	/**
	 * @var string Online status. Either "online", "offline" or "archived".
	 */
	var $online_status = "";

	/**
	 * @var string Online status or window advertising plugin. Either "online" or "offline".
	 */
	var $window_advertising_status = "";

	/**
	 * @var string Unix timestamp for update date.
	 */
	var $updatedate = "";
	
	/**
	 * @var string Redaxo user who last updated property
	 */
	var $updateuser = "";
	
	/**
	 * @var int Redaxo language ID
	 */
	var $clang_id = 0;
	
	/**
	 * @var string Property title
	 */
	var $name = "";
	
	/**
	 * @var string Short description of property
	 */
	var $teaser = "";
	
	/**
	 * @var string General description
	 */
	var $description = "";
	
	/**
	 * @var string Description of the property location
	 */
	var $description_location = "";
	
	/**
	 * @var string Description of protperty equipment or furnishing
	 */
	var $description_equipment = "";
	
	/**
	 * @var string Description of other features
	 */
	var $description_others = "";
	
	/**
	 * @var string[] Language specific documents of the property
	 */
	var $documents = [];
		
	/**
	 * @var string Needs translation update? "no", "yes" or "delete"
	 */
	var $translation_needs_update = "delete";
	
	/**
	 * @var String URL der Maschine
	 */
	private $url = "";

	/**
	 * Fetches a property object from database or creates an empty property object.
	 * @param int $property_id Database property id
	 * @param int $clang_id Redaxo language id
	 */
	 public function __construct($property_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_immo_properties AS properties "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_immo_properties_lang AS lang "
					."ON properties.property_id = lang.property_id "
					."AND clang_id = ". $this->clang_id ." "
				."WHERE properties.property_id = ". $property_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if($num_rows > 0) {
			$this->property_id = $result->getValue("property_id");
			$this->additional_costs = $result->getValue("additional_costs");
			$this->animals = $result->getValue("animals") == "1" ? TRUE : FALSE;
			$this->apartment_type = $result->getValue("apartment_type");
			$this->available_from = $result->getValue("available_from");
			$this->bath = preg_grep('/^\s*$/s', explode("|", $result->getValue("bath")), PREG_GREP_INVERT);
			$this->broadband_internet = preg_grep('/^\s*$/s', explode("|", $result->getValue("broadband_internet")), PREG_GREP_INVERT);
			$this->cable_sat_tv = $result->getValue("cable_sat_tv") == "1" ? TRUE : FALSE;
			if($result->getValue("category_id") > 0) {
				$this->category = new Category($result->getValue("category_id"), $clang_id);
			}
			$this->city = $result->getValue("city");
			$this->cold_rent = $result->getValue("cold_rent");
			$this->condition_type = $result->getValue("condition_type");
			$this->construction_year = $result->getValue("construction_year");
			if($result->getValue("contact_id") > 0) {
				$this->contact = new Contact($result->getValue("contact_id"));
			}
			$this->country_code = $result->getValue("country_code");
			$this->courtage = $result->getValue("courtage");
			$this->courtage_incl_vat = $result->getValue("courtage_incl_vat") == "1" ? TRUE : FALSE;
			$this->currency_code = $result->getValue("currency_code");
			$this->deposit = $result->getValue("deposit");
			$this->description = html_entity_decode(stripslashes(htmlspecialchars_decode($result->getValue("description"))));
			$this->description_equipment = html_entity_decode(stripslashes(htmlspecialchars_decode($result->getValue("description_equipment"))));
			$this->description_location = html_entity_decode(stripslashes(htmlspecialchars_decode($result->getValue("description_location"))));
			$this->description_others = html_entity_decode(stripslashes(htmlspecialchars_decode($result->getValue("description_others"))));
			$this->documents = preg_grep('/^\s*$/s', explode(",", $result->getValue("documents")), PREG_GREP_INVERT);
			$this->elevator = preg_grep('/^\s*$/s', explode("|", $result->getValue("elevator")), PREG_GREP_INVERT);
			$this->energy_consumption = $result->getValue("energy_consumption");
			$this->energy_pass = $result->getValue("energy_pass");
			$this->energy_pass_valid_until = $result->getValue("energy_pass_valid_until");
			$this->firing_type = preg_grep('/^\s*$/s', explode("|", $result->getValue("firing_type")), PREG_GREP_INVERT);
			$this->floor = $result->getValue("floor");
			$this->floor_type = preg_grep('/^\s*$/s', explode("|", $result->getValue("floor_type")), PREG_GREP_INVERT);
			$this->ground_plans = preg_grep('/^\s*$/s', explode(",", $result->getValue("ground_plans")), PREG_GREP_INVERT);
			$this->heating_type = preg_grep('/^\s*$/s', explode("|", $result->getValue("heating_type")), PREG_GREP_INVERT);
			$this->house_number = $result->getValue("house_number");
			$this->house_type = $result->getValue("house_type");
			$this->including_warm_water = $result->getValue("including_warm_water") == "1" ? TRUE : FALSE;
			$this->internal_object_number = $result->getValue("internal_object_number");
			$this->kitchen = preg_grep('/^\s*$/s', explode("|", $result->getValue("kitchen")), PREG_GREP_INVERT);
			$this->land_area = $result->getValue("land_area");
			$this->land_type = $result->getValue("land_type");
			$this->latitude = $result->getValue("latitude") == "" ? 0 : $result->getValue("latitude");
			$this->living_area = $result->getValue("living_area");
			$this->location_plans = preg_grep('/^\s*$/s', explode(",", $result->getValue("location_plans")), PREG_GREP_INVERT);
			$this->longitude = $result->getValue("longitude") == "" ? 0 : $result->getValue("longitude");
			$this->market_type = stripslashes($result->getValue("market_type"));
			$this->name = $result->getValue("name");
			$this->object_reserved = $result->getValue("object_reserved") == "1" ? TRUE : FALSE;
			$this->object_sold = $result->getValue("object_sold") == "1" ? TRUE : FALSE;
			$this->object_type = $result->getValue("object_type");
			$this->office_type = $result->getValue("office_type");
			$this->hall_warehouse_type = $result->getValue("hall_warehouse_type");
			$this->online_status = $result->getValue("online_status");
			$this->openimmo_object_id = $result->getValue("openimmo_object_id") == "" ? $this->createOpenImmoObjectID() : $result->getValue("openimmo_object_id");
			$this->other_type = $result->getValue("other_type");
			$this->parking_space_duplex = $result->getValue("parking_space_duplex");
			$this->parking_space_garage = $result->getValue("parking_space_garage");
			$this->parking_space_simple = $result->getValue("parking_space_simple");
			$this->parking_space_undergroundcarpark = $result->getValue("parking_space_undergroundcarpark");
			$this->parking_type = $result->getValue("parking_type");
			$this->pictures = preg_grep('/^\s*$/s', explode(",", $result->getValue("pictures")), PREG_GREP_INVERT);
			$this->price_plus_vat = $result->getValue("price_plus_vat") == "1" ? TRUE : FALSE;
			// deprecated
			$this->rent_plus_vat = $this->price_plus_vat;
			$this->priority = $result->getValue("priority");
			$this->publish_address = $result->getValue("publish_address") == "1" ? TRUE : FALSE;
			$this->purchase_price = $result->getValue("purchase_price");
			$this->purchase_price_m2 = $result->getValue("purchase_price_m2");
			$this->rented = $result->getValue("rented") == "1" ? TRUE : FALSE;
			$this->flat_sharing_possible = $result->getValue("flat_sharing_possible") == "1" ? TRUE : FALSE;
			$this->rooms = ($result->getValue("rooms") == round($result->getValue("rooms")) ? round($result->getValue("rooms")) : $result->getValue("rooms"));
			$this->street = $result->getValue("street");
			$this->teaser = $result->getValue("teaser");
			$this->total_area = $result->getValue("total_area");
			if($result->getValue("translation_needs_update") != "") {
				$this->translation_needs_update = $result->getValue("translation_needs_update");
			}
			$this->type_of_use = $result->getValue("type_of_use");
			$this->updatedate = $result->getValue("updatedate");
			$this->updateuser = $result->getValue("updateuser");
			$this->wheelchair_accessable = $result->getValue("wheelchair_accessable") == "1" ? TRUE : FALSE;
			$this->zip_code = $result->getValue("zip_code");
			// Window advertising plugin fields
			if(\rex_plugin::get("d2u_immo", "window_advertising")->isAvailable()) {
				$this->window_advertising_status = $result->getValue("window_advertising_status") == "online" ? TRUE : FALSE;
			}
		}
	}

	/**
	 * Changes the status of a property
	 */
	public function changeStatus() {
		if($this->online_status == "online") {
			if($this->property_id > 0) {
				$query = "UPDATE ". \rex::getTablePrefix() ."d2u_immo_properties "
					."SET online_status = 'offline' "
					."WHERE property_id = ". $this->property_id;
				$result = \rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "offline";
			
			// Remove from export
			if(\rex_plugin::get("d2u_immo", "export")->isAvailable()) {
				ExportedProperty::removePropertyFromAllExports($this->property_id);
			}
		}
		else {
			if($this->property_id > 0) {
				$query = "UPDATE ". \rex::getTablePrefix() ."d2u_immo_properties "
					."SET online_status = 'online' "
					."WHERE property_id = ". $this->property_id;
				$result = \rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "online";
		}
		
		// Don't forget to regenerate URL cache / update search_it index
		\d2u_addon_backend_helper::generateUrlCache("property_id");
		\d2u_addon_backend_helper::generateUrlCache("category_id");
	}
	
	/**
	 * Changes the status of a property
	 */
	public function changeWindowAdvertisingStatus() {
		if(\rex_plugin::get("d2u_immo", "window_advertising")->isAvailable()) {
			if($this->window_advertising_status == "online") {
				if($this->property_id > 0) {
					$query = "UPDATE ". \rex::getTablePrefix() ."d2u_immo_properties "
						."SET window_advertising_status = 'offline' "
						."WHERE property_id = ". $this->property_id;
					$result = \rex_sql::factory();
					$result->setQuery($query);
				}
				$this->window_advertising_status = "offline";
			}
			else {
				if($this->property_id > 0) {
					$query = "UPDATE ". \rex::getTablePrefix() ."d2u_immo_properties "
						."SET window_advertising_status = 'online' "
						."WHERE property_id = ". $this->property_id;
					$result = \rex_sql::factory();
					$result->setQuery($query);
				}
				$this->window_advertising_status = "online";
			}
		}
	}
	
	/**
	 * Create a OpenImmoID. Description cited form OpenImmo follows:
	 * - Kennbuchstabe: ([O]bjekt|[A]nbieter) {K};
	 * - Software-Hersteller 3 chars, {P};
	 * - Timestamp (date-time) 17 Stellen, {YMThmst};
	 * - Zufall 10 Stellen; {R};
	 * - Form: KPPPYYYYMMTThhmmsstttRRRRRRRRRR;
	 * Bsp: OXZZ20011128124930123asd43fer34;
	 * @return string OpenImm Object ID
	 */
	private function createOpenImmoObjectID() {
		return "OD2U". date('YmdHis', time()) ."000". rand(10000, 99999). rand(10000, 99999);
	}
	
	/**
	 * Deletes the object.
	 * @param int $delete_all If TRUE, all translations and main object are deleted. If 
	 * FALSE, only this translation will be deleted.
	 */
	public function delete($delete_all = TRUE) {
		$query_lang = "DELETE FROM ". \rex::getTablePrefix() ."d2u_immo_properties_lang "
			."WHERE property_id = ". $this->property_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = \rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_immo_properties_lang "
			."WHERE property_id = ". $this->property_id;
		$result_main = \rex_sql::factory();
		$result_main->setQuery($query_main);
		if($result_main->getRows() == 0) {
			$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_immo_properties "
				."WHERE property_id = ". $this->property_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);

			// reset priorities
			$this->setPriority(TRUE);			
		}
		
		// Don't forget to regenerate URL cache / update search_it index
		\d2u_addon_backend_helper::generateUrlCache("property_id");
		\d2u_addon_backend_helper::generateUrlCache("category_id");
	}
	
	/**
	 * Get all properties.
	 * @param int $clang_id Redaxo clang id.
	 * @param string $market_type KAUF, MIETE_PACHT, ERBPACHT, LEASING or empty (all)
	 * @param boolean $only_online Show only online properties
	 * @return Properties[] Array with Property objects.
	 */
	public static function getAll($clang_id, $market_type = '', $only_online = FALSE) {
		$query = "SELECT lang.property_id FROM ". \rex::getTablePrefix() ."d2u_immo_properties_lang AS lang "
			."LEFT JOIN ". \rex::getTablePrefix() ."d2u_immo_properties AS properties "
				."ON lang.property_id = properties.property_id AND lang.clang_id = ". $clang_id ." ";
		if($only_online || $market_type != '') {
			$where = [];
			if($only_online) {
				$where[] = "online_status = 'online'";
			}
			if($market_type != '') {
				$where[] = "market_type = '". $market_type ."'";
			}
			$query .= "WHERE ". implode(' AND ', $where) ." ";
		}
		if(\rex_addon::get('d2u_immo')->hasConfig('default_property_sort') && \rex_addon::get('d2u_immo')->getConfig('default_property_sort') == 'priority') {
			$query .= 'ORDER BY priority ASC';
		}
		else {
			$query .= 'ORDER BY name ASC';
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$properties = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$properties[] = new Property($result->getValue("property_id"), $clang_id);
			$result->next();
		}
		return $properties;
	}

	/**
	 * Get all properties that are selected for window advertising.
	 * @param int $clang_id Redaxo clang id.
	 * @return Properties[] Array with Property objects.
	 */
	public static function getAllWindowAdvertisingProperties($clang_id) {
		$properties = [];
		if(\rex_plugin::get("d2u_immo", "window_advertising")->isAvailable()) {
			$query = "SELECT lang.property_id FROM ". \rex::getTablePrefix() ."d2u_immo_properties_lang AS lang "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_immo_properties AS properties "
					."ON lang.property_id = properties.property_id AND lang.clang_id = ". $clang_id ." "
				."WHERE window_advertising_status = 'online' ";
			if(\rex_addon::get('d2u_immo')->hasConfig('default_property_sort') && \rex_addon::get('d2u_immo')->getConfig('default_property_sort') == 'priority') {
				$query .= 'ORDER BY priority ASC';
			}
			else {
				$query .= 'ORDER BY name ASC';
			}
			$result = \rex_sql::factory();
			$result->setQuery($query);

			for($i = 0; $i < $result->getRows(); $i++) {
				$properties[] = new Property($result->getValue("property_id"), $clang_id);
				$result->next();
			}
		}
		return $properties;
	}

	/**
	 * Creates a short description suiteable for social networks.
	 * @return Short description.
	 */
	public function getSocialNetworkDescription() {
		$social_description = "";
		if (strtoupper($this->market_type) == "KAUF") {
			if ($this->purchase_price > 0) {
				$social_description .= Sprog\Wildcard::get('d2u_immo_purchase_price', $this->clang_id) .":&nbsp;". number_format($this->purchase_price, 0, ",", ".") .",-&nbsp;". $this->currency_code ."; "; 
			}
		} else {
			if ($this->cold_rent > 0) {
				$social_description .= Sprog\Wildcard::get('d2u_immo_cold_rent', $this->clang_id) .":&nbsp;". number_format($this->cold_rent, 2, ",", ".") .'&nbsp;'.$this->currency_code ."; ";
			}
			if ($this->additional_costs > 0) {
				$social_description .= Sprog\Wildcard::get('d2u_immo_additional_costs', $this->clang_id) . ":&nbsp;". number_format($this->additional_costs, 2, ",", ".") .'&nbsp;'.$this->currency_code ."; ";
			}
			if($this->price_plus_vat) {
				$social_description .= "<p>". \Sprog\Wildcard::get('d2u_immo_prices_plus_vat', $this->clang_id) ."</p>";
			}
		}

		if(strtoupper($this->object_type) == "HAUS" || strtoupper($this->object_type) == "WOHNUNG" || strtoupper($this->object_type) == "BUERO_PRAXEN") {
			if ($this->living_area > 0) {	
				if(strtoupper($this->object_type) == "HAUS" || strtoupper($this->object_type) == "WOHNUNG") {
					$social_description .= Sprog\Wildcard::get('d2u_immo_living_area', $this->clang_id) .":&nbsp;";
				}
				else if(strtoupper($this->object_type) == "BUERO_PRAXEN") {
					$social_description .= Sprog\Wildcard::get('d2u_immo_office_area', $this->clang_id) .":&nbsp;";
				}
				$social_description .= number_format($this->living_area, 2, ",", ".") ."m²; ";
			}	
			if ($this->rooms > 0) {	
				$social_description .= Sprog\Wildcard::get('d2u_immo_rooms', $this->clang_id) .":&nbsp;". $this->rooms ."; ";
			}
			if ($this->floor > 0) {
				$social_description .= Sprog\Wildcard::get('d2u_immo_floor', $this->clang_id) .":&nbsp;". $this->floor ."; ";
			}		
		}

		if ($this->total_area > 0) {	
			$social_description .= Sprog\Wildcard::get('d2u_immo_total_area', $this->clang_id) .":&nbsp;". number_format($this->total_area, 0, ",", ".") ."m²; ";
		}
		if ($this->land_area > 0) {	
			$social_description .= Sprog\Wildcard::get('d2u_immo_land_area', $this->clang_id) .":&nbsp;". number_format($this->land_area, 0, ",", ".") ."m²; ";
		}

		// Energieausweis
		$social_description .= Sprog\Wildcard::get('d2u_immo_energy_pass', $this->clang_id) ." (". Sprog\Wildcard::get('d2u_immo_energy_pass_'. $this->energy_pass, $this->clang_id) ."): "; 
		$social_description .=  Sprog\Wildcard::get('d2u_immo_energy_pass_valid_until', $this->clang_id) ." ". $this->energy_pass_valid_until .", ";
		$social_description .=  Sprog\Wildcard::get('d2u_immo_energy_pass_value', $this->clang_id) ." ". $this->energy_consumption;
		if($this->including_warm_water) {
			$social_description .=  " ". Sprog\Wildcard::get('d2u_immo_energy_pass_incl_warm_water', $this->clang_id);
		}
		$social_description .=  ", ". Sprog\Wildcard::get('d2u_immo_construction_year', $this->clang_id) ." ". $this->construction_year .", ";
		foreach($this->firing_type as $firing_type) {
			$social_description .=  Sprog\Wildcard::get('d2u_immo_firing_type_'. $firing_type, $this->clang_id) .", ";
		}
		$social_description .= "; ". Sprog\Wildcard::get('d2u_immo_form_city', $this->clang_id) .": ". $this->zip_code ." ". $this->city;
		
		return $social_description;
	}
	
	/**
	 * Get objects concerning translation updates
	 * @param int $clang_id Redaxo language ID
	 * @param string $type 'update' or 'missing'
	 * @return Country[] Array with country objects.
	 */
	public static function getTranslationHelperObjects($clang_id, $type) {
		$query = 'SELECT property_id FROM '. \rex::getTablePrefix() .'d2u_immo_properties_lang '
				."WHERE clang_id = ". $clang_id ." AND translation_needs_update = 'yes' "
				.'ORDER BY name';
		if($type == 'missing') {
			$query = 'SELECT main.property_id FROM '. \rex::getTablePrefix() .'d2u_immo_properties AS main '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_immo_properties_lang AS target_lang '
						.'ON main.property_id = target_lang.property_id AND target_lang.clang_id = '. $clang_id .' '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_immo_properties_lang AS default_lang '
						.'ON main.property_id = default_lang.property_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
					."WHERE target_lang.property_id IS NULL "
					.'ORDER BY default_lang.name';
			$clang_id = \rex_config::get('d2u_helper', 'default_lang');
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);

		$objects = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$objects[] = new Property($result->getValue("property_id"), $clang_id);
			$result->next();
		}
		
		return $objects;
    }
	
	/**
	 * Returns the URL of this object.
	 * @param string $including_domain TRUE if Domain name should be included
	 * @return string URL
	 */
	public function getURL($including_domain = FALSE) {
		if($this->url == "") {
			$d2u_immo = \rex_addon::get("d2u_immo");
				
			$parameterArray = [];
			$parameterArray['property_id'] = $this->property_id;
			$this->url = \rex_getUrl($d2u_immo->getConfig('article_id'), $this->clang_id, $parameterArray, "&");
		}

		if($including_domain) {
			if(\rex_addon::get('yrewrite') && \rex_addon::get('yrewrite')->isAvailable())  {
				return str_replace(\rex_yrewrite::getCurrentDomain()->getUrl() .'/', \rex_yrewrite::getCurrentDomain()->getUrl(), \rex_yrewrite::getCurrentDomain()->getUrl() . $this->url);
			}
			else {
				return str_replace(\rex::getServer(). '/', \rex::getServer(), \rex::getServer() . $this->url);
			}
		}
		else {
			return $this->url;
		}
	}
	
	/**
	 * Updates or inserts the object into database.
	 * @return boolean TRUE if successful
	 */
	public function save() {
		$error = 0;

		// Save the not language specific part
		$pre_save_object = new Property($this->property_id, $this->clang_id);

		// save priority, but only if new or changed
		if($this->priority != $pre_save_object->priority || $this->property_id == 0) {
			$this->setPriority();
		}

		if($this->property_id == 0 || $pre_save_object != $this) {
			$query = \rex::getTablePrefix() ."d2u_immo_properties SET "
					."additional_costs = ". $this->additional_costs .", "
					."animals = ". ($this->animals ? 1 : 0) .", "
					."apartment_type = '". $this->apartment_type ."', "
					."available_from = '". $this->available_from ."', "
					."bath = '|". implode("|", $this->bath) ."|', "
					."broadband_internet = '|". implode("|", $this->broadband_internet) ."|', "
					."cable_sat_tv = ". ($this->cable_sat_tv ? 1 : 0) .", "
					."category_id = ". ($this->category !== FALSE ? $this->category->category_id : 0) .", "
					."city = '". $this->city ."', "
					."cold_rent = ". $this->cold_rent .", "
					."price_plus_vat = ". ($this->price_plus_vat ? 1 : 0) .", "
					."condition_type = '". $this->condition_type ."', "
					."construction_year = ". $this->construction_year .", "
					."contact_id = ". ($this->contact !== FALSE ? $this->contact->contact_id : 0) .", "
					."country_code = '". $this->country_code ."', "
					."courtage = '". $this->courtage ."', "
					."courtage_incl_vat = ". ($this->courtage_incl_vat ? 1 : 0) .", "
					."currency_code = '". $this->currency_code ."', "
					."deposit = ". $this->deposit .", "
					."elevator = '|". implode("|", $this->elevator) ."|', "
					."energy_consumption = '". $this->energy_consumption ."', "
					."energy_pass = '". $this->energy_pass ."', "
					."energy_pass_valid_until = '". $this->energy_pass_valid_until ."', "
					."firing_type = '|". implode("|", $this->firing_type) ."|', "
					."floor = ". $this->floor .", "
					."floor_type = '|". implode("|", $this->floor_type) ."|', "
					."ground_plans = '". implode(",", $this->ground_plans) ."', "
					."hall_warehouse_type = '". $this->hall_warehouse_type ."', "
					."heating_type = '|". implode("|", $this->heating_type) ."|', "
					."house_number = '". $this->house_number ."', "
					."house_type = '". $this->house_type ."', "
					."including_warm_water = ". ($this->including_warm_water ? 1 : 0) .", "
					."internal_object_number = '". $this->internal_object_number ."', "
					."kitchen = '|". implode("|", $this->kitchen) ."|', "
					."land_area = ". str_replace(',', '.', $this->land_area) .", "
					."land_type = '". $this->land_type ."', "
					."latitude = '". $this->latitude ."', "
					."living_area = ". str_replace(',', '.', $this->living_area) .", "
					."location_plans = '". implode(",", $this->location_plans) ."', "
					."longitude = '". $this->longitude ."', "
					."market_type = '". $this->market_type ."', "
					."object_reserved = ". ($this->object_reserved ? 1 : 0) .", "
					."object_sold = ". ($this->object_sold ? 1 : 0) .", "
					."object_type = '". $this->object_type ."', "
					."office_type = '". $this->office_type ."', "
					."online_status = '". $this->online_status ."', "
					."openimmo_object_id = '". $this->openimmo_object_id ."', "
					."other_type = '". $this->other_type ."', "
					."parking_space_duplex = ". $this->parking_space_duplex .", "
					."parking_space_garage = ". $this->parking_space_garage .", "
					."parking_space_simple = ". $this->parking_space_simple .", "
					."parking_space_undergroundcarpark = ". $this->parking_space_undergroundcarpark .", "
					."parking_type = '". $this->parking_type ."', "
					."pictures = '". implode(",", $this->pictures) ."', "
					."publish_address = ". ($this->publish_address ? 1 : 0) .", "
					."purchase_price = ". $this->purchase_price .", "
					."purchase_price_m2 = ". $this->purchase_price_m2 .", "
					."rented = ". ($this->rented ? 1 : 0) .", "
					."flat_sharing_possible = ". ($this->flat_sharing_possible ? 1 : 0) .", "
					."rooms = ". $this->rooms .", "
					."street = '". $this->street ."', "
					."total_area = ". str_replace(',', '.', $this->total_area) .", "
					."type_of_use = '". $this->type_of_use ."', "
					."wheelchair_accessable = ". ($this->wheelchair_accessable ? 1 : 0) .", "
					."zip_code = '". $this->zip_code ."' ";
			if(\rex_plugin::get("d2u_immo", "window_advertising")->isAvailable()) {
				$query .= ", window_advertising_status = '". ($this->window_advertising_status ? 'online' : 'offline') ."' ";
			}

			if($this->property_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE property_id = ". $this->property_id;
			}
			$result = \rex_sql::factory();
			$result->setQuery($query);
			if($this->property_id == 0) {
				$this->property_id = $result->getLastId();
				$error = $result->hasError();
			}
			
			// Remove from export
			if(\rex_plugin::get("d2u_immo", "export")->isAvailable() && $pre_save_object->online_status == "online" && $this->online_status != "online") {
				ExportedProperty::removePropertyFromAllExports($this->property_id);
			}
		}
		
		$regenerate_urls = false;
		if($error == 0) {
			// Save the language specific part
			$pre_save_object = new Property($this->property_id, $this->clang_id);
			if($pre_save_object != $this) {
				$query = "REPLACE INTO ". \rex::getTablePrefix() ."d2u_immo_properties_lang SET "
						."property_id = '". $this->property_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."description = '". addslashes(htmlspecialchars($this->description)) ."', "
						."description_equipment = '". addslashes(htmlspecialchars($this->description_equipment)) ."', "
						."description_location = '". addslashes(htmlspecialchars($this->description_location)) ."', "
						."description_others = '". addslashes(htmlspecialchars($this->description_others)) ."', "
						."documents = '". implode(",", $this->documents) ."', "
						."teaser = '". $this->teaser ."', "
						."name = '". addslashes($this->name) ."', "
						."translation_needs_update = '". $this->translation_needs_update ."', "
						."updatedate = CURRENT_TIMESTAMP, "
						."updateuser = '". \rex::getUser()->getLogin() ."' ";
				$result = \rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
				
				if(!$error && $pre_save_object->name != $this->name) {
					$regenerate_urls = true;
				}
			}
		}

		// Update URLs
		if($regenerate_urls) {
			\d2u_addon_backend_helper::generateUrlCache("property_id");
			\d2u_addon_backend_helper::generateUrlCache("category_id");
		}
		
		return $error;
	}
	
	/**
	 * Reassigns priorities in database.
	 * @param boolean $delete Reorder priority after deletion
	 */
	private function setPriority($delete = FALSE) {
		// Pull prios from database
		$query = "SELECT property_id, priority FROM ". \rex::getTablePrefix() ."d2u_immo_properties "
			."WHERE property_id <> ". $this->property_id ." ORDER BY priority";
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		// When priority is too small, set at beginning
		if($this->priority <= 0) {
			$this->priority = 1;
		}
		
		// When prio is too high or was deleted, simply add at end 
		if($this->priority > $result->getRows() || $delete) {
			$this->priority = $result->getRows() + 1;
		}

		$properties = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$properties[$result->getValue("priority")] = $result->getValue("property_id");
			$result->next();
		}
		array_splice($properties, ($this->priority - 1), 0, array($this->property_id));

		// Save all prios
		foreach($properties as $prio => $property_id) {
			$query = "UPDATE ". \rex::getTablePrefix() ."d2u_immo_properties "
					."SET priority = ". ($prio + 1) ." " // +1 because array_splice recounts at zero
					."WHERE property_id = ". $property_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);
		}
	}
}