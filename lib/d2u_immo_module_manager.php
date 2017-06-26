<?php
/**
 * Class managing modules published by www.design-to-use.de
 *
 * @author Tobias Krais
 */
class D2UImmoModules {
	/**
	 * Get modules offered by this addon.
	 * @return D2UModule[] Modules offered by this addon
	 */
	public static function getD2UImmoModules() {
		$modules = [];
		$modules[] = new D2UModule("70-1",
			"D2U Immo Addon - Hauptausgabe",
			1);
		$modules[] = new D2UModule("70-2",
			"D2U Immo Addon - Infobox Ansprechpartner",
			1);
		$modules[] = new D2UModule("70-3",
			"D2U Immo Addon - Ausgabe Kategorie",
			1);
		return $modules;
	}
}