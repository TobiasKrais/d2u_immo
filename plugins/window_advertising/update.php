<?php

// Update database to 1.1.2
$sql = rex_sql::factory();
if (rex_version::compare($this->getVersion(), '1.1.2', '<')) { /** @phpstan-ignore-line */
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_immo_window_advertising_lang ADD COLUMN `updatedate_new` DATETIME NOT NULL AFTER `updatedate`;');
    $sql->setQuery('UPDATE '. \rex::getTablePrefix() .'d2u_immo_window_advertising_lang SET `updatedate_new` = FROM_UNIXTIME(`updatedate`);');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_immo_window_advertising_lang DROP updatedate;');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_immo_window_advertising_lang CHANGE `updatedate_new` `updatedate` DATETIME NOT NULL;');
}

// use path relative to __DIR__ to get correct path in update temp dir
$this->includeFile(__DIR__.'/install.php'); /** @phpstan-ignore-line */
