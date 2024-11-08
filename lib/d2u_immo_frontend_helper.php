<?php

use D2U_Immo\Category;
use D2U_Immo\Property;

/**
 * @api
 * Offers helper functions for frontend.
 */
class d2u_immo_frontend_helper
{
    /**
     * Returns alternate URLs. Key is Redaxo language id, value is URL.
     * @return string[] alternate URLs
     */
    public static function getAlternateURLs()
    {
        $alternate_URLs = [];

        // Prepare objects first for sorting in correct order
        $url_namespace = TobiasKrais\D2UHelper\FrontendHelper::getUrlNamespace();
        $url_id = TobiasKrais\D2UHelper\FrontendHelper::getUrlId();

        if (filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'property_id' === $url_namespace) {
            $property_id = (int) filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT);
            if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $property_id = $url_id;
            }
            foreach (rex_clang::getAllIds(true) as $this_lang_key) {
                $lang_property = new D2U_Immo\Property($property_id, $this_lang_key);
                if ('delete' !== $lang_property->translation_needs_update) {
                    $alternate_URLs[$this_lang_key] = $lang_property->getUrl();
                }
            }
        } elseif (filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'category_id' === $url_namespace) {
            $category_id = (int) filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
            if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $category_id = $url_id;
            }
            foreach (rex_clang::getAllIds(true) as $this_lang_key) {
                $lang_category = new D2U_Immo\Category($category_id, $this_lang_key);
                if ('delete' !== $lang_category->translation_needs_update) {
                    $alternate_URLs[$this_lang_key] = $lang_category->getUrl();
                }
            }
        }

        return $alternate_URLs;
    }

    /**
     * Returns breadcrumbs. Not from article path, but only part from this addon.
     * @return string[] Breadcrumb elements
     */
    public static function getBreadcrumbs()
    {
        $breadcrumbs = [];

        // Prepare objects first for sorting in correct order
        $url_namespace = TobiasKrais\D2UHelper\FrontendHelper::getUrlNamespace();
        $url_id = TobiasKrais\D2UHelper\FrontendHelper::getUrlId();

        $category = false;
        $property = false;
        if (filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'property_id' === $url_namespace) {
            $property_id = (int) filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT);
            if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $property_id = $url_id;
            }
            $property = new D2U_Immo\Property($property_id, rex_clang::getCurrentId());
        }
        if (filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'category_id' === $url_namespace) {
            $category_id = (int) filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
            if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $category_id = $url_id;
            }
            $category = new D2U_Immo\Category($category_id, rex_clang::getCurrentId());
        }

        // Breadcrumbs
        if ($category instanceof Category) {
            if ($category->parent_category instanceof Category) {
                $breadcrumbs[] = '<a href="' . $category->parent_category->getUrl() . '">' . $category->parent_category->name . '</a>';
            }
            $breadcrumbs[] = '<a href="' . $category->getUrl() . '">' . $category->name . '</a>';
        }
        if ($property instanceof Property) {
            $breadcrumbs[] = '<a href="' . $property->getUrl() . '">' . $property->name . '</a>';
        }

        return $breadcrumbs;
    }
}
