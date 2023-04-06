<?php

if (rex_version::compare($this->getVersion(), '1.1.2', '<')) { /** @phpstan-ignore-line */
    $sql = rex_sql::factory();
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