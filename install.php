<?php
$sql = rex_sql::factory();
// Install database
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_immo_contacts (
	contact_id int(10) unsigned NOT NULL auto_increment,
	firstname varchar(50) collate utf8mb4_unicode_ci default NULL,
	lastname varchar(50) collate utf8mb4_unicode_ci NOT NULL,
	company varchar(100) collate utf8mb4_unicode_ci default NULL,
	street varchar(100) collate utf8mb4_unicode_ci default NULL,
	house_number varchar(5) collate utf8mb4_unicode_ci default NULL,
	zip_code varchar(10) collate utf8mb4_unicode_ci default NULL,
	city varchar(255) collate utf8mb4_unicode_ci default NULL,
	country_code varchar(3) collate utf8mb4_unicode_ci default NULL,
	phone varchar(50) collate utf8mb4_unicode_ci default NULL,
	fax varchar(50) collate utf8mb4_unicode_ci default NULL,
	mobile varchar(50) collate utf8mb4_unicode_ci default NULL,
	email varchar(255) collate utf8mb4_unicode_ci default NULL,
	picture varchar(255) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (contact_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");

$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_immo_categories (
	category_id int(10) unsigned NOT NULL auto_increment,
	priority int(10) default NULL,
	parent_category_id int(10) default NULL,
	picture varchar(255) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (category_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_immo_categories_lang (
	category_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8mb4_unicode_ci default NULL,
	teaser varchar(255) collate utf8mb4_unicode_ci default NULL,
	translation_needs_update varchar(7) collate utf8mb4_unicode_ci default NULL,
	updatedate DATETIME default NULL,
	updateuser varchar(255) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (category_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");

$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_immo_properties (
	property_id int(10) unsigned NOT NULL auto_increment,
	internal_object_number varchar(255) collate utf8mb4_unicode_ci default NULL,
	priority int(10) default 0,
	contact_id int(10) default NULL,
	category_id int(10) default NULL,
	type_of_use varchar(255) collate utf8mb4_unicode_ci default NULL,
	market_type varchar(255) collate utf8mb4_unicode_ci default NULL,
	object_type varchar(255) collate utf8mb4_unicode_ci default NULL,
	apartment_type varchar(255) collate utf8mb4_unicode_ci default NULL,
	house_type varchar(255) collate utf8mb4_unicode_ci default NULL,
	land_type varchar(255) collate utf8mb4_unicode_ci default NULL,
	office_type varchar(255) collate utf8mb4_unicode_ci default NULL,
	hall_warehouse_type varchar(255) collate utf8mb4_unicode_ci default NULL,
	parking_type varchar(255) collate utf8mb4_unicode_ci default NULL,
	other_type varchar(255) collate utf8mb4_unicode_ci default NULL,
	street varchar(100) collate utf8mb4_unicode_ci default NULL,
	house_number varchar(5) collate utf8mb4_unicode_ci default NULL,
	zip_code varchar(10) collate utf8mb4_unicode_ci NOT NULL,
	city varchar(255) collate utf8mb4_unicode_ci NOT NULL,
	country_code varchar(3) collate utf8mb4_unicode_ci default NULL,
	longitude varchar(20) collate utf8mb4_unicode_ci default NULL,
	latitude varchar(20) collate utf8mb4_unicode_ci default NULL,
	floor int(4) default NULL,
	publish_address int(1) default 0,
	purchase_price int(15) default 0,
	purchase_price_m2 int(15) default 0,
	cold_rent int(15) default 0,
	price_plus_vat int(1) default 0,
	additional_costs int(15) default 0,
	deposit int(10) collate utf8mb4_unicode_ci default NULL,
	courtage varchar(255) collate utf8mb4_unicode_ci,
	courtage_incl_vat int(1) default 0,
	currency_code varchar(3) collate utf8mb4_unicode_ci DEFAULT 'EUR',
	parking_space_duplex int(5) default 0,
	parking_space_simple int(5) default 0,
	parking_space_garage int(5) default 0,
	parking_space_undergroundcarpark int(5) default 0,
	living_area decimal(10,2) default '0.00',
	total_area decimal(10,2) default '0.00',
	land_area decimal(10,2) default '0.00',
	rooms decimal(10,2) default '0.00',
	construction_year int(5) default 0,
	flat_sharing_possible int(1) default 0,
	bath varchar(255) collate utf8mb4_unicode_ci default NULL,
	kitchen varchar(255) collate utf8mb4_unicode_ci default NULL,
	floor_type varchar(255) collate utf8mb4_unicode_ci default NULL,
	heating_type varchar(255) collate utf8mb4_unicode_ci default NULL,
	firing_type varchar(255) collate utf8mb4_unicode_ci default NULL,
	elevator varchar(255) collate utf8mb4_unicode_ci default NULL,
	wheelchair_accessable int(1) default 0,
	cable_sat_tv int(1) default 0,
	broadband_internet varchar(255) collate utf8mb4_unicode_ci default NULL,
	condition_type varchar(255) collate utf8mb4_unicode_ci default NULL,
	energy_pass varchar(255) collate utf8mb4_unicode_ci default NULL,
	energy_pass_valid_until varchar(50) collate utf8mb4_unicode_ci default NULL,
	energy_consumption varchar(255) collate utf8mb4_unicode_ci default NULL,
	including_warm_water int(1) default 0,
	pictures text collate utf8mb4_unicode_ci,
	ground_plans text collate utf8mb4_unicode_ci,
	location_plans text collate utf8mb4_unicode_ci,
	available_from varchar(255) collate utf8mb4_unicode_ci default NULL,
	rented int(1) default 0,
	animals int(1) default 0,
	object_reserved int(1) default 0,
	object_sold int(1) default 0,
	openimmo_object_id varchar(31) collate utf8mb4_unicode_ci default NULL,
	online_status varchar(10) collate utf8mb4_unicode_ci default 'online',
	PRIMARY KEY (property_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_immo_properties_lang (
	property_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8mb4_unicode_ci default NULL,
	teaser varchar(255) collate utf8mb4_unicode_ci default NULL,
	description text collate utf8mb4_unicode_ci default NULL,
	description_location text collate utf8mb4_unicode_ci default NULL,
	description_equipment text collate utf8mb4_unicode_ci default NULL,
	description_others text collate utf8mb4_unicode_ci default NULL,
	documents text collate utf8mb4_unicode_ci,
	translation_needs_update varchar(7) collate utf8mb4_unicode_ci default NULL,
	updatedate DATETIME default NULL,
	updateuser varchar(255) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (property_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");

// Create views for url addon
$sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_immo_url_properties AS
	SELECT lang.property_id, lang.clang_id, lang.name, CONCAT(lang.name, " - ", categories.name) AS seo_title, lang.teaser AS seo_description, SUBSTRING_INDEX(properties.pictures, ",", 1) as picture,  properties.category_id, lang.updatedate
	FROM '. \rex::getTablePrefix() .'d2u_immo_properties_lang AS lang
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_immo_properties AS properties ON lang.property_id = properties.property_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_immo_categories_lang AS categories ON properties.category_id = categories.category_id AND lang.clang_id = categories.clang_id
	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.status = 1 AND properties.online_status = "online"');
$sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_immo_url_categories AS
	SELECT properties.category_id, categories_lang.clang_id, CONCAT_WS(" - ", parent_categories.name, categories_lang.name) AS name, CONCAT_WS(" - ", categories_lang.name, parent_categories.name) AS seo_title, categories_lang.teaser AS seo_description, categories.picture, categories_lang.updatedate
	FROM '. \rex::getTablePrefix() .'d2u_immo_properties_lang AS lang
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_immo_properties AS properties ON lang.property_id = properties.property_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_immo_categories_lang AS categories_lang ON properties.category_id = categories_lang.category_id AND lang.clang_id = categories_lang.clang_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_immo_categories AS categories ON categories_lang.category_id = categories.category_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_immo_categories_lang AS parent_categories ON categories.parent_category_id = parent_categories.category_id AND lang.clang_id = parent_categories.clang_id
	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.status = 1 AND properties.online_status = "online"');

// Insert url schemes
if(\rex_addon::get('url')->isAvailable()) {
	$clang_id = count(rex_clang::getAllIds()) == 1 ? rex_clang::getStartId() : 0;
	$article_id = rex_config::get('d2u_immo', 'article_id', 0) > 0 ? rex_config::get('d2u_immo', 'article_id') : rex_article::getSiteStartArticleId(); 
	if(rex_string::versionCompare(\rex_addon::get('url')->getVersion(), '1.5', '>=')) {
		// Insert url schemes Version 2.x
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."url_generator_profile WHERE `namespace` = 'property_id';");
		$sql->setQuery("INSERT INTO ". \rex::getTablePrefix() ."url_generator_profile (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			('property_id', "
			. $article_id .", "
			. $clang_id .", "
			. "'1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties', "
			. "'{\"column_id\":\"property_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"-\",\"column_segment_part_2\":\"property_id\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"category_id\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"always\",\"sitemap_priority\":\"1.0\",\"column_sitemap_lastmod\":\"updatedate\"}', "
			. "'relation_1_xxx_1_xxx_". rex::getTablePrefix() ."d2u_immo_categories_lang', "
			. "'{\"column_id\":\"category_id\",\"column_clang_id\":\"clang_id\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\"}', "
			. "'', '[]', '', '[]', CURRENT_TIMESTAMP, '". rex::getUser()->getValue('login') ."', CURRENT_TIMESTAMP, '". rex::getUser()->getValue('login') ."');");
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."url_generator_profile WHERE `namespace` = 'category_id';");
		$sql->setQuery("INSERT INTO ". \rex::getTablePrefix() ."url_generator_profile (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			('category_id', "
			. $article_id .", "
			. $clang_id .", "
			. "'1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories', "
			. "'{\"column_id\":\"category_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"always\",\"sitemap_priority\":\"0.7\",\"column_sitemap_lastmod\":\"updatedate\"}', "
			. "'', '[]', '', '[]', '', '[]', CURRENT_TIMESTAMP, '". rex::getUser()->getValue('login') ."', CURRENT_TIMESTAMP, '". rex::getUser()->getValue('login') ."');");
	}
	else {
		// Insert url schemes Version 1.x
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_properties';");
		$sql->setQuery("INSERT INTO `". \rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". $article_id .", "
			. $clang_id .", "
			. "'', "
			. "'1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_properties', "
			. "'{\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_properties_field_1\":\"name\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_properties_field_2\":\"property_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_properties_field_3\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_properties_id\":\"property_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_properties_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_properties_restriction_field\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_properties_restriction_operator\":\"=\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_properties_restriction_value\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_properties_url_param_key\":\"property_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_properties_seo_title\":\"seo_title\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_properties_seo_description\":\"seo_description\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_properties_seo_image\":\"picture\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_properties_sitemap_add\":\"1\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_properties_sitemap_frequency\":\"monthly\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_properties_sitemap_priority\":\"1.0\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_properties_sitemap_lastmod\":\"updatedate\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_properties_path_names\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_properties_path_categories\":\"0\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_properties_relation_field\":\"category_id\"}', "
			. "'1_xxx_relation_". \rex::getTablePrefix() ."d2u_immo_url_categories', "
			. "'{\"1_xxx_relation_". \rex::getTablePrefix() ."d2u_immo_url_categories_field_1\":\"name\",\"1_xxx_relation_". \rex::getTablePrefix() ."d2u_immo_url_categories_field_2\":\"\",\"1_xxx_relation_". \rex::getTablePrefix() ."d2u_immo_url_categories_field_3\":\"\",\"1_xxx_relation_". \rex::getTablePrefix() ."d2u_immo_url_categories_id\":\"category_id\",\"1_xxx_relation_". \rex::getTablePrefix() ."d2u_immo_url_categories_clang_id\":\"clang_id\"}', "
			. "'before', UNIX_TIMESTAMP(), '". rex::getUser()->getValue('login') ."', UNIX_TIMESTAMP(), '". rex::getUser()->getValue('login') ."');");
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."url_generate WHERE `table` = '1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_categories';");
		$sql->setQuery("INSERT INTO `". \rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". $article_id .", "
			. $clang_id .", "
			. "'', "
			. "'1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_categories', "
			. "'{\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_categories_field_1\":\"name\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_categories_field_2\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_categories_field_3\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_categories_id\":\"category_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_categories_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_categories_restriction_field\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_categories_restriction_operator\":\"=\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_categories_restriction_value\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_categories_url_param_key\":\"category_id\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_categories_seo_title\":\"seo_title\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_categories_seo_description\":\"seo_description\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_categories_seo_image\":\"picture\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_categories_sitemap_add\":\"1\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_categories_sitemap_frequency\":\"always\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_categories_sitemap_priority\":\"0.7\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_categories_sitemap_lastmod\":\"updatedate\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_categories_path_names\":\"\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_categories_path_categories\":\"0\",\"1_xxx_". \rex::getTablePrefix() ."d2u_immo_url_categories_relation_field\":\"\"}', "
			. "'', '[]', 'before', UNIX_TIMESTAMP(), '". rex::getUser()->getValue('login') ."', UNIX_TIMESTAMP(), '". rex::getUser()->getValue('login') ."');");
	}

	\d2u_addon_backend_helper::generateUrlCache();
	\d2u_addon_backend_helper::update_searchit_url_index();
}

// Media Manager media types
$sql->setQuery("SELECT * FROM ". \rex::getTablePrefix() ."media_manager_type WHERE name = 'd2u_immo_contact'");
if($sql->getRows() == 0) {
	$sql->setQuery("INSERT INTO ". \rex::getTablePrefix() ."media_manager_type (`status`, `name`, `description`) VALUES
		(1, 'd2u_immo_contact', 'D2U Immobilien Kontaktbild');");
	$last_id = $sql->getLastId();
	$sql->setQuery("INSERT INTO ". \rex::getTablePrefix() ."media_manager_type_effect (`type_id`, `effect`, `parameters`, `priority`, `createdate`, `createuser`) VALUES
		(". $last_id .", 'resize', '{\"rex_effect_convert2img\":{\"rex_effect_convert2img_convert_to\":\"jpg\",\"rex_effect_convert2img_density\":\"100\"},\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"left\",\"rex_effect_crop_vpos\":\"top\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_repeats\":\"\",\"rex_effect_filter_blur_type\":\"\",\"rex_effect_filter_blur_smoothit\":\"\"},\"rex_effect_filter_colorize\":{\"rex_effect_filter_colorize_filter_r\":\"\",\"rex_effect_filter_colorize_filter_g\":\"\",\"rex_effect_filter_colorize_filter_b\":\"\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"\",\"rex_effect_filter_sharpen_radius\":\"\",\"rex_effect_filter_sharpen_threshold\":\"\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"\",\"rex_effect_insert_image_padding_y\":\"\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"400\",\"rex_effect_resize_height\":\"400\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"not_enlarge\"},\"rex_effect_rotate\":{\"rex_effect_rotate_rotate\":\"0\"},\"rex_effect_rounded_corners\":{\"rex_effect_rounded_corners_topleft\":\"\",\"rex_effect_rounded_corners_topright\":\"\",\"rex_effect_rounded_corners_bottomleft\":\"\",\"rex_effect_rounded_corners_bottomright\":\"\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}', 1, CURRENT_TIMESTAMP, 'd2u_immo'),
		(". $last_id .", 'workspace', '{\"rex_effect_convert2img\":{\"rex_effect_convert2img_convert_to\":\"jpg\",\"rex_effect_convert2img_density\":\"100\"},\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"left\",\"rex_effect_crop_vpos\":\"top\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_repeats\":\"\",\"rex_effect_filter_blur_type\":\"\",\"rex_effect_filter_blur_smoothit\":\"\"},\"rex_effect_filter_colorize\":{\"rex_effect_filter_colorize_filter_r\":\"\",\"rex_effect_filter_colorize_filter_g\":\"\",\"rex_effect_filter_colorize_filter_b\":\"\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"\",\"rex_effect_filter_sharpen_radius\":\"\",\"rex_effect_filter_sharpen_threshold\":\"\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"\",\"rex_effect_insert_image_padding_y\":\"\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"\",\"rex_effect_resize_height\":\"\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"enlarge\"},\"rex_effect_rotate\":{\"rex_effect_rotate_rotate\":\"0\"},\"rex_effect_rounded_corners\":{\"rex_effect_rounded_corners_topleft\":\"\",\"rex_effect_rounded_corners_topright\":\"\",\"rex_effect_rounded_corners_bottomleft\":\"\",\"rex_effect_rounded_corners_bottomright\":\"\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"400\",\"rex_effect_workspace_height\":\"400\",\"rex_effect_workspace_hpos\":\"center\",\"rex_effect_workspace_vpos\":\"middle\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"255\",\"rex_effect_workspace_bg_g\":\"255\",\"rex_effect_workspace_bg_b\":\"255\"}}', 2, CURRENT_TIMESTAMP, 'd2u_immo');");
}
$sql->setQuery("SELECT * FROM ". \rex::getTablePrefix() ."media_manager_type WHERE name = 'd2u_immo_list_tile'");
if($sql->getRows() == 0) {
	$sql->setQuery("INSERT INTO ". \rex::getTablePrefix() ."media_manager_type (`status`, `name`, `description`) VALUES
		(1, 'd2u_immo_list_tile', 'D2U Immobilien Liste Vorschaubild');");
	$last_id = $sql->getLastId();
	$sql->setQuery("INSERT INTO ". \rex::getTablePrefix() ."media_manager_type_effect (`type_id`, `effect`, `parameters`, `priority`, `createdate`, `createuser`) VALUES
		(". $last_id .", 'resize', '{\"rex_effect_convert2img\":{\"rex_effect_convert2img_convert_to\":\"jpg\",\"rex_effect_convert2img_density\":\"100\"},\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"left\",\"rex_effect_crop_vpos\":\"top\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_repeats\":\"\",\"rex_effect_filter_blur_type\":\"\",\"rex_effect_filter_blur_smoothit\":\"\"},\"rex_effect_filter_colorize\":{\"rex_effect_filter_colorize_filter_r\":\"\",\"rex_effect_filter_colorize_filter_g\":\"\",\"rex_effect_filter_colorize_filter_b\":\"\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"\",\"rex_effect_filter_sharpen_radius\":\"\",\"rex_effect_filter_sharpen_threshold\":\"\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"\",\"rex_effect_insert_image_padding_y\":\"\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"768\",\"rex_effect_resize_height\":\"768\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"not_enlarge\"},\"rex_effect_rotate\":{\"rex_effect_rotate_rotate\":\"0\"},\"rex_effect_rounded_corners\":{\"rex_effect_rounded_corners_topleft\":\"\",\"rex_effect_rounded_corners_topright\":\"\",\"rex_effect_rounded_corners_bottomleft\":\"\",\"rex_effect_rounded_corners_bottomright\":\"\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}', 1, CURRENT_TIMESTAMP, 'd2u_immo');");
}

// YForm e-mail template
$sql->setQuery("SELECT * FROM ". \rex::getTablePrefix() ."yform_email_template WHERE name = 'd2u_immo_request'");
if($sql->getRows() == 0) {
	$sql->setQuery("INSERT INTO ". \rex::getTablePrefix() ."yform_email_template (`name`, `mail_from`, `mail_from_name`, `mail_reply_to`, `mail_reply_to_name`, `subject`, `body`, `body_html`, `attachments`) VALUES
		('d2u_immo_request', '', '', 'REX_YFORM_DATA[field=\"email\"]', 'REX_YFORM_DATA[field=\"name\"]', 'Immobilienanfrage', 'Immobilienanfrage von Internetseite:\r\nImmobilie: REX_YFORM_DATA[field=\"immo_name\"]\r\n\r\nEs fragt an:\r\nName: REX_YFORM_DATA[field=\"name\"]\r\nAnschrift: REX_YFORM_DATA[field=\"address\"]\r\nPLZ/Ort: REX_YFORM_DATA[field=\"zip\"] REX_YFORM_DATA[field=\"city\"]\r\nTelefon: REX_YFORM_DATA[field=\"phone\"]\r\nTelefon Anrufe gestattet: <?php print REX_YFORM_DATA[field=\"phone_calls\"] == 1 ? \"Ja\" : \"Nein\"; ?>\r\nEmail: REX_YFORM_DATA[field=\"email\"]\r\nDatenschutzerklärung zugestimmt: <?php print REX_YFORM_DATA[field=\"privacy_policy_accepted\"] == 1 ? \"Ja\" : \"Nein\"; ?>\r\n\r\nNachricht: REX_YFORM_DATA[field=\"message\"]\r\n', '', '')");
}

// Insert frontend translations
if(class_exists('d2u_immo_lang_helper')) {
	d2u_immo_lang_helper::factory()->install();
}

// Standard settings
if (!$this->hasConfig()) {
    $this->setConfig('article_id', rex_article::getSiteStartArticleId());
	$this->setConfig('default_category_sort', "name");
	$this->setConfig('default_property_sort', "name");
	$this->setConfig('finance_calculator_real_estate_tax', "0.05");
	$this->setConfig('finance_calculator_notary_costs', "0.015");
	$this->setConfig('finance_calculator_interest_rate', "0.018");
	$this->setConfig('finance_calculator_repayment', "0.02");
}