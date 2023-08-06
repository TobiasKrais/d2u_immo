<?php

$sql = rex_sql::factory();

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_immo_export_provider');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_immo_export_properties');

// Delete Autoexport if activated
if (!class_exists('d2u_immo_export_cronjob')) {
    // Load class in case addon is deactivated
    require_once 'lib/d2u_immo_export_cronjob.php';
}
$export_cronjob = d2u_immo_export_cronjob::factory();
if ($export_cronjob->isInstalled()) {
    $export_cronjob->delete();
}

// Delete language replacements
if (!class_exists('export_lang_helper')) {
    // Load class in case addon is deactivated
    require_once 'lib/export_lang_helper.php';
}
export_lang_helper::factory()->uninstall();