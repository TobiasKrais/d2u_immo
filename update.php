<?php
$sql = rex_sql::factory();
$sql->setQuery('CREATE OR REPLACE VIEW '. rex::getTablePrefix() .'d2u_immo_url_categories AS
	SELECT properties.category_id, categories_lang.clang_id, CONCAT_WS(" - ", parent_categories.name, categories_lang.name) AS name, CONCAT_WS(" - ", categories_lang.name, parent_categories.name) AS seo_title, categories_lang.teaser AS seo_description, categories_lang.updatedate
	FROM '. rex::getTablePrefix() .'d2u_immo_properties_lang AS lang
	LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_properties AS properties ON lang.property_id = properties.property_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_categories_lang AS categories_lang ON properties.category_id = categories_lang.category_id AND lang.clang_id = categories_lang.clang_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_categories AS categories ON categories_lang.category_id = categories.category_id
	LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_categories_lang AS parent_categories ON categories.parent_category_id = parent_categories.category_id AND lang.clang_id = parent_categories.clang_id
	LEFT JOIN '. rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.status = 1 AND properties.online_status = "online"');
if(\rex_addon::get("url")->isAvailable()) {
	$clang_id = count(rex_clang::getAllIds()) == 1 ? rex_clang::getStartId() : 0;
	$sql->setQuery("SELECT id FROM `". rex::getTablePrefix() ."url_generate` WHERE `table` = '1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties';");
	if($sql->getRows() == 0) {
		$sql_replace->setQuery("DELETE FROM `". rex::getTablePrefix() ."url_generate` WHERE `table` = '1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties';");
		$sql->setQuery("INSERT INTO `". rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". rex_article::getSiteStartArticleId() .", ". $clang_id .", '', '1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties', '{\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_field_1\":\"name\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_field_2\":\"property_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_field_3\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_id\":\"property_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_restriction_field\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_restriction_operator\":\"=\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_restriction_value\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_url_param_key\":\"property_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_seo_title\":\"seo_title\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_seo_description\":\"seo_description\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_seo_image\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_sitemap_add\":\"1\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_sitemap_frequency\":\"monthly\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_sitemap_priority\":\"1.0\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_sitemap_lastmod\":\"updatedate\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_path_names\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_path_categories\":\"0\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties_relation_field\":\"category_id\"}', '1_xxx_relation_". rex::getTablePrefix() ."d2u_immo_url_categories', '{\"1_xxx_relation_". rex::getTablePrefix() ."d2u_immo_url_categories_field_1\":\"name\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_immo_url_categories_field_2\":\"\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_immo_url_categories_field_3\":\"\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_immo_url_categories_id\":\"category_id\",\"1_xxx_relation_". rex::getTablePrefix() ."d2u_immo_url_categories_clang_id\":\"clang_id\"}', 'before', UNIX_TIMESTAMP(), 'd2u_immo_addon_installer', UNIX_TIMESTAMP(), 'd2u_immo_addon_installer');");
	}
	$sql->setQuery("SELECT id FROM `". rex::getTablePrefix() ."url_generate` WHERE `table` = '1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories';");
	if($sql->getRows() == 0) {
		$sql_replace->setQuery("DELETE FROM `". rex::getTablePrefix() ."url_generate` WHERE `table` = '1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories';");
		$sql->setQuery("INSERT INTO `". rex::getTablePrefix() ."url_generate` (`article_id`, `clang_id`, `url`, `table`, `table_parameters`, `relation_table`, `relation_table_parameters`, `relation_insert`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
			(". rex_article::getSiteStartArticleId() .", ". $clang_id .", '', '1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories', '{\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_field_1\":\"name\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_field_2\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_field_3\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_id\":\"category_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_clang_id\":\"". (count(rex_clang::getAllIds()) > 1 ? "clang_id" : "") ."\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_restriction_field\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_restriction_operator\":\"=\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_restriction_value\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_url_param_key\":\"category_id\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_seo_title\":\"seo_title\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_seo_description\":\"seo_description\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_seo_image\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_sitemap_add\":\"1\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_sitemap_frequency\":\"always\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_sitemap_priority\":\"0.7\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_sitemap_lastmod\":\"updatedate\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_path_names\":\"\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_path_categories\":\"0\",\"1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories_relation_field\":\"\"}', '', '[]', 'before', UNIX_TIMESTAMP(), 'd2u_immo_addon_installer', UNIX_TIMESTAMP(), 'd2u_immo_addon_installer');");
	}
	UrlGenerator::generatePathFile([]);
}

// 1.0.7 update
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_immo_properties LIKE 'rent_plus_vat';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE `". \rex::getTablePrefix() ."d2u_immo_properties` ADD `rent_plus_vat` TINYINT(1) NOT NULL DEFAULT 0 AFTER `cold_rent`;");
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
		13);
	$modules[] = new D2UModule("70-2",
		"D2U Immo Addon - Infobox Ansprechpartner",
		2);
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