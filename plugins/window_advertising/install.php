v<?php
$sql = rex_sql::factory();

// Create database
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_immo_window_advertising (
	ad_id int(10) unsigned NOT NULL auto_increment,
	priority int(10) default 0,
	picture varchar(255) collate utf8mb4_unicode_ci default NULL,
	online_status varchar(10) collate utf8mb4_unicode_ci default 'online',
	PRIMARY KEY (ad_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_immo_window_advertising_lang (
	ad_id int(10) unsigned NOT NULL auto_increment,
	clang_id int(10) NOT NULL,
	title varchar(255) collate utf8mb4_unicode_ci default NULL,
	description text collate utf8mb4_unicode_ci default NULL,
	translation_needs_update varchar(7) collate utf8mb4_unicode_ci default NULL,
	updatedate DATETIME default NULL,
	updateuser varchar(255) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (ad_id, clang_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");

$sql->setQuery("SHOW COLUMNS FROM ". rex::getTablePrefix() ."d2u_immo_properties LIKE 'window_advertising_status';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". rex::getTablePrefix() ."d2u_immo_properties "
		. "ADD window_advertising_status VARCHAR(10) collate utf8mb4_unicode_ci default 'offline' AFTER online_status;");
}