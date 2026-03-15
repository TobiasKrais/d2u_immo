<?php

$sql = rex_sql::factory();

// Delete views
$sql->setQuery('DROP VIEW IF EXISTS ' . rex::getTablePrefix() . 'd2u_immo_url_properties');
$sql->setQuery('DROP VIEW IF EXISTS ' . rex::getTablePrefix() . 'd2u_immo_url_categories');

// Delete url schemes
if (\rex_addon::get('url')->isAvailable()) {
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."url_generator_profile WHERE `namespace` = 'property_id';");
    $sql->setQuery('DELETE FROM '. \rex::getTablePrefix() ."url_generator_profile WHERE `namespace` = 'category_id';");
}

// Delete Media Manager media types
$sql->setQuery('DELETE FROM '. rex::getTablePrefix() ."media_manager_type WHERE name LIKE 'd2u_immo%'");
$sql->setQuery('DELETE FROM '. rex::getTablePrefix() ."media_manager_type_effect WHERE createuser = 'd2u_immo'");

// Delete cronjobs
if (class_exists(\TobiasKrais\D2UImmo\ExportCronjob::class)) {
    $export_cronjob = \TobiasKrais\D2UImmo\ExportCronjob::factory();
    if ($export_cronjob->isInstalled()) {
        $export_cronjob->delete();
    }
}
if (class_exists(TobiasKrais\D2UImmo\ImportCronjob::class)) {
    $import_cronjob = TobiasKrais\D2UImmo\ImportCronjob::factory();
    if ($import_cronjob->isInstalled()) {
        $import_cronjob->delete();
    }
}

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_immo_contacts');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_immo_categories');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_immo_categories_lang');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_immo_export_provider');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_immo_export_properties');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_immo_properties');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_immo_properties_lang');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_immo_window_advertising');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_immo_window_advertising_lang');

// Delete language replacements
if (!class_exists(\TobiasKrais\D2UImmo\LangHelper::class)) {
    // Load class in case addon is deactivated
    require_once 'lib/LangHelper.php';
}
\TobiasKrais\D2UImmo\LangHelper::factory()->uninstall();
