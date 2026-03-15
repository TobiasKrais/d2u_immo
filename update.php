<?php

$sql = rex_sql::factory();

$tableExists = static function (string $tableName) use ($sql): bool {
    return count($sql->getArray('SHOW TABLES LIKE :table', ['table' => $tableName])) > 0;
};

// Export plugin legacy update
if (rex_version::compare($this->getVersion(), '1.1.2', '<') && $tableExists(rex::getTable('d2u_immo_export_properties'))) { /** @phpstan-ignore-line */
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_immo_export_properties ADD COLUMN `export_timestamp_new` DATETIME NOT NULL AFTER `export_timestamp`;');
    $sql->setQuery('UPDATE '. \rex::getTablePrefix() .'d2u_immo_export_properties SET `export_timestamp_new` = FROM_UNIXTIME(`export_timestamp`);');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_immo_export_properties DROP export_timestamp;');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_immo_export_properties CHANGE `export_timestamp_new` `export_timestamp` DATETIME NOT NULL;');
}

// Window advertising legacy update
if (rex_version::compare($this->getVersion(), '1.1.2', '<') && $tableExists(rex::getTable('d2u_immo_window_advertising_lang'))) { /** @phpstan-ignore-line */
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_immo_window_advertising_lang ADD COLUMN `updatedate_new` DATETIME NOT NULL AFTER `updatedate`;');
    $sql->setQuery('UPDATE '. \rex::getTablePrefix() .'d2u_immo_window_advertising_lang SET `updatedate_new` = FROM_UNIXTIME(`updatedate`);');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_immo_window_advertising_lang DROP updatedate;');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_immo_window_advertising_lang CHANGE `updatedate_new` `updatedate` DATETIME NOT NULL;');
}

// Remove Facebook and Twitter support from export providers
if ($tableExists(rex::getTable('d2u_immo_export_provider'))) {
    \rex_sql_table::get(\rex::getTable('d2u_immo_export_provider'))
        ->removeColumn('facebook_email')
        ->removeColumn('facebook_pageid')
        ->removeColumn('twitter_id')
        ->ensure();
    $sql->setQuery('DELETE FROM `'. rex::getTablePrefix() ."d2u_immo_export_provider` WHERE type = 'facebook' OR type = 'twitter';");
}

if (rex_version::compare($this->getVersion(), '1.1.2', '<')) { /** @phpstan-ignore-line */
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_immo_properties_lang ADD COLUMN `updatedate_new` DATETIME NOT NULL AFTER `updatedate`;');
    $sql->setQuery('UPDATE '. \rex::getTablePrefix() .'d2u_immo_properties_lang SET `updatedate_new` = FROM_UNIXTIME(`updatedate`);');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_immo_properties_lang DROP updatedate;');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_immo_properties_lang CHANGE `updatedate_new` `updatedate` DATETIME NOT NULL;');
}

// remove default lang setting
if ($this->hasConfig('default_lang')) { /** @phpstan-ignore-line */
    $this->removeConfig('default_lang'); /** @phpstan-ignore-line */
}

// use path relative to __DIR__ to get correct path in update temp dir
$this->includeFile(__DIR__.'/install.php'); /** @phpstan-ignore-line */
