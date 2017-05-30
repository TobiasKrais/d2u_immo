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
		$d2u_immo_modules = [];
		$d2u_immo_modules[] = new D2UModule("70-1",
			"D2U Immo Addon - Hauptausgabe",
			1);
		$d2u_immo_modules[] = new D2UModule("70-2",
			"D2U Immo Addon - Infobox Ansprechpartner",
			1);
		return $d2u_immo_modules;
	}
}