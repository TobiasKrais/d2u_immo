<?php
$sql = rex_sql::factory();
// Update database to 1.1.1
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_immo_window_advertising` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_immo_window_advertising_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");