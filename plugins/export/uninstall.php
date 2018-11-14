<?php
$sql = rex_sql::factory();

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_immo_export_provider');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_immo_export_properties');

// Delete Autoexport if activated
if(!class_exists('export_backend_helper')) {
	// Load class in case addon is deactivated
	require_once 'lib/export_backend_helper.php';
}
if(export_backend_helper::autoexportIsInstalled()) {
	export_backend_helper::autoexportDelete();
}