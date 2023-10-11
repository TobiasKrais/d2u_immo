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
     * @return D2UModule[] Modules offered by this addon
     */
    public static function getModules()
    {
        $modules = [];
        $modules[] = new D2UModule('70-1',
            'D2U Immo Addon - Hauptausgabe',
            23);
        $modules[] = new D2UModule('70-2',
            'D2U Immo Addon - Infobox Ansprechpartner',
            5);
        $modules[] = new D2UModule('70-3',
            'D2U Immo Addon - Ausgabe Kategorie',
            5);
        return $modules;
    }
}
