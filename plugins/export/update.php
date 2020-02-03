<?php
// 1.1.0 Update database
$sql = rex_sql::factory();
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_immo_export_provider LIKE 'online_status';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_immo_export_provider "
		. "ADD online_status VARCHAR(10) NULL DEFAULT 'online' AFTER media_manager_type;");
}

// Update database to 1.1.1
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_immo_export_provider` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_immo_export_properties` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

if (rex_string::versionCompare($this->getVersion(), '1.1.2', '<')) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_immo_export_properties ADD COLUMN `export_timestamp_new` DATETIME NOT NULL AFTER `export_timestamp`;");
	$sql->setQuery("UPDATE ". \rex::getTablePrefix() ."d2u_immo_export_properties SET `export_timestamp_new` = FROM_UNIXTIME(`export_timestamp`);");
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_immo_export_properties DROP export_timestamp;");
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_immo_export_properties CHANGE `export_timestamp_new` `export_timestamp` DATETIME NOT NULL;");
}

// 1.1.5 remove Facebook support
\rex_sql_table::get(
    \rex::getTable('d2u_immo_export_provider'))
    ->removeColumn('facebook_email')
    ->removeColumn('facebook_pageid')
    ->ensure();
$sql->setQuery("DELETE FROM `". rex::getTablePrefix() ."d2u_immo_export_provider` WHERE type = 'facebook';");