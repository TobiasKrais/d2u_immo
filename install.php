<?php
$sql = rex_sql::factory();
// Install database
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_immo_contacts (
	contact_id int(10) unsigned NOT NULL auto_increment,
	firstname varchar(50) collate utf8_general_ci default NULL,
	lastname varchar(50) collate utf8_general_ci NOT NULL,
	company varchar(100) collate utf8_general_ci default NULL,
	street varchar(100) collate utf8_general_ci default NULL,
	house_number varchar(5) collate utf8_general_ci default NULL,
	zip_code varchar(10) collate utf8_general_ci default NULL,
	city varchar(255) collate utf8_general_ci default NULL,
	country_code varchar(3) collate utf8_general_ci default NULL,
	phone varchar(50) collate utf8_general_ci default NULL,
	fax varchar(50) collate utf8_general_ci default NULL,
	mobile varchar(50) collate utf8_general_ci default NULL,
	email varchar(255) collate utf8_general_ci default NULL,
	picture varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (contact_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");

$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_immo_categories (
	category_id int(10) unsigned NOT NULL auto_increment,
	priority int(10) default NULL,
	parent_category_id int(10) default NULL,
	picture varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (category_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_immo_categories_lang (
	category_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8_general_ci default NULL,
	translation_needs_update varchar(7) collate utf8_general_ci default NULL,
	updatedate int(11) default NULL,
	updateuser varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (category_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");

$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_immo_properties (
	property_id int(10) unsigned NOT NULL auto_increment,
	internal_object_number varchar(255) collate utf8_general_ci default NULL,
	priority int(10) default NULL,
	contact_id int(10) default NULL,
	category_id int(10) default NULL,
	type_of_use varchar(255) collate utf8_general_ci default NULL,
	market_type varchar(255) collate utf8_general_ci default NULL,
	object_type varchar(255) collate utf8_general_ci default NULL,
	apartment_type varchar(255) collate utf8_general_ci default NULL,
	house_type varchar(255) collate utf8_general_ci default NULL,
	land_type varchar(255) collate utf8_general_ci default NULL,
	office_type varchar(255) collate utf8_general_ci default NULL,
	other_type varchar(255) collate utf8_general_ci default NULL,
	street varchar(100) collate utf8_general_ci default NULL,
	house_number varchar(5) collate utf8_general_ci default NULL,
	zip_code varchar(10) collate utf8_general_ci NOT NULL,
	city varchar(255) collate utf8_general_ci NOT NULL,
	country_code varchar(3) collate utf8_general_ci default NULL,
	longitude varchar(20) collate utf8_general_ci default NULL,
	latitude varchar(20) collate utf8_general_ci default NULL,
	floor int(4) default NULL,
	publish_address int(1) default 0,
	purchase_price int(15) default NULL,
	purchase_price_m2 int(15) default NULL,
	cold_rent int(15) default NULL,
	additional_costs int(15) default NULL,
	deposit varchar(50) collate utf8_general_ci default NULL,
	courtage varchar(255) collate utf8_general_ci,
	courtage_incl_vat int(1) default 0,
	currency_code varchar(3) collate utf8_general_ci,
	parking_space_duplex int(5) default NULL,
	parking_space_simple int(5) default NULL,
	parking_space_garage int(5) default NULL,
	parking_space_undergroundcarpark int(5) default NULL,
	living_area decimal(10,2) default NULL,
	total_area decimal(10,2) default NULL,
	land_area decimal(10,2) default NULL,
	rooms varchar(50) collate utf8_general_ci default NULL,
	construction_year int(5) default NULL,
	residential_community_possible int(1) default 0,
	bath varchar(255) collate utf8_general_ci default NULL,
	kitchen varchar(255) collate utf8_general_ci default NULL,
	floor_type varchar(255) collate utf8_general_ci default NULL,
	heating_type varchar(255) collate utf8_general_ci default NULL,
	firing_type varchar(255) collate utf8_general_ci default NULL,
	elevator varchar(255) collate utf8_general_ci default NULL,
	wheelchair_accessable int(1) default 0,
	cable_sat_tv int(1) default 0,
	broadband_internet varchar(255) collate utf8_general_ci default NULL,
	condition_type varchar(255) collate utf8_general_ci default NULL,
	energy_pass varchar(255) collate utf8_general_ci default NULL,
	energy_pass_valid_until varchar(50) collate utf8_general_ci default NULL,
	energy_consumption varchar(255) collate utf8_general_ci default NULL,
	including_warm_water int(1) default 0,
	pictures text collate utf8_general_ci,
	ground_plans text collate utf8_general_ci,
	location_plans text collate utf8_general_ci,
	available_from varchar(255) collate utf8_general_ci default NULL,
	rented int(1) default 0,
	animals int(1) default 0,
	object_reserved int(1) default 0,
	object_sold int(1) default 0,
	object_archived int(1) default 0,
	openimmo_object_id varchar(31) collate utf8_general_ci default NULL,
	online_status varchar(10) collate utf8_general_ci default 'online',
	PRIMARY KEY (property_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_immo_properties_lang (
	property_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8_general_ci default NULL,
	teaser varchar(255) collate utf8_general_ci default NULL,
	description text collate utf8_general_ci default NULL,
	description_location text collate utf8_general_ci default NULL,
	description_equipment text collate utf8_general_ci default NULL,
	description_others text collate utf8_general_ci default NULL,
	documents text collate utf8_general_ci,
	translation_needs_update varchar(7) collate utf8_general_ci default NULL,
	updatedate int(11) default NULL,
	updateuser varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (property_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");

// Standard settings
if (!$this->hasConfig()) {
	// Find first Redaxo lang an set later as default lang
	$langs = rex_clang::getAll();
	$default_clang_id = 1;
	foreach ($langs as $lang) {
		$default_clang_id = $lang->getId();
		break;
	}
	
    $this->setConfig('article_id', rex_article::getSiteStartArticleId());
	$this->setConfig('default_lang', $default_clang_id);
	$this->setConfig('default_category_sort', "name");
	$this->setConfig('default_property_sort', "name");
}