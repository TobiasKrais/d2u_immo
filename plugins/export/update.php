<?php

$sql = rex_sql::factory();
if (rex_version::compare($this->getVersion(), '1.1.2', '<')) { /** @phpstan-ignore-line */
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_immo_export_properties ADD COLUMN `export_timestamp_new` DATETIME NOT NULL AFTER `export_timestamp`;');
    $sql->setQuery('UPDATE '. \rex::getTablePrefix() .'d2u_immo_export_properties SET `export_timestamp_new` = FROM_UNIXTIME(`export_timestamp`);');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_immo_export_properties DROP export_timestamp;');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_immo_export_properties CHANGE `export_timestamp_new` `export_timestamp` DATETIME NOT NULL;');
}

// Remove Facebook and Twitter support
\rex_sql_table::get(\rex::getTable('d2u_immo_export_provider'))
    ->removeColumn('facebook_email')
    ->removeColumn('facebook_pageid')
    ->removeColumn('twitter_id')
    ->ensure();
$sql->setQuery('DELETE FROM `'. rex::getTablePrefix() ."d2u_immo_export_provider` WHERE type = 'facebook' OR type = 'twitter';");

// use path relative to __DIR__ to get correct path in update temp dir
$this->includeFile(__DIR__.'/install.php'); /** @phpstan-ignore-line */
