<?php
$sql = rex_sql::factory();

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_immo_window_advertising');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_immo_window_advertising_lang');

// Delete property extensions
$sql->setQuery('ALTER TABLE ' . rex::getTablePrefix() . 'd2u_immo_properties DROP window_advertising_status;');