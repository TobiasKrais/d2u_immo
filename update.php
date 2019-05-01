<?php
$sql = rex_sql::factory();
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

	d2u_addon_backend_helper::generateUrlCache();
}

// 1.0.7 update
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_immo_properties LIKE 'price_plus_vat';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE `". \rex::getTablePrefix() ."d2u_immo_properties` ADD `price_plus_vat` TINYINT(1) NOT NULL DEFAULT 0 AFTER `cold_rent`;");
}
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_immo_properties LIKE 'hall_warehouse_type';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE `". \rex::getTablePrefix() ."d2u_immo_properties` ADD `hall_warehouse_type` TINYINT(1) NOT NULL DEFAULT 0 AFTER `office_type`;");
}
// 1.0.8
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_immo_properties LIKE 'parking_type';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE `". \rex::getTablePrefix() ."d2u_immo_properties` ADD `parking_type` TINYINT(1) NOT NULL DEFAULT 0 AFTER `hall_warehouse_type`;");
}
// 1.0.9
$sql->setQuery("ALTER TABLE `". \rex::getTablePrefix() ."d2u_immo_properties` CHANGE `deposit` `deposit` INT(10) NOT NULL DEFAULT 0;");

// 1.1.0 YForm e-mail template
$sql->setQuery("SELECT * FROM ". \rex::getTablePrefix() ."yform_email_template WHERE name = 'd2u_immo_request'");
if($sql->getRows() == 0) {
	$sql->setQuery("INSERT INTO ". \rex::getTablePrefix() ."yform_email_template (`name`, `mail_from`, `mail_from_name`, `subject`, `body`, `body_html`, `attachments`) VALUES
		('d2u_immo_request', 'REX_YFORM_DATA[field=\"email\"]', 'REX_YFORM_DATA[field=\"name\"]', 'Immobilienanfrage', 'Immobilienanfrage von Internetseite:\r\nImmobilie: REX_YFORM_DATA[field=\"immo_name\"]\r\n\r\nEs fragt an:\r\nName: REX_YFORM_DATA[field=\"name\"]\r\nAnschrift: REX_YFORM_DATA[field=\"address\"]\r\nPLZ/Ort: REX_YFORM_DATA[field=\"zip\"] REX_YFORM_DATA[field=\"city\"]\r\nTelefon: REX_YFORM_DATA[field=\"phone\"]\r\nTelefon Anrufe gestattet: <?php print REX_YFORM_DATA[field=\"phone_calls\"] == 1 ? \"Ja\" : \"Nein\"; ?>\r\nEmail: REX_YFORM_DATA[field=\"email\"]\r\nDatenschutzerklärung zugestimmt: <?php print REX_YFORM_DATA[field=\"privacy_policy_accepted\"] == 1 ? \"Ja\" : \"Nein\"; ?>\r\n\r\nNachricht: REX_YFORM_DATA[field=\"message\"]\r\n', '', '')");
}

// Update database to 1.1.1
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_immo_contacts` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_immo_categories` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_immo_categories_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_immo_properties` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_immo_properties_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_immo_properties LIKE 'price_plus_vat';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE `". \rex::getTablePrefix() ."d2u_immo_properties` CHANGE `rent_plus_vat` `price_plus_vat` TINYINT(1) NOT NULL DEFAULT 0;");
}

if (rex_string::versionCompare($this->getVersion(), '1.1.2', '<')) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_immo_properties_lang ADD COLUMN `updatedate_new` DATETIME NOT NULL AFTER `updatedate`;");
	$sql->setQuery("UPDATE ". \rex::getTablePrefix() ."d2u_immo_properties_lang SET `updatedate_new` = FROM_UNIXTIME(`updatedate`);");
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_immo_properties_lang DROP updatedate;");
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_immo_properties_lang CHANGE `updatedate_new` `updatedate` DATETIME NOT NULL;");
}

// Update language replacements
if(!class_exists('d2u_immo_lang_helper')) {
	// Load class in case addon is deactivated
	require_once 'lib/d2u_immo_lang_helper.php';
}
d2u_immo_lang_helper::factory()->install();

// Update modules
if(class_exists('D2UModuleManager')) {
	$modules = [];
	$modules[] = new D2UModule("70-1",
		"D2U Immo Addon - Hauptausgabe",
		17);
	$modules[] = new D2UModule("70-2",
		"D2U Immo Addon - Infobox Ansprechpartner",
		3);
	$modules[] = new D2UModule("70-3",
		"D2U Immo Addon - Ausgabe Kategorie",
		3);

	$d2u_module_manager = new D2UModuleManager($modules, "", "d2u_immo");
	$d2u_module_manager->autoupdate();
}

// remove default lang setting
if (!$this->hasConfig()) {
	$this->removeConfig('default_lang');
}