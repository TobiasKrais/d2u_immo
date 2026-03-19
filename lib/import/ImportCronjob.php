<?php

namespace TobiasKrais\D2UImmo;

/**
 * Offers helper functions for import plugin.
 */
class ImportCronjob extends \TobiasKrais\D2UHelper\ACronJob
{
    public const DESCRIPTION = 'Imports OpenImmo files';
    public const NAME = 'D2U Immo Autoimport';
    public const PHP_CODE = '<?php namespace TobiasKrais\\\\\\\\D2UImmo; ImportOpenImmo::autoimport(); ?>';

    /**
     * Create a new instance of object.
     * @return ImportCronjob CronJob object
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
        $interval = '{\"minutes\":\"all\",\"hours\":\"all\",\"days\":\"all\",\"weekdays\":\"all\",\"months\":\"all\"}';
        self::save(self::DESCRIPTION, self::PHP_CODE, $interval);
    }
}
