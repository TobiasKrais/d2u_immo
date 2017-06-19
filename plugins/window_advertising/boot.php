<?php
if(rex::isBackend() && is_object(rex::getUser())) {
	rex_perm::register('d2u_immo[window_advertising]', rex_i18n::msg('d2u_immo_window_advertising_rights_all'), rex_perm::OPTIONS);
}