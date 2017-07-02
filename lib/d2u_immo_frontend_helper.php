<?php
/**
 * Offers helper functions for frontend
 */
class d2u_immo_frontend_helper {
	/**
	 * Returns breadcrumbs. Not from article path, but only part from this addon.
	 * @return string[] Breadcrumb elements
	 */
	public static function getBreadcrumbs() {
		$breadcrumbs = [];

		// Prepare objects first for sorting in correct order
		$category = FALSE;
		$property = FALSE;
		$url_data = [];
		if(rex_addon::get("url")->isAvailable()) {
			$url_data = UrlGenerator::getData();
		}
		if(filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && isset($url_data->urlParamKey) && $url_data->urlParamKey === "property_id")) {
			$property_id = filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT);
			if(rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
				$property_id = UrlGenerator::getId();
			}
			$property = new Property($property_id, rex_clang::getCurrentId());
		}
		if(filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && isset($url_data->urlParamKey) && $url_data->urlParamKey === "category_id")) {
			$category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
			if(rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
				$category_id = UrlGenerator::getId();
			}
			$category = new Category($category_id, rex_clang::getCurrentId());
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