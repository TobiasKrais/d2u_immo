<?php
/**
 * Class managing modules published by www.design-to-use.de.
 *
 * @author Tobias Krais
 */
class D2UImmoModules
{
    /**
     * Get modules offered by this addon.
     * @return \TobiasKrais\D2UHelper\Module[] Modules offered by this addon
     */
    public static function getModules()
    {
        $modules = [];
        $modules[] = new \TobiasKrais\D2UHelper\Module('70-1',
            'D2U Immo Addon - Hauptausgabe (BS4, deprecated)',
            24);
        $modules[] = new \TobiasKrais\D2UHelper\Module('70-2',
            'D2U Immo Addon - Infobox Ansprechpartner (BS4, deprecated)',
            5);
        $modules[] = new \TobiasKrais\D2UHelper\Module('70-3',
            'D2U Immo Addon - Ausgabe Kategorie (BS4, deprecated)',
            5);
        $modules[] = new \TobiasKrais\D2UHelper\Module('70-4',
            'D2U Immo Addon - Hauptausgabe (BS5)',
            2);
        $modules[] = new \TobiasKrais\D2UHelper\Module('70-5',
            'D2U Immo Addon - Infobox Ansprechpartner (BS5)',
            1);
        $modules[] = new \TobiasKrais\D2UHelper\Module('70-6',
            'D2U Immo Addon - Ausgabe Kategorie (BS5)',
            1);
        return $modules;
    }
}
