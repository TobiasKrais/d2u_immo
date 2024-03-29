<?php
/**
 * Redaxo D2U Immo Addon.
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

namespace D2U_Immo;

use rex;
use rex_addon;
use rex_clang;
use rex_config;
use rex_sql;

/**
 * Immo contact.
 */
class Contact
{
    /** @var int Database ID */
    public int $contact_id = 0;

    /** @var string First name */
    public string $firstname = '';

    /** @var string Last name */
    public string $lastname = '';

    /** @var string Company */
    public string $company = '';

    /** @var string Street name */
    public string $street = '';

    /** @var string House number */
    public string $house_number = '';

    /** @var string ZIP code */
    public string $zip_code = '';

    /** @var string City */
    public string $city = '';

    /** @var string ISO three digit country code */
    public string $country_code = '';

    /** @var string Phone number */
    public string $phone = '';

    /** @var string Fax number */
    public string $fax = '';

    /** @var string Mobile phone number */
    public string $mobile = '';

    /** @var string E-Mail address */
    public string $email = '';

    /** @var string Picture */
    public string $picture = '';

    /**
     * Constructor. Reads a contact stored in database.
     * @param int $contact_id contact ID
     */
    public function __construct($contact_id)
    {
        if($contact_id <= 0) {
            return;
        }

        $query = 'SELECT * FROM '. rex::getTablePrefix() .'d2u_immo_contacts '
                .'WHERE contact_id = '. $contact_id;
        $result = rex_sql::factory();
        $result->setQuery($query);
        $num_rows = $result->getRows();

        if ($num_rows > 0) {
            $this->contact_id = (int) $result->getValue('contact_id');
            $this->city = (string) $result->getValue('city');
            $this->company = (string) $result->getValue('company');
            $this->country_code = (string) $result->getValue('country_code');
            $this->email = (string) $result->getValue('email');
            $this->fax = (string) $result->getValue('fax');
            $this->firstname = (string) $result->getValue('firstname');
            $this->house_number = (string) $result->getValue('house_number');
            $this->lastname = (string) $result->getValue('lastname');
            $this->mobile = (string) $result->getValue('mobile');
            $this->phone = (string) $result->getValue('phone');
            $this->picture = (string) $result->getValue('picture');
            $this->street = (string) $result->getValue('street');
            $this->zip_code = (string) $result->getValue('zip_code');
        }
    }

    /**
     * Deletes the object.
     */
    public function delete(): void
    {
        $query = 'DELETE FROM '. rex::getTablePrefix() .'d2u_immo_contacts '
            .'WHERE contact_id = '. $this->contact_id;
        $result = rex_sql::factory();
        $result->setQuery($query);
    }

    /**
     * Create a new, empty Contact
     * @return Contact Empty contact
     */
    public static function factory()
    {
        return new self(0);
    }

    /**
     * Get all contacts.
     * @return Contact[] array with Contact objects
     */
    public static function getAll()
    {
        $query = 'SELECT contact_id FROM '. rex::getTablePrefix() .'d2u_immo_contacts ORDER BY lastname';
        $result = rex_sql::factory();
        $result->setQuery($query);

        $contacts = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $contacts[] = new self((int) $result->getValue('contact_id'));
            $result->next();
        }
        return $contacts;
    }

    /**
     * Get contact by e-mail-address.
     * @param string $email E-mail-address
     * @return Contact|null
     */
    public static function getByMail($email)
    {
        $query = 'SELECT contact_id FROM '. rex::getTablePrefix() .'d2u_immo_contacts '
                .'WHERE email = "'. $email .'"';
        $result = rex_sql::factory();
        $result->setQuery($query);
        $num_rows = $result->getRows();

        if ($num_rows > 0) {
            return new self((int) $result->getValue('contact_id'));
        }

        return null;
    }

    /**
     * Gets the properties of the contact.
     * @return Property[] Properties
     */
    public function getProperties()
    {
        $query = 'SELECT properties.property_id FROM '. rex::getTablePrefix() .'d2u_immo_properties AS properties '
            .'LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_properties_lang AS lang '
                .'ON properties.property_id = lang.property_id AND lang.clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang') .' '
            .'WHERE contact_id = '. $this->contact_id .' ';
        if (rex_addon::get('d2u_immo')->hasConfig('default_property_sort') && 'priority' === rex_addon::get('d2u_immo')->getConfig('default_property_sort')) {
            $query .= 'ORDER BY priority ASC';
        } else {
            $query .= 'ORDER BY name ASC';
        }
        $result = rex_sql::factory();
        $result->setQuery($query);

        $properties = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $properties[] = new Property((int) $result->getValue('property_id'), rex_clang::getCurrentId());
            $result->next();
        }
        return $properties;
    }

    /**
     * Checks if at least one property has this contact
     * @return bool true if at least one property uses this contact
     */
    public function hasProperties()
    {
        $query = 'SELECT properties.property_id FROM '. rex::getTablePrefix() .'d2u_immo_properties AS properties '
            .'WHERE contact_id = '. $this->contact_id .' ';
        $result = rex_sql::factory();
        $result->setQuery($query);

        return $result->getRows() > 0;
    }

    /**
     * Updates or inserts the object into database.
     * @return bool true if error occurs
     */
    public function save()
    {
        $error = false;

        $query = rex::getTablePrefix() .'d2u_immo_contacts SET '
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

        if (0 === $this->contact_id) {
            $query = 'INSERT INTO '. $query;
        } else {
            $query = 'UPDATE '. $query .' WHERE contact_id = '. $this->contact_id;
        }

        $result = rex_sql::factory();
        $result->setQuery($query);
        if (0 === $this->contact_id) {
            $this->contact_id = (int) $result->getLastId();
            $error = $result->hasError();
        }

        return $error;
    }
}
