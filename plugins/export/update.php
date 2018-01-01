<?php
// 1.1.0 Update database
$sql = rex_sql::factory();
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_immo_export_provider LIKE 'online_status';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_immo_export_provider "
		. "ADD online_status VARCHAR(10) NULL DEFAULT 'online' AFTER media_manager_type;");
}