<?php
if(\rex::isBackend() && is_object(\rex::getUser())) {
	rex_perm::register('d2u_immo[]', rex_i18n::msg('d2u_immo_rights_all'));
	rex_perm::register('d2u_immo[edit_lang]', rex_i18n::msg('d2u_immo_rights_edit_lang'), rex_perm::OPTIONS);
	rex_perm::register('d2u_immo[edit_data]', rex_i18n::msg('d2u_immo_rights_edit_data'), rex_perm::OPTIONS);
	rex_perm::register('d2u_immo[settings]', rex_i18n::msg('d2u_immo_rights_settings'), rex_perm::OPTIONS);	
}

if(\rex::isBackend()) {
	rex_extension::register('CLANG_DELETED', 'rex_d2u_immo_clang_deleted');
	rex_extension::register('MEDIA_IS_IN_USE', 'rex_d2u_immo_media_is_in_use');
	rex_extension::register('ART_PRE_DELETED', 'rex_d2u_immo_article_is_in_use');
}

/**
 * Checks if article is used by this addon
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 * @throws rex_api_exception If article is used
 */
function rex_d2u_immo_article_is_in_use(rex_extension_point $ep) {
	$warning = [];
	$params = $ep->getParams();
	$article_id = $params['id'];

	// Prepare warnings
	// Settings
	$addon = rex_addon::get("d2u_immo");
	if($addon->hasConfig("article_id") && $addon->getConfig("article_id") == $article_id) {
		$message = '<a href="index.php?page=d2u_immo/settings">'.
			 rex_i18n::msg('d2u_immo_rights_all') ." - ". rex_i18n::msg('d2u_immo_meta_settings') . '</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
	}

	if(count($warning) > 0) {
		throw new rex_api_exception(rex_i18n::msg('d2u_helper_rex_article_cannot_delete') ."<ul><li>". implode("</li><li>", $warning) ."</li></ul>");
	}
	else {
		return "";
	}
}

/**
 * Deletes language specific configurations and objects
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 */
function rex_d2u_immo_clang_deleted(rex_extension_point $ep) {
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$clang_id = $params['id'];

	// Delete
	$categories = D2U_Immo\Category::getAll($clang_id);
	foreach ($categories as $category) {
		$category->delete(FALSE);
	}
	$properties = D2U_Immo\Property::getAll($clang_id, '', FALSE);
	foreach ($properties as $property) {
		$property->delete(FALSE);
	}
	
	// Delete language settings
	if(rex_config::has('d2u_immo', 'lang_replacement_'. $clang_id)) {
		rex_config::remove('d2u_immo', 'lang_replacement_'. $clang_id);
	}
	// Delete language replacements
	d2u_immo_lang_helper::factory()->uninstall($clang_id);

	return $warning;
}

/**
 * Checks if media is used by this addon
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 */
function rex_d2u_immo_media_is_in_use(rex_extension_point $ep) {
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$filename = addslashes($params['filename']);

	// Contacts
	$sql_contacts = rex_sql::factory();
	$sql_contacts->setQuery('SELECT contact_id, firstname, lastname FROM `' . \rex::getTablePrefix() . 'd2u_immo_contacts` '
		.'WHERE picture = "'. $filename .'" ');
	
	// Categories
	$sql_categories = rex_sql::factory();
	$sql_categories->setQuery('SELECT lang.category_id, name FROM `' . \rex::getTablePrefix() . 'd2u_immo_categories_lang` AS lang '
		.'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_immo_categories` AS categories ON lang.category_id = categories.category_id '
		.'WHERE picture = "'. $filename .'" ');  

	// Properties
	$sql_properties = rex_sql::factory();
	$sql_properties->setQuery('SELECT lang.property_id, name FROM `' . \rex::getTablePrefix() . 'd2u_immo_properties_lang` AS lang '
		.'LEFT JOIN `' . \rex::getTablePrefix() . 'd2u_immo_properties` AS properties ON lang.property_id = properties.property_id '
		.'WHERE FIND_IN_SET("'. $filename .'", pictures) OR FIND_IN_SET("'. $filename .'", ground_plans) OR FIND_IN_SET("'. $filename .'", location_plans) OR FIND_IN_SET("'. $filename .'", documents)');

	// Prepare warnings
	// Categories
	for($i = 0; $i < $sql_categories->getRows(); $i++) {
		$message = '<a href="javascript:openPage(\'index.php?page=d2u_immo/category&func=edit&entry_id='. $sql_categories->getValue('category_id') .'\')">'.
			 rex_i18n::msg('d2u_immo_rights_all') ." - ". rex_i18n::msg('d2u_helper_categories') .': '. $sql_categories->getValue('name') . '</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
    }
	
	// Contacts
	for($i = 0; $i < $sql_contacts->getRows(); $i++) {
		$message = '<a href="javascript:openPage(\'index.php?page=d2u_immo/contact&func=edit&entry_id='.
			$sql_contacts->getValue('contact_id') .'\')">'. rex_i18n::msg('d2u_immo_rights_all') ." - ". rex_i18n::msg('d2u_immo_contacts') .': '. $sql_contacts->getValue('firstname') .' '. $sql_contacts->getValue('lastname') .'</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
    }

	// Properties
	for($i = 0; $i < $sql_properties->getRows(); $i++) {
		$message = '<a href="javascript:openPage(\'index.php?page=d2u_immo/property&func=edit&entry_id='.
			$sql_properties->getValue('property_id') .'\')">'. rex_i18n::msg('d2u_immo_rights_all') ." - ". rex_i18n::msg('d2u_immo_properties') .': '. $sql_properties->getValue('name') .'</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
    }
	
	$addon = rex_addon::get("d2u_immo");
	if($addon->hasConfig("even_informative_pdf") && $addon->getConfig("even_informative_pdf") == $filename) {
		$message = '<a href="javascript:openPage(\'index.php?page=d2u_immo/settings\')">'.
			 rex_i18n::msg('d2u_immo') ." - ". rex_i18n::msg('d2u_immo_settings') . '</a>';
		if(!in_array($message, $warning)) {
			$warning[] = $message;
		}
	}


	return $warning;
}