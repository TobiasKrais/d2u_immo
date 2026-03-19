<?php

namespace TobiasKrais\D2UImmo;

/**
 * Offers helper functions for export plugin.
 */
class ExportCronjob extends \TobiasKrais\D2UHelper\ACronJob
{
    public const DESCRIPTION = 'Exports property automatically to FTP based export providers';
    public const NAME = 'D2U Immo Autoexport';
    public const PHP_CODE = '<?php namespace TobiasKrais\\\\\\\\D2UImmo; Provider::autoexport(); ?>';

    /**
     * Create a new instance of object.
     * @return ExportCronjob CronJob object
     */
    public static function factory()
    {
        $cronjob = new self();
        $cronjob->name = self::NAME;
        return $cronjob;
    }

    /**
     * Install CronJob. Its also activated.
     */
    public function install(): void
    {
        $interval = '{\"minutes\":[0],\"hours\":[21],\"days\":\"all\",\"weekdays\":\"all\",\"months\":\"all\"}';
        self::save(self::DESCRIPTION, self::PHP_CODE, $interval);
    }
}
