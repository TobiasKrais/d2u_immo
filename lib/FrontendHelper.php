<?php
namespace D2U_Immo;

use rex_clang;

/**
 * @api
 * Offers helper functions for frontend.
 */
class FrontendHelper
{
    /**
     * Returns alternate URLs. Key is Redaxo language id, value is URL.
     * @param ?string $url_namespace URL namespace
     * @param ?int $url_id URL id
     * @return array<int,string> alternate URLs
     */
    public static function getAlternateURLs($url_namespace = null, $url_id = null)
    {
        if (null === $url_namespace) {
            $url_namespace = \TobiasKrais\D2UHelper\FrontendHelper::getUrlNamespace();
        }
        if (null === $url_id) {
            $url_id = \TobiasKrais\D2UHelper\FrontendHelper::getUrlId();
        }
        $alternate_URLs = [];

        if (filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'property_id' === $url_namespace) {
            $property_id = (int) filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT);
            if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $property_id = $url_id;
            }
            foreach (rex_clang::getAllIds(true) as $this_lang_key) {
                $lang_property = new Property($property_id, $this_lang_key);
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
                $lang_category = new Category($category_id, $this_lang_key);
                if ('delete' !== $lang_category->translation_needs_update) {
                    $alternate_URLs[$this_lang_key] = $lang_category->getUrl();
                }
            }
        }

        return $alternate_URLs;
    }

    /**
     * Returns breadcrumbs. Not from article path, but only part from this addon.
     * @param ?string $url_namespace URL namespace
     * @param ?int $url_id URL id
     * @return array<int,string> Breadcrumb elements
     */
    public static function getBreadcrumbs($url_namespace = null, $url_id = null)
    {
        if (null === $url_namespace) {
            $url_namespace = \TobiasKrais\D2UHelper\FrontendHelper::getUrlNamespace();
        }
        if (null === $url_id) {
            $url_id = \TobiasKrais\D2UHelper\FrontendHelper::getUrlId();
        }
        $breadcrumbs = [];

        $category = false;
        $property = false;
        if (filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'property_id' === $url_namespace) {
            $property_id = (int) filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT);
            if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $property_id = $url_id;
            }
            $property = new Property($property_id, rex_clang::getCurrentId());
        }
        if (filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'category_id' === $url_namespace) {
            $category_id = (int) filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
            if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
                $category_id = $url_id;
            }
            $category = new Category($category_id, rex_clang::getCurrentId());
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