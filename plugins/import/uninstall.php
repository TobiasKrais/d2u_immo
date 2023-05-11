<?php
// Delete Autoexport if activated
if (!class_exists('D2U_Immo::ImportCronjob')) {
    // Load class in case addon is deactivated
    require_once 'lib/ImportCronjob.php';
}
$import_cronjob = \D2U_Immo\ImportCronjob::factory();
if ($import_cronjob->isInstalled()) {
    $import_cronjob->delete();
}
