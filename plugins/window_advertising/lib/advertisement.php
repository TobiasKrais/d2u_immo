<?php
/**
 * Redaxo D2U Immo Addon.
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

namespace D2U_Immo;

use rex;
use rex_addon;
use rex_config;
use rex_sql;
use rex_yrewrite;

/**
 * Advertisement.
 */
class Advertisement implements \D2U_Helper\ITranslationHelper
{
    /** @var int Database ID */
    public $ad_id = 0;

    /** @var int Redaxo clang id */
    public $clang_id = 0;

    /** @var int Sort Priority */
    public $priority = 0;

    /** @var string Title */
    public $title = '';

    /** @var string Advertisement */
    public $description = '';

    /** @var string Preview picture file name */
    public $picture = '';

    /** @var string Online status. Either "online" or "offline". */
    public $online_status = '';

    /** @var string "yes" if translation needs update */
    public $translation_needs_update = 'delete';

    /** @var string Timestamp containing the last update date */
    public $updatedate = '';

    /** @var string Redaxo update user name */
    public $updateuser = '';

    /** @var string URL */
    public $url = '';

    /**
     * Constructor. Reads a object stored in database.
     * @param int $ad_id ID
     * @param int $clang_id redaxo clang id
     */
    public function __construct($ad_id, $clang_id)
    {
        $this->clang_id = $clang_id;
        $query = 'SELECT * FROM '. rex::getTablePrefix() .'d2u_immo_window_advertising AS advertisements '
                .'LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_window_advertising_lang AS lang '
                    .'ON advertisements.ad_id = lang.ad_id AND clang_id = '. $this->clang_id .' '
                .'WHERE advertisements.ad_id = '. $ad_id;
        $result = rex_sql::factory();
        $result->setQuery($query);
        $num_rows = $result->getRows();

        if ($num_rows > 0) {
            $this->ad_id = $result->getValue('ad_id');
            $this->priority = $result->getValue('priority');
            $this->title = stripslashes(htmlspecialchars_decode($result->getValue('title')));
            $this->description = stripslashes(htmlspecialchars_decode($result->getValue('description')));
            $this->picture = $result->getValue('picture');
            $this->online_status = $result->getValue('online_status');
            if ('' != $result->getValue('translation_needs_update')) {
                $this->translation_needs_update = $result->getValue('translation_needs_update');
            }
            $this->updatedate = $result->getValue('updatedate');
            $this->updateuser = $result->getValue('updateuser');
        }
    }

    /**
     * Changes the online status.
     */
    public function changeStatus(): void
    {
        if ('online' === $this->online_status) {
            if ($this->ad_id > 0) {
                $query = 'UPDATE '. rex::getTablePrefix() .'d2u_immo_window_advertising '
                    ."SET online_status = 'offline' "
                    .'WHERE ad_id = '. $this->ad_id;
                $result = rex_sql::factory();
                $result->setQuery($query);
            }
            $this->online_status = 'offline';
        } else {
            if ($this->ad_id > 0) {
                $query = 'UPDATE '. rex::getTablePrefix() .'d2u_immo_window_advertising '
                    ."SET online_status = 'online' "
                    .'WHERE ad_id = '. $this->ad_id;
                $result = rex_sql::factory();
                $result->setQuery($query);
            }
            $this->online_status = 'online';
        }
    }

    /**
     * Deletes the object in all languages.
     * @param bool $delete_all If true, all translations and main object are deleted. If
     * false, only this translation will be deleted.
     */
    public function delete($delete_all = true): void
    {
        $query_lang = 'DELETE FROM '. rex::getTablePrefix() .'d2u_immo_window_advertising_lang '
            .'WHERE ad_id = '. $this->ad_id
            . ($delete_all ? '' : ' AND clang_id = '. $this->clang_id);
        $result_lang = rex_sql::factory();
        $result_lang->setQuery($query_lang);

        // If no more lang objects are available, delete
        $query_main = 'SELECT * FROM '. rex::getTablePrefix() .'d2u_immo_window_advertising_lang '
            .'WHERE ad_id = '. $this->ad_id;
        $result_main = rex_sql::factory();
        $result_main->setQuery($query_main);
        if (0 === (int) $result_main->getRows()) {
            $query = 'DELETE FROM '. rex::getTablePrefix() .'d2u_immo_window_advertising '
                .'WHERE ad_id = '. $this->ad_id;
            $result = rex_sql::factory();
            $result->setQuery($query);

            // reset priorities
            $this->setPriority(true);
        }
    }

    /**
     * Get all advertisements.
     * @param int $clang_id redaxo clang id
     * @param bool $only_online Show only online advertisements
     * @return Advertisement[] array with Advertisement objects
     */
    public static function getAll($clang_id, $only_online = false)
    {
        $query = 'SELECT lang.ad_id FROM '. rex::getTablePrefix() .'d2u_immo_window_advertising_lang AS lang '
            .'LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_window_advertising AS advertisements '
                .'ON lang.ad_id = advertisements.ad_id AND clang_id = '. $clang_id .' ';
        if ($only_online) {
            $query .= "WHERE online_status = 'online' ";
        }
        $query .= 'ORDER BY priority';
        $result = rex_sql::factory();
        $result->setQuery($query);

        $advertisements = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $advertisements[] = new self($result->getValue('ad_id'), $clang_id);
            $result->next();
        }
        return $advertisements;
    }

    /**
     * Get objects concerning translation updates.
     * @param int $clang_id Redaxo language ID
     * @param string $type 'update' or 'missing'
     * @return Advertisement[] array with Advertisement objects
     */
    public static function getTranslationHelperObjects($clang_id, $type)
    {
        $query = 'SELECT ad_id FROM '. rex::getTablePrefix() .'d2u_immo_window_advertising_lang '
                .'WHERE clang_id = '. $clang_id ." AND translation_needs_update = 'yes' "
                .'ORDER BY title';
        if ('missing' === $type) {
            $query = 'SELECT main.ad_id FROM '. rex::getTablePrefix() .'d2u_immo_window_advertising AS main '
                    .'LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_window_advertising_lang AS target_lang '
                        .'ON main.ad_id = target_lang.ad_id AND target_lang.clang_id = '. $clang_id .' '
                    .'LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_window_advertising_lang AS default_lang '
                        .'ON main.ad_id = default_lang.ad_id AND default_lang.clang_id = '. rex_config::get('d2u_helper', 'default_lang') .' '
                    .'WHERE target_lang.ad_id IS NULL '
                    .'ORDER BY default_lang.title';
            $clang_id = rex_config::get('d2u_helper', 'default_lang');
        }
        $result = rex_sql::factory();
        $result->setQuery($query);

        $objects = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $objects[] = new self($result->getValue('ad_id'), $clang_id);
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
            $d2u_immo = rex_addon::get('d2u_immo');

            $parameterArray = [];
            $parameterArray['ad_id'] = $this->ad_id;

            $this->url = rex_getUrl($d2u_immo->getConfig('article_id'), $this->clang_id, $parameterArray, '&');
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
        $pre_save_advertisement = new self($this->ad_id, $this->clang_id);

        // save priority, but only if new or changed
        if ($this->priority !== $pre_save_advertisement->priority || 0 === $this->ad_id) {
            $this->setPriority();
        }

        if (0 === $this->ad_id || $pre_save_advertisement != $this) {
            $query = rex::getTablePrefix() .'d2u_immo_window_advertising SET '
                    .'priority = '. $this->priority .', '
                    ."picture = '". $this->picture ."', "
                    ."online_status = '". $this->online_status ."' ";

            if (0 === $this->ad_id) {
                $query = 'INSERT INTO '. $query;
            } else {
                $query = 'UPDATE '. $query .' WHERE ad_id = '. $this->ad_id;
            }

            $result = rex_sql::factory();
            $result->setQuery($query);
            if (0 === $this->ad_id) {
                $this->ad_id = (int) $result->getLastId();
                $error = $result->hasError();
            }
        }

        if (0 == $error) {
            // Save the language specific part
            $pre_save_advertisement = new self($this->ad_id, $this->clang_id);
            if ($pre_save_advertisement != $this) {
                $query = 'REPLACE INTO '. rex::getTablePrefix() .'d2u_immo_window_advertising_lang SET '
                        ."ad_id = '". $this->ad_id ."', "
                        ."clang_id = '". $this->clang_id ."', "
                        ."title = '". addslashes(htmlspecialchars($this->title)) ."', "
                        ."description = '". addslashes(htmlspecialchars($this->description)) ."', "
                        ."translation_needs_update = '". $this->translation_needs_update ."', "
                        .'updatedate = CURRENT_TIMESTAMP, '
                        ."updateuser = '". (rex::getUser() instanceof rex_user ? rex::getUser()->getLogin() : '') ."' ";
                $result = rex_sql::factory();
                $result->setQuery($query);
                $error = $result->hasError();
            }
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
        $query = 'SELECT ad_id, priority FROM '. rex::getTablePrefix() .'d2u_immo_window_advertising '
            .'WHERE ad_id <> '. $this->ad_id .' ORDER BY priority';
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

        $advertisements = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $advertisements[$result->getValue('priority')] = $result->getValue('ad_id');
            $result->next();
        }
        array_splice($advertisements, $this->priority - 1, 0, [$this->ad_id]);

        // Save all prios
        foreach ($advertisements as $prio => $ad_id) {
            $query = 'UPDATE '. rex::getTablePrefix() .'d2u_immo_window_advertising '
                    .'SET priority = '. ((int) $prio + 1) .' ' // +1 because array_splice recounts at zero
                    .'WHERE ad_id = '. $ad_id;
            $result = rex_sql::factory();
            $result->setQuery($query);
        }
    }
}
