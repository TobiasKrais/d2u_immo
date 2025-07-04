<?php

use D2U_Immo\Property;

require_once 'property.php';
if ('priority_down' === $func) {
    $property = new Property((int) rex_request('entry_id', 'int'), rex_config::get('d2u_helper', 'default_lang'));
    $property->priority++;
    $property->save();

    header('Location: '. rex_url::currentBackendPage(['message' => 'd2u_immo_priority_changed'], false));
    exit;
} elseif ('priority_up' === $func) {
    $property = new Property((int) rex_request('entry_id', 'int'), rex_config::get('d2u_helper', 'default_lang'));
    if ($property->priority > 1) {
        $property->priority--;
        $property->save();
    }

    header('Location: '. rex_url::currentBackendPage(['message' => 'd2u_immo_priority_changed'], false));
    exit;
}

if ('' === $func) { /** @phpstan-ignore-line */
    $query = 'SELECT properties.property_id, lang.name AS propertyname, CONCAT(street, " ", house_number) AS `address`, categories.name AS categoryname, online_status, priority, '
        .'(SELECT MAX(priority) FROM '. rex::getTablePrefix() .'d2u_immo_properties WHERE online_status = "online" OR online_status = "offline") AS max_priority '
        .'FROM '. rex::getTablePrefix() .'d2u_immo_properties AS properties '
        .'LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_properties_lang AS lang '
            .'ON properties.property_id = lang.property_id AND lang.clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang') .' '
        .'LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_categories_lang AS categories '
            .'ON properties.category_id = categories.category_id AND categories.clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang') .' '
        .'WHERE online_status = "online" OR online_status = "offline"';
    $default_sort = [];
    if ('priority' === rex_config::get('d2u_immo', 'default_property_sort')) {
         $default_sort = ['online_status' => 'DESC', 'priority' => 'ASC'];
    } else {
        $default_sort = ['online_status' => 'DESC', 'propertyname' => 'ASC'];
    }
    $list = rex_list::factory(query:$query, rowsPerPage:1000, defaultSort:$default_sort);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon fa-home"></i>';
    $thIcon = '';
    if (rex::getUser() instanceof rex_user && (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_immo[edit_data]'))) {
        $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
    }
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###property_id###']);

    $list->setColumnLabel('property_id', rex_i18n::msg('id'));
    $list->setColumnLayout('property_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);
    $list->setColumnSortable('property_id');

    $list->setColumnLabel('propertyname', rex_i18n::msg('d2u_helper_name'));
    $list->setColumnParams('propertyname', ['func' => 'edit', 'entry_id' => '###property_id###']);
    $list->setColumnSortable('propertyname');

    $list->setColumnLabel('address', rex_i18n::msg('d2u_immo_property_address'));
    $list->setColumnSortable('address');

    $list->setColumnLabel('categoryname', rex_i18n::msg('d2u_helper_category'));
    $list->setColumnSortable('categoryname');

    $list->setColumnLabel('priority', rex_i18n::msg('header_priority'));
    $list->setColumnSortable('priority');
    $list->setColumnFormat('priority', 'custom', static function ($params) {
        $list_params = $params['list'];
        $property_id = $list_params->getValue('property_id');
        $priority = $list_params->getValue('priority');
        $max_priority = $list_params->getValue('max_priority');
        $buttons = '<div class="priority-container">';
            $buttons .= '<span class="priority-value">'. $priority .'</span>';
            $buttons .= '<div class="priority-controls">';
                if ($priority > 1) {
                    $buttons .= '<a href="index.php?page='. rex_be_controller::getCurrentPage() .'&amp;func=priority_up&amp;entry_id='. $property_id .'" '
                        .'class="priority-btn priority-up" title="'. rex_i18n::msg('d2u_immo_priority_up') .'">'
                        .'<i class="rex-icon rex-icon-up"></i>'
                        .'</a>';
                }
                if ($priority < $max_priority) {
                    $buttons .= '<a href="index.php?page='. rex_be_controller::getCurrentPage() .'&amp;func=priority_down&amp;entry_id='. $property_id .'" '
                        .'class="priority-btn priority-down" title="'. rex_i18n::msg('d2u_immo_priority_down') .'">'
                        .'<i class="rex-icon rex-icon-down"></i>'
                        .'</a>';
                }
            $buttons .= '</div>';
        $buttons .= '</div>';
        return $buttons;
    });

    $list->removeColumn('max_priority');

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###property_id###']);

    $list->removeColumn('online_status');
    if (rex::getUser() instanceof rex_user && (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_immo[edit_data]'))) {
        $list->addColumn(rex_i18n::msg('status_online'), '<a class="rex-###online_status###" href="' . rex_url::currentBackendPage(['func' => 'changestatus']) . '&entry_id=###property_id###"><i class="rex-icon rex-icon-###online_status###"></i> ###online_status###</a>');
        $list->setColumnLayout(rex_i18n::msg('status_online'), ['', '<td class="rex-table-action">###VALUE###</td>']);

        $list->addColumn(rex_i18n::msg('d2u_helper_clone'), '<i class="rex-icon fa-copy"></i> ' . rex_i18n::msg('d2u_helper_clone'));
        $list->setColumnLayout(rex_i18n::msg('d2u_helper_clone'), ['', '<td class="rex-table-action">###VALUE###</td>']);
        $list->setColumnParams(rex_i18n::msg('d2u_helper_clone'), ['func' => 'clone', 'entry_id' => '###property_id###']);

        $list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
        $list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
        $list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###property_id###']);
        $list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));
    }

    $list->setNoRowsMessage(rex_i18n::msg('d2u_immo_properties_not_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_immo_properties'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}
