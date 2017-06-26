<?php
require_once 'property.php';

if ($func == '') {
	$query = 'SELECT properties.property_id, lang.name AS propertyname, categories.name AS categoryname, online_status, priority '
		.'FROM '. rex::getTablePrefix() .'d2u_immo_properties AS properties '
		.'LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_properties_lang AS lang '
			.'ON properties.property_id = lang.property_id AND lang.clang_id = '. rex_config::get("d2u_immo", "default_lang") .' '
		.'LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_categories_lang AS categories '
			.'ON properties.category_id = categories.category_id AND categories.clang_id = '. rex_config::get("d2u_immo", "default_lang") .' '
		.'WHERE online_status = "online" OR online_status = "offline"';
	if($this->getConfig('default_property_sort') == 'priority') {
		$query .= 'ORDER BY online_status DESC, priority ASC';
	}
	else {
		$query .= 'ORDER BY online_status DESC, propertyname ASC';
	}
    $list = rex_list::factory($query);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon fa-home"></i>';
    $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###property_id###']);

    $list->setColumnLabel('property_id', rex_i18n::msg('id'));
    $list->setColumnLayout('property_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);

    $list->setColumnLabel('propertyname', rex_i18n::msg('d2u_immo_name'));
    $list->setColumnParams('propertyname', ['func' => 'edit', 'entry_id' => '###property_id###']);

    $list->setColumnLabel('categoryname', rex_i18n::msg('d2u_immo_category'));

	$list->setColumnLabel('priority', rex_i18n::msg('header_priority'));

 	$list->removeColumn('online_status');
    $list->addColumn(rex_i18n::msg('status_online'), '<a class="rex-###online_status###" href="' . rex_url::currentBackendPage(['func' => 'changestatus']) . '&entry_id=###property_id###"><i class="rex-icon rex-icon-###online_status###"></i> ###online_status###</a>');
	$list->setColumnLayout(rex_i18n::msg('status_online'), ['', '<td class="rex-table-action">###VALUE###</td>']);

	$list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('system_update'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###property_id###']);

 	$list->addColumn(rex_i18n::msg('d2u_helper_clone'), '<i class="rex-icon fa-copy"></i> ' . rex_i18n::msg('d2u_helper_clone'));
    $list->setColumnLayout(rex_i18n::msg('d2u_helper_clone'), ['', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('d2u_helper_clone'), ['func' => 'clone', 'entry_id' => '###property_id###']);

    $list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
    $list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###property_id###']);
    $list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_immo_confirm_delete'));

    $list->setNoRowsMessage(rex_i18n::msg('d2u_immo_category_no_categories_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_immo_properties'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}