<?php

namespace D2U_Immo;

use d2u_addon_backend_helper;
use rex;
use rex_addon;
use rex_config;
use rex_plugin;
use rex_sql;

use rex_user;

use rex_yrewrite;

use function is_array;

/**
 * @api
 * Property objects.
 */
class Property implements \D2U_Helper\ITranslationHelper
{
    /** @var int probperty ID */
    public int $property_id = 0;

    /** @var string internal project number */
    public string $internal_object_number = '';

    /** @var int sort priority */
    public int $priority = 0;

    /** @var Contact|bool contact object */
    public Contact|bool $contact = false;

    /** @var Category|bool category object */
    public Category|bool $category = false;

    /**
     * @var string Type of use. Values are defined in OpenImmo definition of
     * value "nutzungsart".
     */
    public string $type_of_use = '';

    /**
     * @var string Type of market, either KAUF, MIETE_PACHT, ERBPACHT, LEASING.
     * Values are defined in OpenImmo definition of value "vermarktungsart".
     */
    public string $market_type = '';

    /**
     * @var string Type of object. Values are defined in OpenImmo definition
     * of value "objektart".
     */
    public string $object_type = '';

    /**
     * @var string Type of apartment. Values are defined in OpenImmo definition
     * of value "wohnungtyp".
     */
    public string $apartment_type = '';

    /**
     * @var string Type of house. Values are defined in OpenImmo definition
     * of value "haustyp".
     */
    public string $house_type = '';

    /**
     * @var string Type of land. Values are defined in OpenImmo definition
     * of value "grundst_typ".
     */
    public string $land_type = '';

    /**
     * @var string Type of office. Values are defined in OpenImmo definition
     * of value "buero_typ".
     */
    public string $office_type = '';

    /**
     * @var string Type of hall / warehouse. Values are defined in OpenImmo definition
     * of value "hallen_lager_prod".
     */
    public string $hall_warehouse_type = '';

    /**
     * @var string Type of car parking places. Values are defined in OpenImmo definition
     * of value "parken".
     */
    public string $parking_type = '';

    /**
     * @var string Type of office. Values are defined in OpenImmo definition
     * of value "sonstige_typ".
     */
    public string $other_type = '';

    /** @var string street */
    public string $street = '';

    /** @var string house number */
    public string $house_number = '';

    /** @var string ZIP code */
    public string $zip_code = '';

    /** @var string City */
    public string $city = '';

    /** @var string Three char ISO country code */
    public string $country_code = '';

    /** @var float Longitude */
    public float $longitude = 0;

    /** @var float Latitude */
    public float $latitude = 0;

    /** @var int Floor */
    public int $floor = 0;

    /** @var bool true if property addess may be published */
    public bool $publish_address = true;

    /** @var int price if object can be bought */
    public int $purchase_price = 0;

    /** @var int Price per square meter */
    public int $purchase_price_m2 = 0;

    /** @var int Monthly cold rent if object is for rent */
    public int $cold_rent = 0;

    /** @var bool true if rent has additional VAT */
    public bool$price_plus_vat = false;

    /** @var int Additional monthly costs for rent */
    public int $additional_costs = 0;

    /** @var int Deposit */
    public int $deposit = 0;

    /** @var string Courtage */
    public string $courtage = '';

    /** @var bool true if VAT is included in courtage */
    public bool $courtage_incl_vat = true;

    /** @var string three digit ISO currency code */
    public string $currency_code = 'EUR';

    /** @var int Number of duplex parking spaces */
    public int $parking_space_duplex = 0;

    /** @var int Number of parking space */
    public int $parking_space_simple = 0;

    /** @var int Number of garage parking space */
    public int $parking_space_garage = 0;

    /** @var int Number of underground car park spaces */
    public int $parking_space_undergroundcarpark = 0;

    /** @var float Square meters of living area */
    public float $living_area = 0;

    /** @var float Square meters of total area */
    public float $total_area = 0;

    /** @var float Square meters of land area */
    public float $land_area = 0;

    /** @var float Number of rooms */
    public float $rooms = 0;

    /** @var int Year of construction */
    public int $construction_year = 0;

    /** @var bool true if property can be used */
    public bool $flat_sharing_possible = false;

    /** @var array<string> bath room features as described in OpenImmo value of definition "bad" */
    public array $bath = [];

    /** @var array<string> Kitchen features as described in OpenImmo value of definition "kueche" */
    public array $kitchen = [];

    /** @var array<string> floor type as described in OpenImmo value of definition "boden" */
    public array $floor_type = [];

    /** @var array<string> heating type as described in OpenImmo value of definition "heizungsart" */
    public array $heating_type = [];

    /** @var array<string> firing type as described in OpenImmo value of definition "befeuerung" */
    public array $firing_type = [];

    /** @var array<string> elevator type as described in OpenImmo value of definition "fahrstuhl" */
    public array $elevator = [];

    /** @var bool true if home is wheelchair accessable */
    public bool $wheelchair_accessable = false;

    /** @var bool true if cable or sat tv is available */
    public bool $cable_sat_tv = true;

    /** @var array<string> broadband type as described in OpenImmo value of definition "breitband_zugang" */
    public array $broadband_internet = [];

    /** @var string condition type as described in OpenImmo value of definition "zustand" */
    public string $condition_type = '';

    /** @var string Type of energy pass. Either "BEDARF" or "VERBRAUCH" */
    public string $energy_pass = '';

    /** @var string energy pass is valid until date */
    public string $energy_pass_valid_until = '';

    /** @var string energy pass consumption value */
    public string $energy_consumption = '';

    /** @var bool Energy pass including warm water */
    public bool $including_warm_water = true;

    /** @var array<string> Picture filenames */
    public array $pictures = [];

    /** @var array<string> 360° picture filenames */
    public array $pictures_360 = [];

    /** @var array<string> filenames of available ground plans */
    public array $ground_plans = [];

    /** @var array<string> filenames of location plans */
    public array $location_plans = [];

    /** @var string starting from when on is property available */
    public string $available_from = '';

    /** @var bool true if property rented */
    public bool $rented = false;

    /** @var bool are animals permittet? If true, yes */
    public bool $animals = true;

    /** @var bool true if property is reserved */
    public bool $object_reserved = false;

    /** @var bool true if property is already sold */
    public bool $object_sold = false;

    /** @var string Unique OpenImmo property identifier */
    public string $openimmo_object_id = '';

    /** @var string Online status. Either "online", "offline" or "archived". */
    public string $online_status = '';

    /** @var string Online status or window advertising plugin. Either "online" or "offline". */
    public string $window_advertising_status = '';

    /** @var string unix timestamp for update date */
    public string $updatedate = '';

    /** @var string Redaxo user who last updated property */
    public string $updateuser = '';

    /** @var int Redaxo language ID */
    public int $clang_id = 0;

    /** @var string Property title */
    public string $name = '';

    /** @var string Short description of property */
    public string $teaser = '';

    /** @var string General description */
    public string $description = '';

    /** @var string Description of the property location */
    public string $description_location = '';

    /** @var string Description of protperty equipment or furnishing */
    public string $description_equipment = '';

    /** @var string Description of other features */
    public string $description_others = '';

    /** @var array<string> Language specific documents of the property */
    public array $documents = [];

    /** @var string OpenImmo Provider ID */
    public string $openimmo_anid = '';

    /** @var string Needs translation update? "no", "yes" or "delete" */
    public string $translation_needs_update = 'delete';

    /** @var string URL der Maschine */
    private string $url = '';

    /**
     * Fetches a property object from database or creates an empty property object.
     * @param int $property_id Database property id
     * @param int $clang_id Redaxo language id
     */
    public function __construct($property_id, $clang_id)
    {
        $this->clang_id = $clang_id;
        if($property_id <= 0) {
            return;
        }

        $query = 'SELECT * FROM '. rex::getTablePrefix() .'d2u_immo_properties AS properties '
                .'LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_properties_lang AS lang '
                    .'ON properties.property_id = lang.property_id '
                    .'AND clang_id = '. $this->clang_id .' '
                .'WHERE properties.property_id = '. $property_id;
        $result = rex_sql::factory();
        $result->setQuery($query);
        $num_rows = $result->getRows();

        if ($num_rows > 0) {
            $this->property_id = (int) $result->getValue('property_id');
            $this->additional_costs = (int) $result->getValue('additional_costs');
            $this->animals = 1 === (int) $result->getValue('animals') ? true : false;
            $this->apartment_type = (string) $result->getValue('apartment_type');
            $this->available_from = (string) $result->getValue('available_from');
            $bath = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('bath')), PREG_GREP_INVERT);
            $this->bath = is_array($bath) ? $bath : [];
            $broadband_internet = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('broadband_internet')), PREG_GREP_INVERT);
            $this->broadband_internet = is_array($broadband_internet) ? $broadband_internet : [];
            $this->cable_sat_tv = 1 === (int) $result->getValue('cable_sat_tv') ? true : false;
            if ((int) $result->getValue('category_id') > 0) {
                $this->category = new Category((int) $result->getValue('category_id'), $clang_id);
            }
            $this->city = (string) $result->getValue('city');
            $this->cold_rent = (int) $result->getValue('cold_rent');
            $this->condition_type = (string) $result->getValue('condition_type');
            $this->construction_year = (int) $result->getValue('construction_year');
            if ((int) $result->getValue('contact_id') > 0) {
                $this->contact = new Contact((int) $result->getValue('contact_id'));
            }
            $this->country_code = (string) $result->getValue('country_code');
            $this->courtage = (string) $result->getValue('courtage');
            $this->courtage_incl_vat = 1 === (int) $result->getValue('courtage_incl_vat') ? true : false;
            $this->currency_code = (string) $result->getValue('currency_code');
            $this->deposit = (int) $result->getValue('deposit');
            $this->description = html_entity_decode(stripslashes(htmlspecialchars_decode((string) $result->getValue('description'))));
            $this->description_equipment = html_entity_decode(stripslashes(htmlspecialchars_decode((string) $result->getValue('description_equipment'))));
            $this->description_location = html_entity_decode(stripslashes(htmlspecialchars_decode((string) $result->getValue('description_location'))));
            $this->description_others = html_entity_decode(stripslashes(htmlspecialchars_decode((string) $result->getValue('description_others'))));
            $documents = preg_grep('/^\s*$/s', explode(',', (string) $result->getValue('documents')), PREG_GREP_INVERT);
            $this->documents = is_array($documents) ? $documents : [];
            $elevator = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('elevator')), PREG_GREP_INVERT);
            $this->elevator = is_array($elevator) ? $elevator : [];
            $this->energy_consumption = (string) $result->getValue('energy_consumption');
            $this->energy_pass = (string) $result->getValue('energy_pass');
            $this->energy_pass_valid_until = (string) $result->getValue('energy_pass_valid_until');
            $firing_type = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('firing_type')), PREG_GREP_INVERT);
            $this->firing_type = is_array($firing_type) ? $firing_type : [];
            $this->floor = (int) $result->getValue('floor');
            $floor_type = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('floor_type')), PREG_GREP_INVERT);
            $this->floor_type = is_array($floor_type) ? $floor_type : [];
            $ground_plans = preg_grep('/^\s*$/s', explode(',', (string) $result->getValue('ground_plans')), PREG_GREP_INVERT);
            $this->ground_plans = is_array($ground_plans) ? $ground_plans : [];
            $heating_type = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('heating_type')), PREG_GREP_INVERT);
            $this->heating_type = is_array($heating_type) ? $heating_type : [];
            $this->house_number = (string) $result->getValue('house_number');
            $this->house_type = (string) $result->getValue('house_type');
            $this->including_warm_water = 1 === (int) $result->getValue('including_warm_water') ? true : false;
            $this->internal_object_number = (string) $result->getValue('internal_object_number');
            $kitchen = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('kitchen')), PREG_GREP_INVERT);
            $this->kitchen = is_array($kitchen) ? $kitchen : [];
            $this->land_area = (float) $result->getValue('land_area');
            $this->land_type = (string) $result->getValue('land_type');
            $this->latitude = (float) $result->getValue('latitude');
            $this->living_area = (float) $result->getValue('living_area');
            $location_plans = preg_grep('/^\s*$/s', explode(',', (string) $result->getValue('location_plans')), PREG_GREP_INVERT);
            $this->location_plans = is_array($location_plans) ? $location_plans : [];
            $this->longitude = (float) $result->getValue('longitude');
            $this->market_type = stripslashes((string) $result->getValue('market_type'));
            $this->name = (string) $result->getValue('name');
            $this->object_reserved = 1 === (int) $result->getValue('object_reserved') ? true : false;
            $this->object_sold = 1 === (int) $result->getValue('object_sold') ? true : false;
            $this->object_type = (string) $result->getValue('object_type');
            $this->office_type = (string) $result->getValue('office_type');
            $this->hall_warehouse_type = (string) $result->getValue('hall_warehouse_type');
            $this->online_status = (string) $result->getValue('online_status');
            $this->openimmo_object_id = '' === $result->getValue('openimmo_object_id') ? $this->createOpenImmoObjectID() : (string) $result->getValue('openimmo_object_id');
            $this->other_type = (string) $result->getValue('other_type');
            $this->parking_space_duplex = (int) $result->getValue('parking_space_duplex');
            $this->parking_space_garage = (int) $result->getValue('parking_space_garage');
            $this->parking_space_simple = (int) $result->getValue('parking_space_simple');
            $this->parking_space_undergroundcarpark = (int) $result->getValue('parking_space_undergroundcarpark');
            $this->parking_type = (string) $result->getValue('parking_type');
            $pictures = preg_grep('/^\s*$/s', explode(',', (string) $result->getValue('pictures')), PREG_GREP_INVERT);
            $this->pictures = is_array($pictures) ? $pictures : [];
            $pictures_360 = preg_grep('/^\s*$/s', explode(',', (string) $result->getValue('pictures_360')), PREG_GREP_INVERT);
            $this->pictures_360 = is_array($pictures_360) ? $pictures_360 : [];
            $this->price_plus_vat = 1 === (int) $result->getValue('price_plus_vat') ? true : false;
            $this->priority = (int) $result->getValue('priority');
            $this->publish_address = 1 === (int) $result->getValue('publish_address') ? true : false;
            $this->purchase_price = (int) $result->getValue('purchase_price');
            $this->purchase_price_m2 = (int) $result->getValue('purchase_price_m2');
            $this->rented = 1 === (int) $result->getValue('rented') ? true : false;
            $this->flat_sharing_possible = 1 === (int) $result->getValue('flat_sharing_possible') ? true : false;
            $this->rooms = ((float) $result->getValue('rooms') === round((float) $result->getValue('rooms')) ? round((float) $result->getValue('rooms')) : (float) $result->getValue('rooms'));
            $this->street = (string) $result->getValue('street');
            $this->teaser = (string) $result->getValue('teaser');
            $this->total_area = (float) $result->getValue('total_area');
            if ('' !== $result->getValue('translation_needs_update') && null !== $result->getValue('translation_needs_update')) {
                $this->translation_needs_update = (string) $result->getValue('translation_needs_update');
            }
            $this->type_of_use = (string) $result->getValue('type_of_use');
            $this->updatedate = (string) $result->getValue('updatedate');
            $this->updateuser = (string) $result->getValue('updateuser');
            $this->wheelchair_accessable = 1 === (int) $result->getValue('wheelchair_accessable') ? true : false;
            $this->zip_code = (string) $result->getValue('zip_code');
            // Import plugin
            if (rex_plugin::get('d2u_immo', 'import')->isAvailable()) {
                $this->openimmo_anid = (string) $result->getValue('openimmo_anid');
            }
            // Window advertising plugin fields
            if (rex_plugin::get('d2u_immo', 'window_advertising')->isAvailable()) {
                $this->window_advertising_status = (string) $result->getValue('window_advertising_status');
            }
        }
    }

    /**
     * Changes the status of a property.
     */
    public function changeStatus(): void
    {
        if ('online' === $this->online_status) {
            if ($this->property_id > 0) {
                $query = 'UPDATE '. rex::getTablePrefix() .'d2u_immo_properties '
                    ."SET online_status = 'offline' "
                    .'WHERE property_id = '. $this->property_id;
                $result = rex_sql::factory();
                $result->setQuery($query);
            }
            $this->online_status = 'offline';

            // Remove from export
            if (rex_plugin::get('d2u_immo', 'export')->isAvailable()) {
                ExportedProperty::removePropertyFromAllExports($this->property_id);
            }
        } else {
            if ($this->property_id > 0) {
                $query = 'UPDATE '. rex::getTablePrefix() .'d2u_immo_properties '
                    ."SET online_status = 'online' "
                    .'WHERE property_id = '. $this->property_id;
                $result = rex_sql::factory();
                $result->setQuery($query);
            }
            $this->online_status = 'online';
        }

        // Don't forget to regenerate URL cache / update search_it index
        d2u_addon_backend_helper::generateUrlCache('property_id');
        d2u_addon_backend_helper::generateUrlCache('category_id');
    }

    /**
     * Changes the status of a property.
     */
    public function changeWindowAdvertisingStatus(): void
    {
        if (rex_plugin::get('d2u_immo', 'window_advertising')->isAvailable()) {
            if ('online' === $this->window_advertising_status) {
                if ($this->property_id > 0) {
                    $query = 'UPDATE '. rex::getTablePrefix() .'d2u_immo_properties '
                        ."SET window_advertising_status = 'offline' "
                        .'WHERE property_id = '. $this->property_id;
                    $result = rex_sql::factory();
                    $result->setQuery($query);
                }
                $this->window_advertising_status = 'offline';
            } else {
                if ($this->property_id > 0) {
                    $query = 'UPDATE '. rex::getTablePrefix() .'d2u_immo_properties '
                        ."SET window_advertising_status = 'online' "
                        .'WHERE property_id = '. $this->property_id;
                    $result = rex_sql::factory();
                    $result->setQuery($query);
                }
                $this->window_advertising_status = 'online';
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
     * Bsp: OXZZ20011128124930123asd43fer34;.
     * @return string OpenImm Object ID
     */
    private function createOpenImmoObjectID()
    {
        return 'OD2U'. date('YmdHis', time()) .'000'. random_int(10000, 99999). random_int(10000, 99999);
    }

    /**
     * Deletes the object.
     * @param bool $delete_all If true, all translations and main object are deleted. If
     * false, only this translation will be deleted.
     */
    public function delete($delete_all = true): void
    {
        $query_lang = 'DELETE FROM '. rex::getTablePrefix() .'d2u_immo_properties_lang '
            .'WHERE property_id = '. $this->property_id
            . ($delete_all ? '' : ' AND clang_id = '. $this->clang_id);
        $result_lang = rex_sql::factory();
        $result_lang->setQuery($query_lang);

        // If no more lang objects are available, delete
        $query_main = 'SELECT * FROM '. rex::getTablePrefix() .'d2u_immo_properties_lang '
            .'WHERE property_id = '. $this->property_id;
        $result_main = rex_sql::factory();
        $result_main->setQuery($query_main);
        if (0 === (int) $result_main->getRows()) {
            $query = 'DELETE FROM '. rex::getTablePrefix() .'d2u_immo_properties '
                .'WHERE property_id = '. $this->property_id;
            $result = rex_sql::factory();
            $result->setQuery($query);

            // reset priorities
            $this->setPriority(true);
        }

        // Don't forget to regenerate URL cache / update search_it index
        d2u_addon_backend_helper::generateUrlCache('property_id');
        d2u_addon_backend_helper::generateUrlCache('category_id');
    }

    /**
     * Create a new, empty Property
     * @param int $clang_id Redaxo language id
     * @return Property Empty property
     */
    public static function factory($clang_id)
    {
        return new self(0, $clang_id);
    }

    /**
     * Get all properties.
     * @param int $clang_id redaxo clang id
     * @param string $market_type KAUF, MIETE_PACHT, ERBPACHT, LEASING or empty (all)
     * @param bool $only_online Show only online properties
     * @return array<int,Property> array with Property objects
     */
    public static function getAll($clang_id, $market_type = '', $only_online = false)
    {
        $query = 'SELECT lang.property_id FROM '. rex::getTablePrefix() .'d2u_immo_properties_lang AS lang '
            .'LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_properties AS properties '
                .'ON lang.property_id = properties.property_id AND lang.clang_id = '. $clang_id .' ';
        if ($only_online || '' !== $market_type) {
            $where = [];
            if ($only_online) {
                $where[] = "online_status = 'online'";
            }
            if ('' !== $market_type) {
                $where[] = "market_type = '". $market_type ."'";
            }
            $query .= 'WHERE '. implode(' AND ', $where) .' ';
        }
        if (rex_addon::get('d2u_immo')->hasConfig('default_property_sort') && 'priority' === rex_addon::get('d2u_immo')->getConfig('default_property_sort')) {
            $query .= 'ORDER BY priority ASC';
        } else {
            $query .= 'ORDER BY name ASC';
        }
        $result = rex_sql::factory();
        $result->setQuery($query);

        $properties = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $properties[(int) $result->getValue('property_id')] = new self((int) $result->getValue('property_id'), $clang_id);
            $result->next();
        }
        return $properties;
    }

    /**
     * Get all properties.
     * @param string $openimmo_anid Unique OpenImmo Anbieter ID
     * @param int $clang_id redaxo clang id
     * @param bool $only_online Show only online properties
     * @return array<int,Property> array with Property objects
     */
    public static function getAllForOpenImmoAnID($openimmo_anid, $clang_id, $only_online = false)
    {
        $properties = [];
        if(rex_plugin::get('d2u_immo', 'import')->isAvailable()) {
            $query = 'SELECT lang.property_id FROM '. rex::getTablePrefix() .'d2u_immo_properties_lang AS lang '
                .'LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_properties AS properties '
                    .'ON lang.property_id = properties.property_id AND lang.clang_id = '. $clang_id .' '
                .'WHERE openimmo_anid = "'. $openimmo_anid .'"';
            if ($only_online) {
                $query .= ' AND online_status = "online"';
            }
            if (rex_addon::get('d2u_immo')->hasConfig('default_property_sort') && 'priority' === rex_addon::get('d2u_immo')->getConfig('default_property_sort')) {
                $query .= ' ORDER BY priority ASC';
            } else {
                $query .= ' ORDER BY name ASC';
            }
            $result = rex_sql::factory();
            $result->setQuery($query);

            for ($i = 0; $i < $result->getRows(); ++$i) {
                $properties[(int) $result->getValue('property_id')] = new self((int) $result->getValue('property_id'), $clang_id);
                $result->next();
            }
        }
        return $properties;
    }

    /**
     * Get all properties that are selected for window advertising.
     * @param int $clang_id redaxo clang id
     * @return array<int,Property> array with Property objects
     */
    public static function getAllWindowAdvertisingProperties($clang_id)
    {
        $properties = [];
        if (rex_plugin::get('d2u_immo', 'window_advertising')->isAvailable()) {
            $query = 'SELECT lang.property_id FROM '. rex::getTablePrefix() .'d2u_immo_properties_lang AS lang '
                .'LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_properties AS properties '
                    .'ON lang.property_id = properties.property_id AND lang.clang_id = '. $clang_id .' '
                ."WHERE window_advertising_status = 'online' ";
            if (rex_addon::get('d2u_immo')->hasConfig('default_property_sort') && 'priority' === rex_addon::get('d2u_immo')->getConfig('default_property_sort')) {
                $query .= 'ORDER BY priority ASC';
            } else {
                $query .= 'ORDER BY name ASC';
            }
            $result = rex_sql::factory();
            $result->setQuery($query);

            for ($i = 0; $i < $result->getRows(); ++$i) {
                $properties[(int) $result->getValue('property_id')] = new self((int) $result->getValue('property_id'), $clang_id);
                $result->next();
            }
        }
        return $properties;
    }

    /**
     * Get property by OpenImmo object ID
     * @param string $openimmo_object_id OpenImmo object ID
     * @param int $clang_id redaxo clang id
     * @return Property|null Property or null if no propterty was found
     */
    public static function getByOpenImmoID($openimmo_object_id, $clang_id)
    {
        $query = 'SELECT property_id FROM '. rex::getTablePrefix() .'d2u_immo_properties '
                .'WHERE openimmo_object_id = "'. $openimmo_object_id .'"';
        $result = rex_sql::factory();
        $result->setQuery($query);

        for ($i = 0; $i < $result->getRows(); ++$i) {
            return new self((int) $result->getValue('property_id'), $clang_id);
        }
        return null;
    }
    
    /**
     * Creates a short description suiteable for social networks.
     * @return string description
     */
    public function getSocialNetworkDescription()
    {
        $social_description = '';
        if ('KAUF' === strtoupper($this->market_type)) {
            if ($this->purchase_price > 0) {
                $social_description .= \Sprog\Wildcard::get('d2u_immo_purchase_price', $this->clang_id) .':&nbsp;'. number_format($this->purchase_price, 0, ',', '.') .',-&nbsp;'. $this->currency_code .'; ';
            }
        } else {
            if ($this->cold_rent > 0) {
                $social_description .= \Sprog\Wildcard::get('d2u_immo_cold_rent', $this->clang_id) .':&nbsp;'. number_format($this->cold_rent, 2, ',', '.') .'&nbsp;'.$this->currency_code .'; ';
            }
            if ($this->additional_costs > 0) {
                $social_description .= \Sprog\Wildcard::get('d2u_immo_additional_costs', $this->clang_id) . ':&nbsp;'. number_format($this->additional_costs, 2, ',', '.') .'&nbsp;'.$this->currency_code .'; ';
            }
            if ($this->price_plus_vat) {
                $social_description .= '<p>'. \Sprog\Wildcard::get('d2u_immo_prices_plus_vat', $this->clang_id) .'</p>';
            }
        }

        if ('HAUS' === strtoupper($this->object_type) || 'WOHNUNG' === strtoupper($this->object_type) || 'BUERO_PRAXEN' === strtoupper($this->object_type)) {
            if ($this->living_area > 0) {
                if ('HAUS' === strtoupper($this->object_type) || 'WOHNUNG' === strtoupper($this->object_type)) {
                    $social_description .= \Sprog\Wildcard::get('d2u_immo_living_area', $this->clang_id) .':&nbsp;';
                } elseif ('BUERO_PRAXEN' === strtoupper($this->object_type)) {
                    $social_description .= \Sprog\Wildcard::get('d2u_immo_office_area', $this->clang_id) .':&nbsp;';
                }
                $social_description .= number_format($this->living_area, 2, ',', '.') .'m²; ';
            }
            if ($this->rooms > 0) {
                $social_description .= \Sprog\Wildcard::get('d2u_immo_rooms', $this->clang_id) .':&nbsp;'. $this->rooms .'; ';
            }
            if ($this->floor > 0) {
                $social_description .= \Sprog\Wildcard::get('d2u_immo_floor', $this->clang_id) .':&nbsp;'. $this->floor .'; ';
            }
        }

        if ($this->total_area > 0) {
            $social_description .= \Sprog\Wildcard::get('d2u_immo_total_area', $this->clang_id) .':&nbsp;'. number_format($this->total_area, 0, ',', '.') .'m²; ';
        }
        if ($this->land_area > 0) {
            $social_description .= \Sprog\Wildcard::get('d2u_immo_land_area', $this->clang_id) .':&nbsp;'. number_format($this->land_area, 0, ',', '.') .'m²; ';
        }

        // Energieausweis
        $social_description .= \Sprog\Wildcard::get('d2u_immo_energy_pass', $this->clang_id) .' ('. \Sprog\Wildcard::get('d2u_immo_energy_pass_'. $this->energy_pass, $this->clang_id) .'): ';
        $social_description .= \Sprog\Wildcard::get('d2u_immo_energy_pass_valid_until', $this->clang_id) .' '. $this->energy_pass_valid_until .', ';
        $social_description .= \Sprog\Wildcard::get('d2u_immo_energy_pass_value', $this->clang_id) .' '. $this->energy_consumption;
        if ($this->including_warm_water) {
            $social_description .= ' '. \Sprog\Wildcard::get('d2u_immo_energy_pass_incl_warm_water', $this->clang_id);
        }
        $social_description .= ', '. \Sprog\Wildcard::get('d2u_immo_construction_year', $this->clang_id) .' '. $this->construction_year .', ';
        foreach ($this->firing_type as $firing_type) {
            $social_description .= \Sprog\Wildcard::get('d2u_immo_firing_type_'. $firing_type, $this->clang_id) .', ';
        }
        $social_description .= '; '. \Sprog\Wildcard::get('d2u_immo_form_city', $this->clang_id) .': '. $this->zip_code .' '. $this->city;

        return $social_description;
    }

    /**
     * Get objects concerning translation updates.
     * @param int $clang_id Redaxo language ID
     * @param string $type 'update' or 'missing'
     * @return array<Property> array with property objects
     */
    public static function getTranslationHelperObjects($clang_id, $type)
    {
        $query = 'SELECT property_id FROM '. rex::getTablePrefix() .'d2u_immo_properties_lang '
                .'WHERE clang_id = '. $clang_id ." AND translation_needs_update = 'yes' "
                .'ORDER BY name';
        if ('missing' === $type) {
            $query = 'SELECT main.property_id FROM '. rex::getTablePrefix() .'d2u_immo_properties AS main '
                    .'LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_properties_lang AS target_lang '
                        .'ON main.property_id = target_lang.property_id AND target_lang.clang_id = '. $clang_id .' '
                    .'LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_properties_lang AS default_lang '
                        .'ON main.property_id = default_lang.property_id AND default_lang.clang_id = '. rex_config::get('d2u_helper', 'default_lang') .' '
                    .'WHERE target_lang.property_id IS NULL '
                    .'ORDER BY default_lang.name';
            $clang_id = (int) rex_config::get('d2u_helper', 'default_lang');
        }
        $result = rex_sql::factory();
        $result->setQuery($query);

        $objects = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $objects[] = new self((int) $result->getValue('property_id'), $clang_id);
            $result->next();
        }

        return $objects;
    }

    /**
     * Returns the URL of this object.
     * @param bool $including_domain true if Domain name should be included
     * @return string URL
     */
    public function getUrl($including_domain = false)
    {
        if ('' === $this->url) {
            $d2u_immo = rex_addon::get('d2u_immo');

            $parameterArray = [];
            $parameterArray['property_id'] = $this->property_id;
            $this->url = rex_getUrl((int) $d2u_immo->getConfig('article_id'), $this->clang_id, $parameterArray, '&');
        }

        if ($including_domain) {
            if (rex_addon::get('yrewrite')->isAvailable()) {
                return str_replace(rex_yrewrite::getCurrentDomain()->getUrl() .'/', rex_yrewrite::getCurrentDomain()->getUrl(), rex_yrewrite::getCurrentDomain()->getUrl() . $this->url);
            }

            return str_replace(rex::getServer(). '/', rex::getServer(), rex::getServer() . $this->url);

        }

        return $this->url;

    }

    /**
     * Updates or inserts the object into database.
     * @return bool true if successful
     */
    public function save()
    {
        $error = false;

        // Save the not language specific part
        $pre_save_object = new self($this->property_id, $this->clang_id);

        // save priority, but only if new or changed
        if ($this->priority !== $pre_save_object->priority || 0 === $this->property_id) {
            $this->setPriority();
        }

        if (0 === $this->property_id || $pre_save_object !== $this) {
            $query = rex::getTablePrefix() .'d2u_immo_properties SET '
                    .'additional_costs = '. $this->additional_costs .', '
                    .'animals = '. ($this->animals ? 1 : 0) .', '
                    ."apartment_type = '". $this->apartment_type ."', "
                    ."available_from = '". $this->available_from ."', "
                    ."bath = '|". implode('|', $this->bath) ."|', "
                    ."broadband_internet = '|". implode('|', $this->broadband_internet) ."|', "
                    .'cable_sat_tv = '. ($this->cable_sat_tv ? 1 : 0) .', '
                    .'category_id = '. ($this->category instanceof Category ? $this->category->category_id : 0) .', '
                    ."city = '". $this->city ."', "
                    .'cold_rent = '. $this->cold_rent .', '
                    .'price_plus_vat = '. ($this->price_plus_vat ? 1 : 0) .', '
                    ."condition_type = '". $this->condition_type ."', "
                    .'construction_year = '. $this->construction_year .', '
                    .'contact_id = '. ($this->contact instanceof Contact ? $this->contact->contact_id : 0) .', '
                    ."country_code = '". $this->country_code ."', "
                    ."courtage = '". $this->courtage ."', "
                    .'courtage_incl_vat = '. ($this->courtage_incl_vat ? 1 : 0) .', '
                    ."currency_code = '". $this->currency_code ."', "
                    .'deposit = '. $this->deposit .', '
                    ."elevator = '|". implode('|', $this->elevator) ."|', "
                    ."energy_consumption = '". $this->energy_consumption ."', "
                    ."energy_pass = '". $this->energy_pass ."', "
                    ."energy_pass_valid_until = '". $this->energy_pass_valid_until ."', "
                    ."firing_type = '|". implode('|', $this->firing_type) ."|', "
                    .'floor = '. $this->floor .', '
                    ."floor_type = '|". implode('|', $this->floor_type) ."|', "
                    ."ground_plans = '". implode(',', $this->ground_plans) ."', "
                    ."hall_warehouse_type = '". $this->hall_warehouse_type ."', "
                    ."heating_type = '|". implode('|', $this->heating_type) ."|', "
                    ."house_number = '". $this->house_number ."', "
                    ."house_type = '". $this->house_type ."', "
                    .'including_warm_water = '. ($this->including_warm_water ? 1 : 0) .', '
                    ."internal_object_number = '". $this->internal_object_number ."', "
                    ."kitchen = '|". implode('|', $this->kitchen) ."|', "
                    .'land_area = '. $this->land_area .', '
                    ."land_type = '". $this->land_type ."', "
                    .'latitude = '. $this->latitude .', '
                    .'living_area = '. $this->living_area .', '
                    ."location_plans = '". implode(',', $this->location_plans) ."', "
                    .'longitude = '. $this->longitude .', '
                    ."market_type = '". $this->market_type ."', "
                    .'object_reserved = '. ($this->object_reserved ? 1 : 0) .', '
                    .'object_sold = '. ($this->object_sold ? 1 : 0) .', '
                    ."object_type = '". $this->object_type ."', "
                    ."office_type = '". $this->office_type ."', "
                    ."online_status = '". $this->online_status ."', "
                    ."openimmo_object_id = '". $this->openimmo_object_id ."', "
                    ."other_type = '". $this->other_type ."', "
                    .'parking_space_duplex = '. $this->parking_space_duplex .', '
                    .'parking_space_garage = '. $this->parking_space_garage .', '
                    .'parking_space_simple = '. $this->parking_space_simple .', '
                    .'parking_space_undergroundcarpark = '. $this->parking_space_undergroundcarpark .', '
                    ."parking_type = '". $this->parking_type ."', "
                    ."pictures = '". implode(',', $this->pictures) ."', "
                    ."pictures_360 = '". implode(',', $this->pictures_360) ."', "
                    .'publish_address = '. ($this->publish_address ? 1 : 0) .', '
                    .'purchase_price = '. $this->purchase_price .', '
                    .'purchase_price_m2 = '. $this->purchase_price_m2 .', '
                    .'rented = '. ($this->rented ? 1 : 0) .', '
                    .'flat_sharing_possible = '. ($this->flat_sharing_possible ? 1 : 0) .', '
                    .'rooms = '. $this->rooms .', '
                    ."street = '". $this->street ."', "
                    .'total_area = '. $this->total_area .', '
                    ."type_of_use = '". $this->type_of_use ."', "
                    .'wheelchair_accessable = '. ($this->wheelchair_accessable ? 1 : 0) .', '
                    ."zip_code = '". $this->zip_code ."' ";
            if (rex_plugin::get('d2u_immo', 'import')->isAvailable()) {
                $query .= ", openimmo_anid = '". $this->openimmo_anid ."' ";
            }
            if (rex_plugin::get('d2u_immo', 'window_advertising')->isAvailable()) {
                $query .= ", window_advertising_status = '". ('online' === $this->window_advertising_status ? 'online' : 'offline') ."' ";
            }

            if (0 === $this->property_id) {
                $query = 'INSERT INTO '. $query;
            } else {
                $query = 'UPDATE '. $query .' WHERE property_id = '. $this->property_id;
            }
            $result = rex_sql::factory();
            $result->setQuery($query);
            if (0 === $this->property_id) {
                $this->property_id = (int) $result->getLastId();
                $error = $result->hasError();
            }

            // Remove from export
            if (rex_plugin::get('d2u_immo', 'export')->isAvailable() && 'online' === $pre_save_object->online_status && 'online' !== $this->online_status) {
                ExportedProperty::removePropertyFromAllExports($this->property_id);
            }
        }

        $regenerate_urls = false;
        if (!$error) {
            // Save the language specific part
            $pre_save_object = new self($this->property_id, $this->clang_id);
            if ($pre_save_object !== $this) {
                $query = 'REPLACE INTO '. rex::getTablePrefix() .'d2u_immo_properties_lang SET '
                        ."property_id = '". $this->property_id ."', "
                        ."clang_id = '". $this->clang_id ."', "
                        ."description = '". addslashes(htmlspecialchars($this->description)) ."', "
                        ."description_equipment = '". addslashes(htmlspecialchars($this->description_equipment)) ."', "
                        ."description_location = '". addslashes(htmlspecialchars($this->description_location)) ."', "
                        ."description_others = '". addslashes(htmlspecialchars($this->description_others)) ."', "
                        ."documents = '". implode(',', $this->documents) ."', "
                        ."teaser = '". $this->teaser ."', "
                        ."name = '". addslashes($this->name) ."', "
                        ."translation_needs_update = '". $this->translation_needs_update ."', "
                        .'updatedate = CURRENT_TIMESTAMP, '
                        ."updateuser = '". (rex::getUser() instanceof rex_user ? rex::getUser()->getLogin() : '') ."' ";
                $result = rex_sql::factory();
                $result->setQuery($query);
                $error = $result->hasError();

                if (!$error && $pre_save_object->name !== $this->name) {
                    $regenerate_urls = true;
                }
            }
        }

        // Update URLs
        if ($regenerate_urls) {
            d2u_addon_backend_helper::generateUrlCache('property_id');
            d2u_addon_backend_helper::generateUrlCache('category_id');
        }

        return $error;
    }

    /**
     * Reassigns priorities in database.
     * @param bool $delete Reorder priority after deletion
     */
    private function setPriority($delete = false): void
    {
        // Pull prios from database
        $query = 'SELECT property_id, priority FROM '. rex::getTablePrefix() .'d2u_immo_properties '
            .'WHERE property_id <> '. $this->property_id .' ORDER BY priority';
        $result = rex_sql::factory();
        $result->setQuery($query);

        // When priority is too small, set at beginning
        if ($this->priority <= 0) {
            $this->priority = 1;
        }

        // When prio is too high or was deleted, simply add at end
        if ($this->priority > $result->getRows() || $delete) {
            $this->priority = (int) $result->getRows() + 1;
        }

        $properties = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $properties[$result->getValue('priority')] = $result->getValue('property_id');
            $result->next();
        }
        array_splice($properties, $this->priority - 1, 0, [$this->property_id]);

        // Save all prios
        foreach ($properties as $prio => $property_id) {
            $query = 'UPDATE '. rex::getTablePrefix() .'d2u_immo_properties '
                    .'SET priority = '. ((int) $prio + 1) .' ' // +1 because array_splice recounts at zero
                    .'WHERE property_id = '. $property_id;
            $result = rex_sql::factory();
            $result->setQuery($query);
        }
    }
}
