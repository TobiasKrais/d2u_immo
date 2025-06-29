<?php

use D2U_Immo\Advertisement;
use D2U_Immo\Category;
use D2U_Immo\Property;

if (\rex::isBackend() && is_object(\rex::getUser())) {
    rex_perm::register('d2u_immo[]', rex_i18n::msg('d2u_immo_rights_all'));
    rex_perm::register('d2u_immo[edit_lang]', rex_i18n::msg('d2u_immo_rights_edit_lang'), rex_perm::OPTIONS);
    rex_perm::register('d2u_immo[edit_data]', rex_i18n::msg('d2u_immo_rights_edit_data'), rex_perm::OPTIONS);
    rex_perm::register('d2u_immo[settings]', rex_i18n::msg('d2u_immo_rights_settings'), rex_perm::OPTIONS);
    rex_view::addCssFile($this->getAssetsUrl('backend.css'));

    rex_extension::register('D2U_HELPER_TRANSLATION_LIST', rex_d2u_immo_translation_list(...));
    rex_extension::register('ART_PRE_DELETED', rex_d2u_immo_article_is_in_use(...));
    rex_extension::register('CLANG_DELETED', rex_d2u_immo_clang_deleted(...));
    rex_extension::register('MEDIA_IS_IN_USE', rex_d2u_immo_media_is_in_use(...));
}
else if (\rex::isFrontend()) {
    rex_extension::register('D2U_HELPER_ALTERNATE_URLS', rex_d2u_immo_alternate_urls(...));
    rex_extension::register('D2U_HELPER_BREADCRUMBS', rex_d2u_immo_breadcrumbs(...));
}

/**
 * Get alternate URLs.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<int,string> Addon url list
 */
function rex_d2u_immo_alternate_urls(rex_extension_point $ep) {
    $params = $ep->getParams();
    $url_namespace = (string) $params['url_namespace'];
    $url_id = (int) $params['url_id'];

    $url_list = \D2U_Immo\FrontendHelper::getAlternateURLs($url_namespace, $url_id);
    if (count($url_list) === 0 && is_array($ep->getSubject())) {
        $url_list = $ep->getSubject();
    }

    return $url_list;
}

/**
 * Checks if article is used by this addon.
 * @param rex_extension_point<string> $ep Redaxo extension point
 * @throws rex_api_exception If article is used
 * @return array<string> Warning message as array
 */
function rex_d2u_immo_article_is_in_use(rex_extension_point $ep)
{
    $warning = [];
    $params = $ep->getParams();
    $article_id = $params['id'];

    // Prepare warnings
    // Settings
    $addon = rex_addon::get('d2u_immo');
    if ($addon->hasConfig('article_id') && (int) $addon->getConfig('article_id') === $article_id) {
        $message = '<a href="index.php?page=d2u_immo/settings">'.
             rex_i18n::msg('d2u_immo_rights_all') .' - '. rex_i18n::msg('d2u_immo_meta_settings') . '</a>';
        $warning[] = $message;
    }

    if (count($warning) > 0) {
        throw new rex_api_exception(rex_i18n::msg('d2u_helper_rex_article_cannot_delete') .'<ul><li>'. implode('</li><li>', $warning) .'</li></ul>');
    }

    return [];
}

/**
 * Get breadcrumb part.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<int,string> HTML formatted breadcrumb elements
 */
function rex_d2u_immo_breadcrumbs(rex_extension_point $ep) {
    $params = $ep->getParams();
    $url_namespace = (string) $params['url_namespace'];
    $url_id = (int) $params['url_id'];

    $breadcrumbs = \D2U_Immo\FrontendHelper::getBreadcrumbs($url_namespace, $url_id);
    if (count($breadcrumbs) === 0) {
        $breadcrumbs = $ep->getSubject();
    }

    return $breadcrumbs;
}

/**
 * Deletes language specific configurations and objects.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_immo_clang_deleted(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $clang_id = $params['id'];

    // Delete
    $categories = D2U_Immo\Category::getAll($clang_id);
    foreach ($categories as $category) {
        $category->delete(false);
    }
    $properties = D2U_Immo\Property::getAll($clang_id, '', false);
    foreach ($properties as $property) {
        $property->delete(false);
    }

    // Delete language settings
    if (rex_config::has('d2u_immo', 'lang_replacement_'. $clang_id)) {
        rex_config::remove('d2u_immo', 'lang_replacement_'. $clang_id);
    }
    // Delete language replacements
    d2u_immo_lang_helper::factory()->uninstall($clang_id);

    return $warning;
}

/**
 * Checks if media is used by this addon.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_immo_media_is_in_use(rex_extension_point $ep)
{
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
    for ($i = 0; $i < $sql_categories->getRows(); ++$i) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_immo/category&func=edit&entry_id='. $sql_categories->getValue('category_id') .'\')">'.
             rex_i18n::msg('d2u_immo_rights_all') .' - '. rex_i18n::msg('d2u_helper_categories') .': '. $sql_categories->getValue('name') . '</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
        $sql_categories->next();
    }

    // Contacts
    for ($i = 0; $i < $sql_contacts->getRows(); ++$i) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_immo/contact&func=edit&entry_id='.
            $sql_contacts->getValue('contact_id') .'\')">'. rex_i18n::msg('d2u_immo_rights_all') .' - '. rex_i18n::msg('d2u_immo_contacts') .': '. $sql_contacts->getValue('firstname') .' '. $sql_contacts->getValue('lastname') .'</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
        $sql_contacts->next();
    }

    // Properties
    for ($i = 0; $i < $sql_properties->getRows(); ++$i) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_immo/property&func=edit&entry_id='.
            $sql_properties->getValue('property_id') .'\')">'. rex_i18n::msg('d2u_immo_rights_all') .' - '. rex_i18n::msg('d2u_immo_properties') .': '. $sql_properties->getValue('name') .'</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
        $sql_properties->next();
    }

    $addon = rex_addon::get('d2u_immo');
    if ($addon->hasConfig('even_informative_pdf') && (string) $addon->getConfig('even_informative_pdf') === $filename) {
        $message = '<a href="javascript:openPage(\'index.php?page=d2u_immo/settings\')">'.
             rex_i18n::msg('d2u_immo') .' - '. rex_i18n::msg('d2u_helper_settings') . '</a>';
        if (!in_array($message, $warning, true)) {
            $warning[] = $message;
        }
    }

    return $warning;
}

/**
 * Addon translation list.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<array<string,array<int,array<string,string>>|string>|string> Addon translation list
 */
function rex_d2u_immo_translation_list(rex_extension_point $ep) {
    $params = $ep->getParams();
    $source_clang_id = (int) $params['source_clang_id'];
    $target_clang_id = (int) $params['target_clang_id'];
    $filter_type = (string) $params['filter_type'];

    $list = $ep->getSubject();
    $list_entry = [
        'addon_name' => rex_i18n::msg('d2u_immo'),
        'pages' => []
    ];

    $categories = Category::getTranslationHelperObjects($target_clang_id, $filter_type);
    if (count($categories) > 0) {
        $html_categories = '<ul>';
        foreach ($categories as $category) {
            if ('' === $category->name) {
                $category = new Category($category->category_id, $source_clang_id);
            }
            $html_categories .= '<li><a href="'. rex_url::backendPage('d2u_immo/category', ['entry_id' => $category->category_id, 'func' => 'edit']) .'">'. $category->name .'</a></li>';
        }
        $html_categories .= '</ul>';
        
        $list_entry['pages'][] = [
            'title' => rex_i18n::msg('d2u_helper_category'),
            'icon' => 'rex-icon-open-category',
            'html' => $html_categories
        ];
    }

    $properties = Property::getTranslationHelperObjects($target_clang_id, $filter_type);
    if (count($properties) > 0) {
        $html_properties = '<ul>';
        foreach ($properties as $property) {
            if ('' === $property->name) {
                $property = new Property($property->property_id, $source_clang_id);
            }
            $html_properties .= '<li><a href="'. rex_url::backendPage('d2u_immo/property', ['entry_id' => $property->property_id, 'func' => 'edit']) .'">'. $property->name .'</a></li>';
        }
        $html_properties .= '</ul>';
        
        $list_entry['pages'][] = [
            'title' => rex_i18n::msg('d2u_immo_properties'),
            'icon' => 'fa-home',
            'html' => $html_properties
        ];
    }

    if (rex_plugin::get('d2u_immo', 'window_advertising')->isAvailable()) {
        $ads = Advertisement::getTranslationHelperObjects($target_clang_id, $filter_type);
        if (count($ads) > 0) {
            $html_ads = '<ul>';
            foreach ($ads as $ad) {
                if ('' === $ad->title) {
                    $ad = new Advertisement($ad->ad_id, $source_clang_id);
                }
                $html_ads .= '<li><a href="'. rex_url::backendPage('d2u_immo/window_advertising/property', ['entry_id' => $ad->ad_id, 'func' => 'edit']) .'">'. $ad->title .'</a></li>';
            }
            $html_ads .= '</ul>';
            
            $list_entry['pages'][] = [
                'title' => rex_i18n::msg('d2u_immo_window_advertising_ads'),
                'icon' => 'fa-home',
                'html' => $html_ads
            ];
        }
    }

    $list[] = $list_entry;

    return $list;
}