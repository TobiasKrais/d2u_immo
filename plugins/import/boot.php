<?php
if (rex::isBackend() && is_object(rex::getUser())) {
    rex_perm::register('d2u_immo[import]', rex_i18n::msg('d2u_immo_rights_all') .': '. rex_i18n::msg('d2u_immo_import'));
}
