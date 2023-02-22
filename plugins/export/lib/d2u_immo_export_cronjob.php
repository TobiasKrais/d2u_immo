<?php
/**
 * Offers helper functions for export plugin.
 */
class d2u_immo_export_cronjob extends D2U_Helper\ACronJob
{
    /**
     * Create a new instance of object.
     * @return multinewsletter_cronjob_cleanup CronJob object
     */
    public static function factory()
    {
        $cronjob = new self();
        $cronjob->name = 'D2U Immo Autoexport';
        return $cronjob;
    }

    /**
     * Install CronJob. Its also activated.
     */
    public function install(): void
    {
        $description = 'Exports property automatically to FTP based export providers';
        $php_code = '<?php namespace D2U_Immo; Provider::autoexport(); ?>';
        $interval = '{\"minutes\":[0],\"hours\":[21],\"days\":\"all\",\"weekdays\":\"all\",\"months\":\"all\"}';
        self::save($description, $php_code, $interval);
    }
}
