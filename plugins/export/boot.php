<?php
if(rex::isBackend() && is_object(rex::getUser())) {
	rex_perm::register('d2u_immo[export]', rex_i18n::msg('d2u_immo_export_rights_export'), rex_perm::OPTIONS);
	rex_perm::register('d2u_immo[export_provider]', rex_i18n::msg('d2u_immo_export_rights_export_provider'));	
}

if(rex::isBackend()) {
	rex_extension::register('CLANG_DELETED', 'rex_d2u_immo_export_clang_deleted');
}

/**
 * Deletes language specific configurations and objects
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 */
function rex_d2u_immo_export_clang_deleted(rex_extension_point $ep) {
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$clang_id = $params['id'];

	// Correct providers
	$providers = Provider::getAll();
	foreach ($providers as $provider) {
		if($provider->clang_id == $clang_id) {
			$provider->clang_id = rex_clang::getStartId();
			$provider->save();
		}
	}

	return $warning;
}
