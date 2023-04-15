<?php

// Create database
\rex_sql_table::get(\rex::getTable('d2u_immo_export_provider'))
    ->ensureColumn(new rex_sql_column('provider_id', 'INT(11) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('provider_id')
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('type', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', true))
    ->ensureColumn(new \rex_sql_column('company_name', 'VARCHAR(100)', true))
    ->ensureColumn(new \rex_sql_column('company_email', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('customer_number', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('media_manager_type', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('online_status', 'VARCHAR(10)', true))
    ->ensureColumn(new \rex_sql_column('ftp_server', 'VARCHAR(100)', true))
    ->ensureColumn(new \rex_sql_column('ftp_username', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('ftp_password', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('ftp_filename', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('ftp_supports_360_pictures', 'TINYINT(1)', true, '0'))
    ->ensureColumn(new \rex_sql_column('social_app_id', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('social_app_secret', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('social_oauth_token', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('social_oauth_token_secret', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('social_oauth_token_valid_until', 'INT(11)', true))
    ->ensureColumn(new \rex_sql_column('linkedin_email', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('linkedin_groupid', 'VARCHAR(191)', true))
    ->ensure();

\rex_sql_table::get(\rex::getTable('d2u_immo_export_properties'))
    ->ensureColumn(new rex_sql_column('property_id', 'INT(11)', false))
    ->ensureColumn(new \rex_sql_column('provider_id', 'INT(11)', false))
    ->setPrimaryKey(['property_id', 'provider_id'])
    ->ensureColumn(new \rex_sql_column('export_action', 'VARCHAR(10)'))
    ->ensureColumn(new \rex_sql_column('provider_import_id', 'VARCHAR(191)', true))
    ->ensureColumn(new \rex_sql_column('export_timestamp', 'DATETIME', true))
    ->ensure();

// Insert frontend translations
if (!class_exists('export_lang_helper')) {
    // Load class in case addon is deactivated
    require_once 'lib/export_lang_helper.php';
}
export_lang_helper::factory()->install();
