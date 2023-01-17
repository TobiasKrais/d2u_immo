<?php
/**
 * Redaxo D2U Immo Addon - Export Plugin
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

namespace D2U_Immo;

/**
 * Exported property object.
 */
class ExportedProperty {
	/**
	 * @var int Database ID
	 */
	var $property_id = 0;
	
	/**
	 * @var Provider Export provider object
	 */
	var $provider_id;

	/**
	 * @var string Export status. Either "add", "update" or "delete"
	 */
	var $export_action = "";
	
	/**
	 * @var string ID provider returned after import. Not all providers return
	 * this value. But some so and it is needed for deleting the property later
	 * on provider website.
	 */
	var $provider_import_id = "";
	
	/**
	 * @var string Export timestamp.
	 */
	var $export_timestamp = "";

	/**
	 * Constructor. Fetches the object from database
	 * @param int $property_id Used property ID
	 * @param int $provider_id Provider ID.
	 */
	 public function __construct($property_id, $provider_id) {
		$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_immo_export_properties "
				."WHERE property_id = ". $property_id ." "
					."AND provider_id = ". $provider_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		$this->property_id = $property_id;
		$this->provider_id = $provider_id;
		if ($num_rows > 0) {
			$this->export_action = $result->getValue("export_action");
			$this->provider_import_id = $result->getValue("provider_import_id");
			if($result->getValue("export_timestamp") != "") {
				$this->export_timestamp = $result->getValue("export_timestamp");
			}
		}
	}
	
	/**
	 * Add used property to export for this provider.
	 */
	public function addToExport() {
		if($this->export_action == "") {
			$this->export_action = "add";
		}
		else if($this->export_action == "add" || $this->export_action == "delete") {
			$this->export_action = "update";
		}
		
		$this->save();
	}

	/**
	 * Add all properties to export for given provider.
	 * @param int $provider_id Provider id
	 */
	public static function addAllToExport($provider_id) {
		$provider = new Provider($provider_id);
		$properties = Property::getAll($provider->clang_id, '', true);
		foreach($properties as $property) {
			$exported_property = new ExportedProperty($property->property_id, $provider_id);
			if($exported_property->export_action == "" && $exported_property->export_timestamp == "") {
				$exported_property->export_action = "add";
			}
			else if(($exported_property->export_action == "" && $exported_property->export_timestamp != "") || $exported_property->export_action == "delete") {
				$exported_property->export_action = "update";
			}
			$exported_property->save();
		}
	}
	
	/**
	 * Deletes the object.
	 */
	public function delete():void {
		$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_immo_export_properties "
			."WHERE property_id = ". $this->property_id ." AND provider_id = ". $this->provider_id;
		$result_lang = \rex_sql::factory();
		$result_lang->setQuery($query);
	}
	
	
	/**
	 * Get all exported used machines.
	 * @param Provider $provider Optional provider object
	 * @return ExportedProperty[] Array with exported properties objects.
	 */
	public static function getAll($provider = false) {
		$query = "SELECT property_id, provider_id FROM ". \rex::getTablePrefix() ."d2u_immo_export_properties AS export";
		if ($provider !== false && $provider->provider_id > 0) {
			$query .= " WHERE provider_id = ". $provider->provider_id;
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);

		$exported_properties = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$exported_properties[] = new ExportedProperty($result->getValue("property_id"), $result->getValue("provider_id"));
			$result->next();
		}
		return $exported_properties;
	}
	
	/**
	 * Proves, if Property is set for export for this provider.
	 * @return boolean true if set, false if not
	 */
	public function isSetForExport() {
		if($this->export_action == "add" || $this->export_action == "update" || ($this->export_action == "" && $this->export_timestamp != "")) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Remove all properties to export for given provider.
	 */
	public static function removeAllDeletedFromExport() {
		$query= "SELECT exported_properties.property_id FROM ". \rex::getTablePrefix() ."d2u_immo_export_properties AS exported_properties "
			."LEFT JOIN ". \rex::getTablePrefix() ."d2u_immo_used_properties AS used_properties "
				."ON exported_properties.property_id = properties.property_id "
			."WHERE properties.property_id IS NULL "
			."GROUP BY exported_properties.property_id";
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		for($i= 0; $i < $result->getRows(); $i++) {
			$query_update = "UPDATE ". \rex::getTablePrefix() ."d2u_immo_export_properties "
				."SET export_action = 'delete' "
				."WHERE property_id = ". $result->getValue("exported_properties.property_id");
			$result_update = \rex_sql::factory();
			$result_update->setQuery($query_update);
			
			$result->next();
		}
	}

	/**
	 * Remove all properties to export for given provider.
	 * @param int $provider_id Provider ID
	 */
	public static function removeAllFromExport($provider_id) {
		$query_lang = "UPDATE ". \rex::getTablePrefix() ."d2u_immo_export_properties "
			."SET export_action = 'delete' "
			."WHERE provider_id = ". $provider_id;
		$result_lang = \rex_sql::factory();
		$result_lang->setQuery($query_lang);
	}
	
	/**
	 * Remove a property from export of all providers.
	 * @param int $property_id Used property id
	 */
	public static function removePropertyFromAllExports($property_id) {
		$query_lang = "UPDATE ". \rex::getTablePrefix() ."d2u_immo_export_properties "
			."SET export_action = 'delete' "
			."WHERE property_id = ". $property_id;
		$result_lang = \rex_sql::factory();
		$result_lang->setQuery($query_lang);
	}

	/**
	 * Remove from export list for this provider.
	 */
	public function removeFromExport() {
		$this->export_action = "delete";
		$this->save();
	}

	/**
	 * Updates or inserts the object into database.
	 * @return boolean true if successful
	 */
	public function save() {
		$query = "REPLACE INTO ". \rex::getTablePrefix() ."d2u_immo_export_properties SET "
				."property_id = ". $this->property_id .", "
				."provider_id = ". $this->provider_id .", "
				."export_action = '". $this->export_action ."', "
				."provider_import_id = '". $this->provider_import_id ."', "
				."export_timestamp = '". $this->export_timestamp ."'";
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		return !$result->hasError();
	}
}