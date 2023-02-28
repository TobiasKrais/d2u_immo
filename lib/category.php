<?php
/**
 * Redaxo D2U Immo Addon.
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

namespace D2U_Immo;

use d2u_addon_backend_helper;
use rex;
use rex_addon;
use rex_config;
use rex_sql;
use rex_yrewrite;

/**
 * Immo Category.
 */
class Category implements \D2U_Helper\ITranslationHelper
{
    /** @var int Database ID */
    public $category_id = 0;

    /** @var int Redaxo clang id */
    public $clang_id = 0;

    /** @var Category Father category object */
    public $parent_category = false;

    /** @var string Name */
    public $name = '';

    /** @var string Short description */
    public $teaser = '';

    /** @var string Preview picture file name */
    public $picture = '';

    /** @var int Sort Priority */
    public $priority = 0;

    /** @var string "yes" if translation needs update */
    public $translation_needs_update = 'delete';

    /** @var int Unix timestamp containing the last update date */
    public $updatedate = 0;

    /** @var string Redaxo update user name */
    public $updateuser = '';

    /** @var string URL */
    public $url = '';

    /**
     * Constructor. Reads a category stored in database.
     * @param int $category_id category ID
     * @param int $clang_id redaxo clang id
     */
    public function __construct($category_id, $clang_id)
    {
        $this->clang_id = $clang_id;
        $query = 'SELECT * FROM '. rex::getTablePrefix() .'d2u_immo_categories AS categories '
                .'LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_categories_lang AS lang '
                    .'ON categories.category_id = lang.category_id '
                    .'AND clang_id = '. $this->clang_id .' '
                .'WHERE categories.category_id = '. $category_id;
        $result = rex_sql::factory();
        $result->setQuery($query);

        if ($result->getRows() > 0) {
            $this->category_id = $result->getValue('category_id');
            if ($result->getValue('parent_category_id') > 0) {
                $this->parent_category = new self($result->getValue('parent_category_id'), $clang_id);
            }
            $this->name = stripslashes($result->getValue('name'));
            $this->teaser = stripslashes($result->getValue('teaser'));
            $this->picture = $result->getValue('picture');
            $this->priority = $result->getValue('priority');
            if ('' != $result->getValue('translation_needs_update')) {
                $this->translation_needs_update = $result->getValue('translation_needs_update');
            }
            $this->updatedate = $result->getValue('updatedate');
            $this->updateuser = $result->getValue('updateuser');
        }
    }

    /**
     * Deletes the object in all languages.
     * @param bool $delete_all If true, all translations and main object are deleted. If
     * false, only this translation will be deleted.
     */
    public function delete($delete_all = true): void
    {
        $query_lang = 'DELETE FROM '. rex::getTablePrefix() .'d2u_immo_categories_lang '
            .'WHERE category_id = '. $this->category_id
            . ($delete_all ? '' : ' AND clang_id = '. $this->clang_id);
        $result_lang = rex_sql::factory();
        $result_lang->setQuery($query_lang);

        // If no more lang objects are available, delete
        $query_main = 'SELECT * FROM '. rex::getTablePrefix() .'d2u_immo_categories_lang '
            .'WHERE category_id = '. $this->category_id;
        $result_main = rex_sql::factory();
        $result_main->setQuery($query_main);
        if (0 === (int) $result_main->getRows()) {
            $query = 'DELETE FROM '. rex::getTablePrefix() .'d2u_immo_categories '
                .'WHERE category_id = '. $this->category_id;
            $result = rex_sql::factory();
            $result->setQuery($query);

            // reset priorities
            $this->setPriority(true);
        }

        d2u_addon_backend_helper::generateUrlCache('category_id');
        d2u_addon_backend_helper::generateUrlCache('property_id');
    }

    /**
     * Get all categories.
     * @param int $clang_id redaxo clang id
     * @return Category[] array with Category objects
     */
    public static function getAll($clang_id)
    {
        $query = 'SELECT lang.category_id FROM '. rex::getTablePrefix() .'d2u_immo_categories_lang AS lang '
            .'LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_categories AS categories '
                .'ON lang.category_id = categories.category_id '
            .'WHERE clang_id = '. $clang_id .' ';
        if ('priority' == rex_addon::get('d2u_immo')->getConfig('default_category_sort', 'name')) {
            $query .= 'ORDER BY priority';
        } else {
            $query .= 'ORDER BY name';
        }
        $result = rex_sql::factory();
        $result->setQuery($query);

        $categories = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $categories[] = new self($result->getValue('category_id'), $clang_id);
            $result->next();
        }
        return $categories;
    }

    /**
     * Detects usage of this category as parent category and returns categories.
     * @return Category[] child categories
     */
    public function getChildren()
    {
        $query = 'SELECT category_id FROM '. rex::getTablePrefix() .'d2u_immo_categories '
            .'WHERE parent_category_id = '. $this->category_id;
        $result = rex_sql::factory();
        $result->setQuery($query);

        $children = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $children[] = new self($result->getValue('category_id'), $this->clang_id);
            $result->next();
        }
        return $children;
    }

    /**
     * Detects whether category is child or not.
     * @return bool true if category has father
     */
    public function isChild()
    {
        if (false === $this->parent_category) {
            return false;
        }

        return true;

    }

    /**
     * Gets the properties of the category.
     * @param string $market_type KAUF, MIETE_PACHT, ERBPACHT, LEASING or empty (all)
     * @param bool $only_online Show only online properties
     * @return Property[] Properties in this category
     */
    public function getProperties($market_type = '', $only_online = false)
    {
        $query = 'SELECT lang.property_id FROM '. rex::getTablePrefix() .'d2u_immo_properties_lang AS lang '
            .'LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_properties AS properties '
                    .'ON lang.property_id = properties.property_id '
            .'WHERE category_id = '. $this->category_id .' AND clang_id = '. $this->clang_id .' ';
        if ($only_online || '' != $market_type) {
            if ($only_online) {
                $query .= "AND online_status = 'online' ";
            }
            if ('' != $market_type) {
                $query .= "AND market_type = '". $market_type ."' ";
            }
        }
        if (rex_addon::get('d2u_immo')->hasConfig('default_property_sort') && 'priority' == rex_addon::get('d2u_immo')->getConfig('default_property_sort')) {
            $query .= 'ORDER BY priority ASC';
        } else {
            $query .= 'ORDER BY name ASC';
        }
        $result = rex_sql::factory();
        $result->setQuery($query);

        $properties = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $properties[] = new Property($result->getValue('property_id'), $this->clang_id);
            $result->next();
        }
        return $properties;
    }

    /**
     * Get objects concerning translation updates.
     * @param int $clang_id Redaxo language ID
     * @param string $type 'update' or 'missing'
     * @return Category[] array with Category objects
     */
    public static function getTranslationHelperObjects($clang_id, $type)
    {
        $query = 'SELECT category_id FROM '. rex::getTablePrefix() .'d2u_immo_categories_lang '
                .'WHERE clang_id = '. $clang_id ." AND translation_needs_update = 'yes' "
                .'ORDER BY name';
        if ('missing' === $type) {
            $query = 'SELECT main.category_id FROM '. rex::getTablePrefix() .'d2u_immo_categories AS main '
                    .'LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_categories_lang AS target_lang '
                        .'ON main.category_id = target_lang.category_id AND target_lang.clang_id = '. $clang_id .' '
                    .'LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_categories_lang AS default_lang '
                        .'ON main.category_id = default_lang.category_id AND default_lang.clang_id = '. rex_config::get('d2u_helper', 'default_lang') .' '
                    .'WHERE target_lang.category_id IS NULL '
                    .'ORDER BY default_lang.name';
            $clang_id = rex_config::get('d2u_helper', 'default_lang');
        }
        $result = rex_sql::factory();
        $result->setQuery($query);

        $objects = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $objects[] = new self($result->getValue('category_id'), $clang_id);
            $result->next();
        }

        return $objects;
    }

    /**
     * Returns the URL of this object.
     * @param bool $including_domain true if Domain name should be included
     * @return string URL
     */
    public function getUrl($including_domain = false)
    {
        if ('' == $this->url) {
            $parameterArray = [];
            $parameterArray['category_id'] = $this->category_id;

            $this->url = rex_getUrl(rex_config::get('d2u_immo', 'article_id'), $this->clang_id, $parameterArray, '&');
        }

        if ($including_domain) {
            if (rex_addon::get('yrewrite') && rex_addon::get('yrewrite')->isAvailable()) {
                return str_replace(rex_yrewrite::getCurrentDomain()->getUrl() .'/', rex_yrewrite::getCurrentDomain()->getUrl(), rex_yrewrite::getCurrentDomain()->getUrl() . $this->url);
            }

            return str_replace(rex::getServer(). '/', rex::getServer(), rex::getServer() . $this->url);

        }

        return $this->url;

    }

    /**
     * Updates or inserts the object into database.
     * @return bool true if successful
     */
    public function save()
    {
        $error = 0;

        // Save the not language specific part
        $pre_save_object = new self($this->category_id, $this->clang_id);

        // save priority, but only if new or changed
        if ($this->priority !== $pre_save_object->priority || 0 === $this->category_id) {
            $this->setPriority();
        }

        if (0 === $this->category_id || $pre_save_object != $this) {
            $query = rex::getTablePrefix() .'d2u_immo_categories SET '
                    .'parent_category_id = '. (false === $this->parent_category ? 0 : $this->parent_category->category_id) .', '
                    .'priority = '. $this->priority .', '
                    ."picture = '". $this->picture ."' ";

            if (0 === $this->category_id) {
                $query = 'INSERT INTO '. $query;
            } else {
                $query = 'UPDATE '. $query .' WHERE category_id = '. $this->category_id;
            }

            $result = rex_sql::factory();
            $result->setQuery($query);
            if (0 === $this->category_id) {
                $this->category_id = (int) $result->getLastId();
                $error = $result->hasError();
            }
        }

        $regenerate_urls = false;
        if (0 == $error) {
            // Save the language specific part
            $pre_save_object = new self($this->category_id, $this->clang_id);
            if ($pre_save_object != $this) {
                $query = 'REPLACE INTO '. rex::getTablePrefix() .'d2u_immo_categories_lang SET '
                        ."category_id = '". $this->category_id ."', "
                        ."clang_id = '". $this->clang_id ."', "
                        ."name = '". addslashes($this->name) ."', "
                        ."teaser = '". addslashes($this->teaser) ."', "
                        ."translation_needs_update = '". $this->translation_needs_update ."', "
                        .'updatedate = '. time() .', '
                        ."updateuser = '". (rex::getUser() instanceof rex_user ? rex::getUser()->getLogin() : '') ."' ";

                $result = rex_sql::factory();
                $result->setQuery($query);
                $error = $result->hasError();

                if (!$error && $pre_save_object->name != $this->name) {
                    $regenerate_urls = true;
                }
            }
        }

        // Update URLs
        if ($regenerate_urls) {
            d2u_addon_backend_helper::generateUrlCache('category_id');
            d2u_addon_backend_helper::generateUrlCache('property_id');
        }

        return $error;
    }

    /**
     * Reassigns priorities in database.
     * @param bool $delete Reorder priority after deletion
     */
    private function setPriority($delete = false): void
    {
        // Pull prios from database
        $query = 'SELECT category_id, priority FROM '. rex::getTablePrefix() .'d2u_immo_categories '
            .'WHERE category_id <> '. $this->category_id .' ORDER BY priority';
        $result = rex_sql::factory();
        $result->setQuery($query);

        // When priority is too small, set at beginning
        if ($this->priority <= 0) {
            $this->priority = 1;
        }

        // When prio is too high or was deleted, simply add at end
        if ($this->priority > $result->getRows() || $delete) {
            $this->priority = (int) $result->getRows() + 1;
        }

        $categories = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $categories[$result->getValue('priority')] = $result->getValue('category_id');
            $result->next();
        }
        array_splice($categories, $this->priority - 1, 0, [$this->category_id]);

        // Save all prios
        foreach ($categories as $prio => $category_id) {
            $query = 'UPDATE '. rex::getTablePrefix() .'d2u_immo_categories '
                    .'SET priority = '. ((int) $prio + 1) .' ' // +1 because array_splice recounts at zero
                    .'WHERE category_id = '. $category_id;
            $result = rex_sql::factory();
            $result->setQuery($query);
        }
    }
}
