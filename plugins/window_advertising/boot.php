<?php

if (rex::isBackend() && is_object(rex::getUser())) {
    rex_perm::register('d2u_immo[window_advertising]', rex_i18n::msg('d2u_immo_window_advertising_rights_all'), rex_perm::OPTIONS);
}

if (rex::isBackend()) {
    rex_extension::register('CLANG_DELETED', 'rex_d2u_immo_window_advertising_clang_deleted');
}

/**
 * Deletes language specific configurations and objects.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_immo_window_advertising_clang_deleted(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $clang_id = $params['id'];

    // Delete
    $ads = \D2U_Immo\Advertisement::getAll($clang_id, false);
    foreach ($ads as $ad) {
        $ad->delete(false);
    }

    return $warning;
}
