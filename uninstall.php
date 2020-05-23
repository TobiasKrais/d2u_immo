<?php
$sql = rex_sql::factory();

// Delete views
$sql->setQuery('DROP VIEW IF EXISTS ' . rex::getTablePrefix() . 'd2u_immo_url_properties');
$sql->setQuery('DROP VIEW IF EXISTS ' . rex::getTablePrefix() . 'd2u_immo_url_categories');

// Delete url schemes
if(\rex_addon::get('url')->isAvailable()) {
	if(rex_version::compare(\rex_addon::get('url')->getVersion(), '1.5', '>=')) {
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."url_generator_profile WHERE `namespace` = 'property_id';");
		$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."url_generator_profile WHERE `namespace` = 'category_id';");		
	}
	else {
		$sql->setQuery("DELETE FROM `". rex::getTablePrefix() ."url_generate` WHERE `table` LIKE '%d2u_immo_url_%'");
	}
}

// Delete Media Manager media types
$sql->setQuery("DELETE FROM ". rex::getTablePrefix() ."media_manager_type WHERE name LIKE 'd2u_immo%'");
$sql->setQuery("DELETE FROM ". rex::getTablePrefix() ."media_manager_type_effect WHERE createuser = 'd2u_immo'");

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_immo_contacts');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_immo_categories');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_immo_categories_lang');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_immo_properties');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_immo_properties_lang');

// Delete language replacements
if(!class_exists('d2u_immo_lang_helper')) {
	// Load class in case addon is deactivated
	require_once 'lib/d2u_immo_lang_helper.php';
}
d2u_immo_lang_helper::factory()->uninstall();