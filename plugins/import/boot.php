<?php

// download ZIP file if requested
$download_filename = (string) rex_request('download_file', 'string');
if ('' !== $download_filename) {
    $download_filename = rex_path::addonCache('d2u_immo', $download_filename);

    // check if file exists
    if (!file_exists($download_filename)) {
        exit('Datei existiert nicht.');
    }

    // set mime type
    $mime = mime_content_type($download_filename);
    if (false !== $mime) {
        // send http headers
        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . basename($download_filename) . '"');
        header('Content-Length: ' . filesize($download_filename));

        // send file
        readfile($download_filename);
        exit;
    }
} elseif (rex::isBackend() && is_object(rex::getUser())) {
    rex_perm::register('d2u_immo[import]', rex_i18n::msg('d2u_immo_rights_all') .': '. rex_i18n::msg('d2u_immo_import'));
}
