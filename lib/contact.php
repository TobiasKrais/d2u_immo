<?php
/**
 * Redaxo D2U Immo Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

namespace D2U_Immo;

/**
 * Immo contact
 */
class Contact {
	/**
	 * @var int Database ID
	 */
	var $contact_id = 0;
	
	/**
	 * @var string First name
	 */
	var $firstname = "";
	
	/**
	 * @var string Last name
	 */
	var $lastname = "";
	
	/**
	 * @var string Company
	 */
	var $company = "";
	
	/**
	 * @var string Street name
	 */
	var $street = "";
	
	/**
	 * @var string House number
	 */
	var $house_number = "";
	
	/**
	 * @var string ZIP code
	 */
	var $zip_code = "";
	
	/**
	 * @var string City
	 */
	var $city = "";
	
	/**
	 * @var string ISO three digit country code
	 */
	var $country_code = "";
	
	/**
	 * @var string Phone number
	 */
	var $phone = "";
	
	/**
	 * @var string Fax number
	 */
	var $fax = "";
	
	/**
	 * @var string Mobile phone number
	 */
	var $mobile = "";
	
	/**
	 * @var string E-Mail address
	 */
	var $email = "";

	/**
	 * @var string Picture
	 */
	var $picture = "";

	/**
	 * Constructor. Reads a contact stored in database.
	 * @param int $contact_id Contact ID.
	 */
	 public function __construct($contact_id) {
		$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_immo_contacts "
				."WHERE contact_id = ". $contact_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->contact_id = $result->getValue("contact_id");
			$this->city = $result->getValue("city");
			$this->company = $result->getValue("company");
			$this->country_code = $result->getValue("country_code");
			$this->email = $result->getValue("email");
			$this->fax = $result->getValue("fax");
			$this->firstname = $result->getValue("firstname");
			$this->house_number = $result->getValue("house_number");
			$this->lastname = $result->getValue("lastname");
			$this->mobile = $result->getValue("mobile");
			$this->phone = $result->getValue("phone");
			$this->picture = $result->getValue("picture");
			$this->street = $result->getValue("street");
			$this->zip_code = $result->getValue("zip_code");
		}
	}
	
	/**
	 * Deletes the object.
	 */
	public function delete() {
		$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_immo_contacts "
			."WHERE contact_id = ". $this->contact_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);
	}

	/**
	 * Get all contacts.
	 * @return Contact[] Array with Contact objects.
	 */
	public static function getAll() {
		$query = "SELECT contact_id FROM ". \rex::getTablePrefix() ."d2u_immo_contacts ORDER BY lastname";
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$contacts = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$contacts[] = new Contact($result->getValue("contact_id"));
			$result->next();
		}
		return $contacts;
	}
	
	/**
	 * Gets the properties of the contact.
	 * @return Property[] Properties
	 */
	public function getProperties() {
		$query = "SELECT properties.property_id FROM ". \rex::getTablePrefix() ."d2u_immo_properties AS properties "
			."LEFT JOIN ". \rex::getTablePrefix() ."d2u_immo_properties_lang AS lang "
				."ON properties.property_id = lang.property_id AND lang.clang_id = ". \intval(rex_config::get("d2u_helper", "default_lang")) ." "
			."WHERE contact_id = ". $this->contact_id ." ";
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
			$properties[] = new Property($result->getValue("property_id"), \rex_clang::getCurrentId());
			$result->next();
		}
		return $properties;
	}
	
	/**
	 * Updates or inserts the object into database.
	 * @return in error code if error occurs
	 */
	public function save() {
		$error = 0;

		$query = \rex::getTablePrefix() ."d2u_immo_contacts SET "
				."city = '". $this->city ."', "
				."company = '". $this->company ."', "
				."country_code = '". $this->country_code ."', "
				."email = '". $this->email ."', "
				."fax = '". $this->fax ."', "
				."firstname = '". $this->firstname ."', "
				."house_number = '". $this->house_number ."', "
				."lastname = '". $this->lastname ."', "
				."mobile = '". $this->mobile ."', "
				."phone = '". $this->phone ."', "
				."picture = '". $this->picture ."', "
				."street = '". $this->street ."', "
				."zip_code = '". $this->zip_code ."' ";

		if($this->contact_id === 0) {
			$query = "INSERT INTO ". $query;
		}
		else {
			$query = "UPDATE ". $query ." WHERE contact_id = ". $this->contact_id;
		}

		$result = \rex_sql::factory();
		$result->setQuery($query);
		if($this->contact_id === 0) {
			$this->contact_id = intval($result->getLastId());
			$error = $result->hasError();
		}
		
		return $error;
	}
}