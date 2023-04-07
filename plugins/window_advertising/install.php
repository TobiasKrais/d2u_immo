<?php

// Create database
\rex_sql_table::get(\rex::getTable('d2u_immo_window_advertising'))
    ->ensureColumn(new rex_sql_column('ad_id', 'INT(11) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('ad_id')
    ->ensureColumn(new \rex_sql_column('priority', 'INT(11)', true))
    ->ensureColumn(new \rex_sql_column('picture', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('online_status', 'VARCHAR(10)', true))
    ->ensure();

\rex_sql_table::get(\rex::getTable('d2u_immo_window_advertising_lang'))
    ->ensureColumn(new rex_sql_column('ad_id', 'INT(11)', false))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
    ->setPrimaryKey(['ad_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('title', 'VARCHAR(191)'))
    ->ensureColumn(new \rex_sql_column('description', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)', true))
    ->ensureColumn(new \rex_sql_column('updatedate', 'DATETIME', true))
    ->ensureColumn(new \rex_sql_column('updateuser', 'VARCHAR(191)', true))
    ->ensure();

\rex_sql_table::get(rex::getTable('d2u_immo_properties'))
    ->ensureColumn(new \rex_sql_column('window_advertising_status', 'VARCHAR(10)', true, 'offline'))
    ->alter();
