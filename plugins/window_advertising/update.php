<?php

$sql = rex_sql::factory();
// Update database to 1.1.1
$sql->setQuery('ALTER TABLE `'. rex::getTablePrefix() .'d2u_immo_window_advertising` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
$sql->setQuery('ALTER TABLE `'. rex::getTablePrefix() .'d2u_immo_window_advertising_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');

// Update database to 1.1.2
if (rex_version::compare($this->getVersion(), '1.1.2', '<')) { /** @phpstan-ignore-line */
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_immo_window_advertising_lang ADD COLUMN `updatedate_new` DATETIME NOT NULL AFTER `updatedate`;');
    $sql->setQuery('UPDATE '. \rex::getTablePrefix() .'d2u_immo_window_advertising_lang SET `updatedate_new` = FROM_UNIXTIME(`updatedate`);');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_immo_window_advertising_lang DROP updatedate;');
    $sql->setQuery('ALTER TABLE '. \rex::getTablePrefix() .'d2u_immo_window_advertising_lang CHANGE `updatedate_new` `updatedate` DATETIME NOT NULL;');
}
