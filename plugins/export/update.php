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