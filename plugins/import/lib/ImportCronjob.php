<?php
namespace D2U_Immo;

/**
 * Offers helper functions for import plugin.
 */
class ImportCronjob extends \D2U_Helper\ACronJob
{
    /**
     * Create a new instance of object.
     * @return ImportCronjob CronJob object
     */
    public static function factory()
    {
        $cronjob = new self();
        $cronjob->name = 'D2U Immo Autoimport';
        return $cronjob;
    }

    /**
     * Install CronJob. Its also activated.
     */
    public function install(): void
    {
        $description = 'Imports OpenImmo files';
        $php_code = '<?php namespace D2U_Immo; ImportOpenImmo::autoimport(); ?>';
        $interval = '{\"minutes\":\"all\",\"hours\":\"all\",\"days\":\"all\",\"weekdays\":\"all\",\"months\":\"all\"}';
        self::save($description, $php_code, $interval);
    }
}
