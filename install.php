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
	teaser varchar(255) collate utf8_general_ci default NULL,
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

// Create views for url addon
$sql->setQuery('CREATE OR REPLACE VIEW '. rex::getTablePrefix() .'d2u_immo_url_properties AS
	SELECT lang.property_id, lang.clang_id, lang.name, CONCAT(lang.name, " - ", categories.name) AS seo_title, lang.teaser AS seo_description, properties.category_id, lang.updatedate
	FROM '. rex::getTablePrefix() .'d2u_immo_properties_lang AS lang
	LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_properties AS properties ON lang.property_id = properties.property_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_categories_lang AS categories ON properties.category_id = categories.category_id AND lang.clang_id = categories.clang_id
	LEFT JOIN '. rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.status = 1 AND properties.online_status = "online"');
$sql->setQuery('CREATE OR REPLACE VIEW '. rex::getTablePrefix() .'d2u_immo_url_categories AS
	SELECT properties.category_id, categories_lang.clang_id, categories_lang.name, parent_categories.name AS parent_name, CONCAT_WS(" - ", categories_lang.name, parent_categories.name) AS seo_title, categories_lang.teaser AS seo_description, categories_lang.updatedate
	FROM '. rex::getTablePrefix() .'d2u_immo_properties_lang AS lang
	LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_properties AS properties ON lang.property_id = properties.property_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_categories_lang AS categories_lang ON properties.category_id = categories_lang.category_id AND lang.clang_id = categories_lang.clang_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_categories AS categories ON categories_lang.category_id = categories.category_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_categories_lang AS parent_categories ON categories.parent_category_id = parent_categories.category_id AND lang.clang_id = parent_categories.clang_id
	LEFT JOIN '. rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.status = 1 AND properties.online_status = "online"');
// Insert url schemes
if(rex_addon::get('url')->isAvailable()) {
	$sql->setQuery("SELECT * FROM ". rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties'");
	if($sql->getRows() == 0) {
		$sql->setQuery("INSERT INTO `". rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". rex_article::getSiteStartArticleId() .", 0, '', '1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties', '{\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_field_1\":\"name\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_field_2\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_field_3\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_id\":\"property_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_clang_id\":\"clang_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_restriction_field\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_restriction_operator\":\"=\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_restriction_value\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_url_param_key\":\"property_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_seo_title\":\"seo_title\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_seo_description\":\"seo_description\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_sitemap_add\":\"1\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_sitemap_frequency\":\"monthly\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_sitemap_priority\":\"1.0\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_sitemap_lastmod\":\"updatedate\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_path_names\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_path_categories\":\"0\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_relation_field\":\"category_id\"}', '1_xxx_relation_". rex::getTablePrefix() ."d2u_immo_url_categories', '{\"1_xxx_relation_". rex::getTablePrefix() ."d2u_immo_url_categories_field_1\":\"parent_name\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_immo_url_categories_field_2\":\"name\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_immo_url_categories_field_3\":\"\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_immo_url_categories_id\":\"category_id\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_immo_url_categories_clang_id\":\"clang_id\"}', 'before', UNIX_TIMESTAMP(), 'd2u_immo_addon_installer', UNIX_TIMESTAMP(), 'd2u_immo_addon_installer');");
	}
	$sql->setQuery("SELECT * FROM ". rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories'");
	if($sql->getRows() == 0) {
		$sql->setQuery("INSERT INTO `". rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". rex_article::getSiteStartArticleId() .", 0, '', '1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories', '{\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_field_1\":\"parent_name\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_field_2\":\"name\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_field_3\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_id\":\"category_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_clang_id\":\"clang_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_restriction_field\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_restriction_operator\":\"=\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_restriction_value\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_url_param_key\":\"category_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_seo_title\":\"seo_title\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_seo_description\":\"seo_description\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_sitemap_add\":\"1\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_sitemap_frequency\":\"always\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_sitemap_priority\":\"0.7\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_sitemap_lastmod\":\"updatedate\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_path_names\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_path_categories\":\"0\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_relation_field\":\"\"}', '', '[]', 'before', UNIX_TIMESTAMP(), 'd2u_immo_addon_installer', UNIX_TIMESTAMP(), 'd2u_immo_addon_installer');");
	}
}

// TODO: Image Types
// d2u_immo_contact
// d2u_immo_list_tile
// d2u_immo_preview

// Insert frontend translations
d2u_immo_lang_helper::factory()->install();

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