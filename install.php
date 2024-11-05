<?php

// Install database
\rex_sql_table::get(\rex::getTable('d2u_immo_contacts'))
    ->ensureColumn(new rex_sql_column('contact_id', 'INT(11) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('contact_id')
    ->ensureColumn(new \rex_sql_column('firstname', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('lastname', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('company', 'VARCHAR(100)', true))
    ->ensureColumn(new \rex_sql_column('street', 'VARCHAR(100)', true))
    ->ensureColumn(new \rex_sql_column('house_number', 'VARCHAR(5)', true))
    ->ensureColumn(new \rex_sql_column('zip_code', 'VARCHAR(10)', true))
    ->ensureColumn(new \rex_sql_column('city', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('country_code', 'VARCHAR(3)', true))
    ->ensureColumn(new \rex_sql_column('phone', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('fax', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('mobile', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('email', 'VARCHAR(100)', true))
    ->ensureColumn(new \rex_sql_column('picture', 'VARCHAR(191)', true))
    ->ensure();

\rex_sql_table::get(\rex::getTable('d2u_immo_categories'))
    ->ensureColumn(new rex_sql_column('category_id', 'INT(11) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('category_id')
    ->ensureColumn(new \rex_sql_column('priority', 'INT(11)', true))
    ->ensureColumn(new \rex_sql_column('parent_category_id', 'INT(11)', true))
    ->ensureColumn(new \rex_sql_column('picture', 'VARCHAR(191)', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_immo_categories_lang'))
    ->ensureColumn(new rex_sql_column('category_id', 'INT(11)', false))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
    ->setPrimaryKey(['category_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(191)'))
    ->ensureColumn(new \rex_sql_column('teaser', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)', true))
    ->ensureColumn(new \rex_sql_column('updatedate', 'DATETIME', true))
    ->ensureColumn(new \rex_sql_column('updateuser', 'VARCHAR(255)', true))
    ->ensure();

\rex_sql_table::get(\rex::getTable('d2u_immo_properties'))
    ->ensureColumn(new rex_sql_column('property_id', 'INT(11) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('property_id')
    ->ensureColumn(new \rex_sql_column('internal_object_number', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('priority', 'INT(11)', true))
    ->ensureColumn(new \rex_sql_column('contact_id', 'INT(11)', true))
    ->ensureColumn(new \rex_sql_column('category_id', 'INT(11)', true))
    ->ensureColumn(new \rex_sql_column('type_of_use', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('market_type', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('object_type', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('apartment_type', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('house_type', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('land_type', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('office_type', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('hall_warehouse_type', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('parking_type', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('other_type', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('street', 'VARCHAR(100)', true))
    ->ensureColumn(new \rex_sql_column('house_number', 'VARCHAR(5)', true))
    ->ensureColumn(new \rex_sql_column('zip_code', 'VARCHAR(10)', true))
    ->ensureColumn(new \rex_sql_column('city', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('country_code', 'VARCHAR(3)', true))
    ->ensureColumn(new \rex_sql_column('longitude', 'DECIMAL(14,10)', true))
    ->ensureColumn(new \rex_sql_column('latitude', 'DECIMAL(14,10)', true))
    ->ensureColumn(new \rex_sql_column('floor', 'INT(4)', true))
    ->ensureColumn(new \rex_sql_column('publish_address', 'TINYINT(1)', true))
    ->ensureColumn(new \rex_sql_column('purchase_price', 'INT(15)', true))
    ->ensureColumn(new \rex_sql_column('purchase_price_m2', 'INT(15)', true))
    ->ensureColumn(new \rex_sql_column('purchase_price_on_request', 'INT(1)', true))
    ->ensureColumn(new \rex_sql_column('cold_rent', 'INT(15)', true))
    ->ensureColumn(new \rex_sql_column('price_plus_vat', 'TINYINT(1)', true))
    ->ensureColumn(new \rex_sql_column('additional_costs', 'INT(15)', true))
    ->ensureColumn(new \rex_sql_column('deposit', 'INT(10)', true))
    ->ensureColumn(new \rex_sql_column('courtage', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('courtage_incl_vat', 'TINYINT(1)', true))
    ->ensureColumn(new \rex_sql_column('currency_code', 'VARCHAR(3)', true))
    ->ensureColumn(new \rex_sql_column('parking_space_duplex', 'INT(5)', true))
    ->ensureColumn(new \rex_sql_column('parking_space_simple', 'INT(5)', true))
    ->ensureColumn(new \rex_sql_column('parking_space_garage', 'INT(5)', true))
    ->ensureColumn(new \rex_sql_column('parking_space_undergroundcarpark', 'INT(5)', true))
    ->ensureColumn(new \rex_sql_column('living_area', 'DECIMAL(10,2)', true))
    ->ensureColumn(new \rex_sql_column('total_area', 'DECIMAL(10,2)', true))
    ->ensureColumn(new \rex_sql_column('land_area', 'DECIMAL(10,2)', true))
    ->ensureColumn(new \rex_sql_column('rooms', 'DECIMAL(10,2)', true))
    ->ensureColumn(new \rex_sql_column('construction_year', 'INT(5)', true))
    ->ensureColumn(new \rex_sql_column('flat_sharing_possible', 'TINYINT(1)', true))
    ->ensureColumn(new \rex_sql_column('bath', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('kitchen', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('floor_type', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('heating_type', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('firing_type', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('elevator', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('wheelchair_accessable', 'TINYINT(1)', true))
    ->ensureColumn(new \rex_sql_column('cable_sat_tv', 'TINYINT(1)', true))
    ->ensureColumn(new \rex_sql_column('broadband_internet', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('condition_type', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('energy_pass', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('energy_pass_valid_until', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('energy_consumption', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('energy_pass_year', 'VARCHAR(16)', true))
    ->ensureColumn(new \rex_sql_column('including_warm_water', 'TINYINT(1)', true))
    ->ensureColumn(new \rex_sql_column('pictures', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('pictures_360', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('ground_plans', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('location_plans', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('available_from', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('rented', 'TINYINT(1)', true))
    ->ensureColumn(new \rex_sql_column('animals', 'TINYINT(1)', true))
    ->ensureColumn(new \rex_sql_column('object_reserved', 'TINYINT(1)', true))
    ->ensureColumn(new \rex_sql_column('object_sold', 'TINYINT(1)', true))
    ->ensureColumn(new \rex_sql_column('listed_monument', 'TINYINT(1)', true))
    ->ensureColumn(new \rex_sql_column('openimmo_object_id', 'VARCHAR(31)', true))
    ->ensureColumn(new \rex_sql_column('online_status', 'VARCHAR(10)', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_immo_properties_lang'))
    ->ensureColumn(new rex_sql_column('property_id', 'INT(11)', false))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
    ->setPrimaryKey(['property_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(191)'))
    ->ensureColumn(new \rex_sql_column('teaser', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('description', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('description_location', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('description_equipment', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('description_others', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('documents', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)', true))
    ->ensureColumn(new \rex_sql_column('updatedate', 'DATETIME', true))
    ->ensureColumn(new \rex_sql_column('updateuser', 'VARCHAR(255)', true))
    ->ensure();

$sql = rex_sql::factory();
// Bugfix < 1.3.0
$sql->setQuery('UPDATE '. \rex::getTablePrefix() .'d2u_immo_properties SET apartment_type = "ETAGE" WHERE apartment_type = "WETAGE"');

// Create views for url addon
$sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_immo_url_properties AS
	SELECT lang.property_id, lang.clang_id, lang.name, CONCAT(lang.name, " - ", categories.name) AS seo_title, lang.teaser AS seo_description, SUBSTRING_INDEX(properties.pictures, ",", 1) as picture, properties.category_id, lang.updatedate
	FROM '. \rex::getTablePrefix() .'d2u_immo_properties_lang AS lang
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_immo_properties AS properties ON lang.property_id = properties.property_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_immo_categories_lang AS categories ON properties.category_id = categories.category_id AND lang.clang_id = categories.clang_id
	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.`status` = 1 AND properties.online_status = "online"');
$sql->setQuery('CREATE OR REPLACE VIEW '. \rex::getTablePrefix() .'d2u_immo_url_categories AS
	SELECT properties.category_id, categories_lang.clang_id, CONCAT_WS(" - ", parent_categories.name, categories_lang.name) AS name, CONCAT_WS(" - ", categories_lang.name, parent_categories.name) AS seo_title, categories_lang.teaser AS seo_description, categories.picture, categories_lang.updatedate
	FROM '. \rex::getTablePrefix() .'d2u_immo_properties_lang AS lang
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_immo_properties AS properties ON lang.property_id = properties.property_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_immo_categories_lang AS categories_lang ON properties.category_id = categories_lang.category_id AND lang.clang_id = categories_lang.clang_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_immo_categories AS categories ON categories_lang.category_id = categories.category_id
	LEFT JOIN '. \rex::getTablePrefix() .'d2u_immo_categories_lang AS parent_categories ON categories.parent_category_id = parent_categories.category_id AND lang.clang_id = parent_categories.clang_id
	LEFT JOIN '. \rex::getTablePrefix() .'clang AS clang ON lang.clang_id = clang.id
	WHERE clang.`status` = 1 AND properties.online_status = "online"');

// Insert url schemes
if (\rex_addon::get('url')->isAvailable()) {
    $clang_id = 1 === count(rex_clang::getAllIds()) ? rex_clang::getStartId() : 0;
    $article_id = rex_config::get('d2u_immo', 'article_id', 0) > 0 ? rex_config::get('d2u_immo', 'article_id') : rex_article::getSiteStartArticleId();

    // Insert url schemes Version 2.x
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."url_generator_profile WHERE `namespace` = 'property_id';");
    $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() ."url_generator_profile (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
		('property_id', "
        . $article_id .', '
        . $clang_id .', '
        . "'1_xxx_". rex::getTablePrefix() ."d2u_immo_url_properties', "
        . "'{\"column_id\":\"property_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"-\",\"column_segment_part_2\":\"property_id\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"category_id\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"always\",\"sitemap_priority\":\"1.0\",\"column_sitemap_lastmod\":\"updatedate\"}', "
        . "'relation_1_xxx_1_xxx_". rex::getTablePrefix() ."d2u_immo_categories_lang', "
        . "'{\"column_id\":\"category_id\",\"column_clang_id\":\"clang_id\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\"}', "
        . "'', '[]', '', '[]', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."');");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."url_generator_profile WHERE `namespace` = 'category_id';");
    $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() ."url_generator_profile (`namespace`, `article_id`, `clang_id`, `table_name`, `table_parameters`, `relation_1_table_name`, `relation_1_table_parameters`, `relation_2_table_name`, `relation_2_table_parameters`, `relation_3_table_name`, `relation_3_table_parameters`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
		('category_id', "
        . $article_id .', '
        . $clang_id .', '
        . "'1_xxx_". rex::getTablePrefix() ."d2u_immo_url_categories', "
        . "'{\"column_id\":\"category_id\",\"column_clang_id\":\"clang_id\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"seo_title\",\"column_seo_description\":\"seo_description\",\"column_seo_image\":\"picture\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"always\",\"sitemap_priority\":\"0.7\",\"column_sitemap_lastmod\":\"updatedate\"}', "
        . "'', '[]', '', '[]', '', '[]', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."', CURRENT_TIMESTAMP, '". (rex::getUser() instanceof rex_user ? rex::getUser()->getValue('login') : '') ."');");

    \TobiasKrais\D2UHelper\BackendHelper::generateUrlCache();
}

// Media Manager media types
$sql->setQuery('SELECT * FROM '. \rex::getTablePrefix() ."media_manager_type WHERE name = 'd2u_immo_contact'");
if (0 === $sql->getRows()) {
    $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() ."media_manager_type (`status`, `name`, `description`) VALUES
		(1, 'd2u_immo_contact', 'D2U Immobilien Kontaktbild');");
    $last_id = $sql->getLastId();
    $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() .'media_manager_type_effect (`type_id`, `effect`, `parameters`, `priority`, `createdate`, `createuser`) VALUES
		('. $last_id .", 'resize', '{\"rex_effect_convert2img\":{\"rex_effect_convert2img_convert_to\":\"jpg\",\"rex_effect_convert2img_density\":\"100\"},\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"left\",\"rex_effect_crop_vpos\":\"top\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_repeats\":\"\",\"rex_effect_filter_blur_type\":\"\",\"rex_effect_filter_blur_smoothit\":\"\"},\"rex_effect_filter_colorize\":{\"rex_effect_filter_colorize_filter_r\":\"\",\"rex_effect_filter_colorize_filter_g\":\"\",\"rex_effect_filter_colorize_filter_b\":\"\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"\",\"rex_effect_filter_sharpen_radius\":\"\",\"rex_effect_filter_sharpen_threshold\":\"\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"\",\"rex_effect_insert_image_padding_y\":\"\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"400\",\"rex_effect_resize_height\":\"400\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"not_enlarge\"},\"rex_effect_rotate\":{\"rex_effect_rotate_rotate\":\"0\"},\"rex_effect_rounded_corners\":{\"rex_effect_rounded_corners_topleft\":\"\",\"rex_effect_rounded_corners_topright\":\"\",\"rex_effect_rounded_corners_bottomleft\":\"\",\"rex_effect_rounded_corners_bottomright\":\"\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}', 1, CURRENT_TIMESTAMP, 'd2u_immo'),
		(". $last_id .", 'workspace', '{\"rex_effect_convert2img\":{\"rex_effect_convert2img_convert_to\":\"jpg\",\"rex_effect_convert2img_density\":\"100\"},\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"left\",\"rex_effect_crop_vpos\":\"top\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_repeats\":\"\",\"rex_effect_filter_blur_type\":\"\",\"rex_effect_filter_blur_smoothit\":\"\"},\"rex_effect_filter_colorize\":{\"rex_effect_filter_colorize_filter_r\":\"\",\"rex_effect_filter_colorize_filter_g\":\"\",\"rex_effect_filter_colorize_filter_b\":\"\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"\",\"rex_effect_filter_sharpen_radius\":\"\",\"rex_effect_filter_sharpen_threshold\":\"\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"\",\"rex_effect_insert_image_padding_y\":\"\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"\",\"rex_effect_resize_height\":\"\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"enlarge\"},\"rex_effect_rotate\":{\"rex_effect_rotate_rotate\":\"0\"},\"rex_effect_rounded_corners\":{\"rex_effect_rounded_corners_topleft\":\"\",\"rex_effect_rounded_corners_topright\":\"\",\"rex_effect_rounded_corners_bottomleft\":\"\",\"rex_effect_rounded_corners_bottomright\":\"\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"400\",\"rex_effect_workspace_height\":\"400\",\"rex_effect_workspace_hpos\":\"center\",\"rex_effect_workspace_vpos\":\"middle\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"255\",\"rex_effect_workspace_bg_g\":\"255\",\"rex_effect_workspace_bg_b\":\"255\"}}', 2, CURRENT_TIMESTAMP, 'd2u_immo');");
}
$sql->setQuery('SELECT * FROM '. \rex::getTablePrefix() ."media_manager_type WHERE name = 'd2u_immo_list_tile'");
if (0 === $sql->getRows()) {
    $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() ."media_manager_type (`status`, `name`, `description`) VALUES
		(1, 'd2u_immo_list_tile', 'D2U Immobilien Liste Vorschaubild');");
    $last_id = $sql->getLastId();
    $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() .'media_manager_type_effect (`type_id`, `effect`, `parameters`, `priority`, `createdate`, `createuser`) VALUES
		('. $last_id .", 'resize', '{\"rex_effect_convert2img\":{\"rex_effect_convert2img_convert_to\":\"jpg\",\"rex_effect_convert2img_density\":\"100\"},\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"left\",\"rex_effect_crop_vpos\":\"top\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_repeats\":\"\",\"rex_effect_filter_blur_type\":\"\",\"rex_effect_filter_blur_smoothit\":\"\"},\"rex_effect_filter_colorize\":{\"rex_effect_filter_colorize_filter_r\":\"\",\"rex_effect_filter_colorize_filter_g\":\"\",\"rex_effect_filter_colorize_filter_b\":\"\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"\",\"rex_effect_filter_sharpen_radius\":\"\",\"rex_effect_filter_sharpen_threshold\":\"\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"\",\"rex_effect_insert_image_padding_y\":\"\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"768\",\"rex_effect_resize_height\":\"768\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"not_enlarge\"},\"rex_effect_rotate\":{\"rex_effect_rotate_rotate\":\"0\"},\"rex_effect_rounded_corners\":{\"rex_effect_rounded_corners_topleft\":\"\",\"rex_effect_rounded_corners_topright\":\"\",\"rex_effect_rounded_corners_bottomleft\":\"\",\"rex_effect_rounded_corners_bottomright\":\"\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}', 1, CURRENT_TIMESTAMP, 'd2u_immo');");
}

// YForm e-mail template
$sql->setQuery('SELECT * FROM '. \rex::getTablePrefix() ."yform_email_template WHERE name = 'd2u_immo_request'");
if (0 === $sql->getRows()) {
    $sql->setQuery('INSERT INTO '. \rex::getTablePrefix() ."yform_email_template (`name`, `mail_from`, `mail_from_name`, `mail_reply_to`, `mail_reply_to_name`, `subject`, `body`, `body_html`, `attachments`) VALUES
		('d2u_immo_request', '', '', 'REX_YFORM_DATA[field=\"email\"]', 'REX_YFORM_DATA[field=\"name\"]', 'Immobilienanfrage', 'Immobilienanfrage von Internetseite:\r\nImmobilie: REX_YFORM_DATA[field=\"immo_name\"]\r\n\r\nEs fragt an:\r\nName: REX_YFORM_DATA[field=\"name\"]\r\nAnschrift: REX_YFORM_DATA[field=\"address\"]\r\nPLZ/Ort: REX_YFORM_DATA[field=\"zip\"] REX_YFORM_DATA[field=\"city\"]\r\nTelefon: REX_YFORM_DATA[field=\"phone\"]\r\nTelefon Anrufe gestattet: <?php print REX_YFORM_DATA[field=\"phone_calls\"] == 1 ? \"Ja\" : \"Nein\"; ?>\r\nEmail: REX_YFORM_DATA[field=\"email\"]\r\nDatenschutzerklärung zugestimmt: <?php print REX_YFORM_DATA[field=\"privacy_policy_accepted\"] == 1 ? \"Ja\" : \"Nein\"; ?>\r\n\r\nNachricht: REX_YFORM_DATA[field=\"message\"]\r\n', '', '')");
} elseif (rex_version::compare($this->getVersion(), '1.1.4', '<')) { /** @phpstan-ignore-line */
    $sql->setQuery('UPDATE '. \rex::getTablePrefix() ."yform_email_template SET `mail_from` = '', `mail_from_name` = '', `mail_reply_to` = 'REX_YFORM_DATA[field=\"email\"]', `mail_reply_to_name` = 'REX_YFORM_DATA[field=\"vorname\"] REX_YFORM_DATA[field=\"name\"]' WHERE name = 'd2u_immo_request';");
}

// Insert frontend translations
if (class_exists(d2u_immo_lang_helper::class)) {
    d2u_immo_lang_helper::factory()->install();
}

// Standard settings
if (!$this->hasConfig()) { /** @phpstan-ignore-line */
    $this->setConfig('article_id', rex_article::getSiteStartArticleId()); /** @phpstan-ignore-line */
}

// Update language replacements
if (!class_exists(d2u_immo_lang_helper::class)) {
    // Load class in case addon is deactivated
    require_once 'lib/d2u_immo_lang_helper.php';
}
d2u_immo_lang_helper::factory()->install();

// Update modules
if (class_exists(TobiasKrais\D2UHelper\ModuleManager::class)) {
    $modules = [];
    $modules[] = new \TobiasKrais\D2UHelper\Module('70-1',
        'D2U Immo Addon - Hauptausgabe',
        23);
    $modules[] = new \TobiasKrais\D2UHelper\Module('70-2',
        'D2U Immo Addon - Infobox Ansprechpartner',
        5);
    $modules[] = new \TobiasKrais\D2UHelper\Module('70-3',
        'D2U Immo Addon - Ausgabe Kategorie',
        5);

    $d2u_module_manager = new \TobiasKrais\D2UHelper\ModuleManager($modules, '', 'd2u_immo');
    $d2u_module_manager->autoupdate();
}
