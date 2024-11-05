<?php

\rex_sql_table::get(\rex::getTable('d2u_immo_properties'))
->ensureColumn(new \rex_sql_column('openimmo_anid', 'INT(10)'))
->alter();

// Delete Autoexport if activated
if (!class_exists(D2U_Immo\ImportCronjob::class)) {
    // Load class in case addon is deactivated
    require_once 'lib/ImportCronjob.php';
}
$import_cronjob = \D2U_Immo\ImportCronjob::factory();
if ($import_cronjob->isInstalled()) {
    $import_cronjob->delete();
}
