<?php
/**
 * Offers helper functions for frontend
 */
class d2u_immo_frontend_helper {
	/**
	 * Returns alternate URLs. Key is Redaxo language id, value is URL
	 * @return string[] alternate URLs
	 */
	public static function getAlternateURLs() {
		$alternate_URLs = [];

		// Prepare objects first for sorting in correct order
		$url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
		$url_id = d2u_addon_frontend_helper::getUrlId();
		
		if(filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "property_id") {
			$property_id = filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT);
			if(\rex_addon::get("url")->isAvailable() && $url_id > 0) {
				$property_id = $url_id;
			}
			foreach(rex_clang::getAllIds(TRUE) as $this_lang_key) {
				$lang_property = new D2U_Immo\Property($property_id, $this_lang_key);
				if($lang_property->translation_needs_update != "delete") {
					$alternate_URLs[$this_lang_key] = $lang_property->getURL();
				}
			}
		}
		else if(filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "category_id") {
			$category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
			if(\rex_addon::get("url")->isAvailable() && $url_id > 0) {
				$category_id = $url_id;
			}
			foreach(rex_clang::getAllIds(TRUE) as $this_lang_key) {
				$lang_category = new D2U_Immo\Category($category_id, $this_lang_key);
				if($lang_category->translation_needs_update != "delete") {
					$alternate_URLs[$this_lang_key] = $lang_category->getURL();
				}
			}
		}
		
		return $alternate_URLs;
	}

	/**
	 * Returns breadcrumbs. Not from article path, but only part from this addon.
	 * @return string[] Breadcrumb elements
	 */
	public static function getBreadcrumbs() {
		$breadcrumbs = [];

		// Prepare objects first for sorting in correct order
		$url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
		$url_id = d2u_addon_frontend_helper::getUrlId();

		$category = FALSE;
		$property = FALSE;
		if(filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "property_id") {
			$property_id = filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT);
			if(\rex_addon::get("url")->isAvailable() && $url_id > 0) {
				$property_id = $url_id;
			}
			$property = new D2U_Immo\Property($property_id, rex_clang::getCurrentId());
		}
		if(filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "category_id") {
			$category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
			if(\rex_addon::get("url")->isAvailable() && $url_id > 0) {
				$category_id = $url_id;
			}
			$category = new D2U_Immo\Category($category_id, rex_clang::getCurrentId());
		}

		// Breadcrumbs
		if($category !== FALSE) {
			if($category->parent_category !== FALSE) {
				$breadcrumbs[] = '<a href="' . $category->parent_category->getUrl() . '">' . $category->parent_category->name . '</a>';
			}
			$breadcrumbs[] = '<a href="' . $category->getUrl() . '">' . $category->name . '</a>';
		}
		if($property !== FALSE) {
			$breadcrumbs[] = '<a href="' . $property->getUrl() . '">' . $property->name . '</a>';
		}
		
		return $breadcrumbs;
	}
}