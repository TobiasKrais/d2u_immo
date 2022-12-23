<?php
require_once 'property.php';

if ($func === '') {
	$query = 'SELECT properties.property_id, lang.name AS propertyname, categories.name AS categoryname, priority '
		.'FROM '. rex::getTablePrefix() .'d2u_immo_properties AS properties '
		.'LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_properties_lang AS lang '
			.'ON properties.property_id = lang.property_id AND lang.clang_id = '. intval(rex_config::get("d2u_helper", "default_lang")) .' '
		.'LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_categories_lang AS categories '
			.'ON properties.category_id = categories.category_id AND categories.clang_id = '. intval(rex_config::get("d2u_helper", "default_lang")) .' '
		.'WHERE online_status = "archived" ';
	if($this->getConfig('default_property_sort') == 'priority') {
		$query .= 'ORDER BY online_status DESC, priority ASC';
	}
	else {
		$query .= 'ORDER BY online_status DESC, propertyname ASC';
	}
    $list = rex_list::factory($query, 1000);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon fa-home"></i>';
    $thIcon = '';
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###property_id###']);

    $list->setColumnLabel('property_id', rex_i18n::msg('id'));
    $list->setColumnLayout('property_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);

    $list->setColumnLabel('propertyname', rex_i18n::msg('d2u_helper_name'));
    $list->setColumnParams('propertyname', ['func' => 'edit', 'entry_id' => '###property_id###']);

    $list->setColumnLabel('categoryname', rex_i18n::msg('d2u_helper_category'));

	$list->setColumnLabel('priority', rex_i18n::msg('header_priority'));

	$list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###property_id###']);

	if(rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_immo[edit_data]')) {
		$list->addColumn(rex_i18n::msg('d2u_helper_clone'), '<i class="rex-icon fa-copy"></i> ' . rex_i18n::msg('d2u_helper_clone'));
		$list->setColumnLayout(rex_i18n::msg('d2u_helper_clone'), ['', '<td class="rex-table-action">###VALUE###</td>']);
		$list->setColumnParams(rex_i18n::msg('d2u_helper_clone'), ['func' => 'clone', 'entry_id' => '###property_id###']);

		$list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
		$list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
		$list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###property_id###']);
		$list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));
	}

    $list->setNoRowsMessage(rex_i18n::msg('d2u_helper_no_categories_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_immo_properties'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}