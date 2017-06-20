<?php
/**
 * Redaxo D2U Immo Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Immo Category
 */
class Category {
	/**
	 * @var int Database ID
	 */
	var $category_id = 0;
	
	/**
	 * @var int Redaxo clang id
	 */
	var $clang_id = 0;
	
	/**
	 * @var Category Father category object
	 */
	var $parent_category = FALSE;
	
	/**
	 * @var string Name
	 */
	var $name = "";
	
	/**
	 * @var string Short description
	 */
	var $teaser = "";
	
	/**
	 * @var string Preview picture file name 
	 */
	var $picture = "";
	
	/**
	 * @var int Sort Priority
	 */
	var $priority = 0;
	
	/**
	 * @var string "yes" if translation needs update
	 */
	var $translation_needs_update = "delete";

	/**
	 * @var int Unix timestamp containing the last update date
	 */
	var $updatedate = 0;
	
	/**
	 * @var string Redaxo update user name
	 */
	var $updateuser = "";
	
	/**
	 * @var string URL
	 */
	var $url = "";

	/**
	 * Constructor. Reads a category stored in database.
	 * @param int $category_id Category ID.
	 * @param int $clang_id Redaxo clang id.
	 */
	 public function __construct($category_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". rex::getTablePrefix() ."d2u_immo_categories AS categories "
				."LEFT JOIN ". rex::getTablePrefix() ."d2u_immo_categories_lang AS lang "
					."ON categories.category_id = lang.category_id "
					."AND clang_id = ". $this->clang_id ." "
				."WHERE categories.category_id = ". $category_id;
		$result = rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->category_id = $result->getValue("category_id");
			if($result->getValue("parent_category_id") > 0) {
				$this->parent_category = new Category($result->getValue("parent_category_id"), $clang_id);
			}
			$this->name = $result->getValue("name");
			$this->teaser = $result->getValue("teaser");
			$this->picture = $result->getValue("picture");
			$this->priority = $result->getValue("priority");
			if($result->getValue("translation_needs_update") != "") {
				$this->translation_needs_update = $result->getValue("translation_needs_update");
			}
			$this->updatedate = $result->getValue("updatedate");
			$this->updateuser = $result->getValue("updateuser");
		}
	}
	
	/**
	 * Deletes the object in all languages.
	 * @param int $delete_all If TRUE, all translations and main object are deleted. If 
	 * FALSE, only this translation will be deleted.
	 */
	public function delete($delete_all = TRUE) {
		if($delete_all) {
			$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_immo_categories_lang "
				."WHERE category_id = ". $this->category_id;
			$result_lang = rex_sql::factory();
			$result_lang->setQuery($query_lang);

			$query = "DELETE FROM ". rex::getTablePrefix() ."d2u_immo_categories "
				."WHERE category_id = ". $this->category_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
		else {
			$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_immo_categories_lang "
				."WHERE category_id = ". $this->category_id ." AND clang_id = ". $this->clang_id;
			$result_lang = rex_sql::factory();
			$result_lang->setQuery($query_lang);
		}
	}
	
	/**
	 * Get all categories.
	 * @param int $clang_id Redaxo clang id.
	 * @return Category[] Array with Category objects.
	 */
	public static function getAll($clang_id) {
		$query = "SELECT lang.category_id FROM ". rex::getTablePrefix() ."d2u_immo_categories_lang AS lang "
			."LEFT JOIN ". rex::getTablePrefix() ."d2u_immo_categories AS categories "
				."ON lang.category_id = categories.category_id "
			."WHERE clang_id = ". $clang_id ." ";
		if(rex_addon::get('d2u_immo')->hasConfig('default_category_sort') && rex_addon::get('d2u_immo')->getConfig('default_category_sort') == 'priority') {
			$query .= 'ORDER BY priority';
		}
		else {
			$query .= 'ORDER BY name';
		}
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$categories = array();
		for($i = 0; $i < $result->getRows(); $i++) {
			$categories[] = new Category($result->getValue("category_id"), $clang_id);
			$result->next();
		}
		return $categories;
	}
	
	/**
	 * Get the <link rel="canonical"> tag for page header.
	 * @return Complete tag.
	 */
	public function getCanonicalTag() {
		return '<link rel="canonical" href="'. $this->getURL() .'">';
	}
	
	/**
	 * Detects usage of this category as parent category and returns categories.
	 * @return Category[] Child categories.
	 */
	public function getChildren() {
		$query = "SELECT category_id FROM ". rex::getTablePrefix() ."d2u_immo_categories "
			."WHERE parent_category_id = ". $this->category_id;
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$children =  array();
		for($i = 0; $i < $result->getRows(); $i++) {
			$children[] = new Category($result->getValue("category_id"), $this->clang_id);
			$result->next();
		}
		return $children;
	}
	
	/**
	 * Get the <title> tag for page header.
	 * @return Complete title tag.
	 */
	public function getTitleTag() {
		return '<title>'. $this->name .' / '. rex::getServerName() .'</title>';
	}
	
	/**
	 * Detects whether category is child or not.
	 * @return boolean TRUE if category has father.
	 */
	public function isChild() {
		if($this->parent_category === FALSE) {
			return FALSE;
		}
		else {
			return TRUE;
		}
	}
	
	/**
	 * Gets the properties of the category.
	 * @return Property[] Properties in this category
	 */
	public function getProperties() {
		$query = "SELECT lang.property_id FROM ". rex::getTablePrefix() ."d2u_immo_properties_lang AS lang "
			."LEFT JOIN ". rex::getTablePrefix() ."d2u_immo_properties AS properties "
					."ON lang.property_id = properties.property_id "
			."WHERE category_id = ". $this->category_id ." AND clang_id = ". $this->clang_id .' ';
		if(rex_addon::get('d2u_immo')->hasConfig('default_property_sort') && rex_addon::get('d2u_immo')->getConfig('default_property_sort') == 'priority') {
			$query .= 'ORDER BY priority ASC';
		}
		else {
			$query .= 'ORDER BY name ASC';
		}
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$properties = array();
		for($i = 0; $i < $result->getRows(); $i++) {
			$properties[] = new Property($result->getValue("property_id"), $this->clang_id);
			$result->next();
		}
		return $properties;
	}

	/**
	 * Get the <meta rel="alternate" hreflang=""> tags for page header.
	 * @return Complete tags.
	 */
	public function getMetaAlternateHreflangTags() {
		$hreflang_tags = "";
		foreach(rex_clang::getAll() as $rex_clang) {
			if($rex_clang->getId() == $this->clang_id && $this->translation_needs_update != "delete") {
				$hreflang_tags .= '<link rel="alternate" type="text/html" hreflang="'. $rex_clang->getCode() .'" href="'. $this->getURL() .'" title="'. str_replace('"', '', $this->name) .'">';
			}
			else {
				$category = new Category($this->category_id, $rex_clang->getId());
				if($category->translation_needs_update != "delete") {
					$hreflang_tags .= '<link rel="alternate" type="text/html" hreflang="'. $rex_clang->getCode() .'" href="'. $category->getURL() .'" title="'. str_replace('"', '', $category->name) .'">';
				}
			}
		}
		return $hreflang_tags;
	}
	
	/**
	 * Get the <meta name="description"> tag for page header.
	 * @return Complete tag.
	 */
	public function getMetaDescriptionTag() {
		return '<meta name="description" content="'. $this->teaser .'">';
	}
	
	/*
	 * Returns the URL of this object.
	 * @param string $including_domain TRUE if Domain name should be included
	 * @return string URL
	 */
	public function getURL($including_domain = FALSE) {
		if($this->url == "") {
			$d2u_immo = rex_addon::get("d2u_immo");
				
			$parameterArray = array();
			$parameterArray['category_id'] = $this->category_id;
			
			$this->url = rex_getUrl($d2u_immo->getConfig('article_id'), $this->clang_id, $parameterArray, "&");
		}

		if($including_domain) {
			return str_replace(rex::getServer(). '/', rex::getServer(), rex::getServer() . $this->url);
		}
		else {
			return $this->url;
		}
	}
	
	/**
	 * Updates or inserts the object into database.
	 * @return boolean TRUE if successful
	 */
	public function save() {
		$error = 0;

		// Save the not language specific part
		$pre_save_category = new Category($this->category_id, $this->clang_id);
	
		// save priority, but only if new or changed
		if($this->priority != $pre_save_category->priority || $this->category_id == 0) {
			$this->setPriority();
		}

		if($this->category_id == 0 || $pre_save_category != $this) {
			$query = rex::getTablePrefix() ."d2u_immo_categories SET "
					."parent_category_id = ". ($this->parent_category === FALSE ? 0 : $this->parent_category->category_id) .", "
					."priority = ". $this->priority .", "
					."picture = '". $this->picture ."' ";

			if($this->category_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE category_id = ". $this->category_id;
			}

			$result = rex_sql::factory();
			$result->setQuery($query);
			if($this->category_id == 0) {
				$this->category_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		if($error == 0) {
			// Save the language specific part
			$pre_save_category = new Category($this->category_id, $this->clang_id);
			if($pre_save_category != $this) {
				$query = "REPLACE INTO ". rex::getTablePrefix() ."d2u_immo_categories_lang SET "
						."category_id = '". $this->category_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."name = '". htmlspecialchars($this->name) ."', "
						."teaser = '". htmlspecialchars($this->teaser) ."', "
						."translation_needs_update = '". $this->translation_needs_update ."', "
						."updatedate = ". time() .", "
						."updateuser = '". rex::getUser()->getLogin() ."' ";

				$result = rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
			}
		}
		
		// Update URLs
		if(rex_addon::get("url")->isAvailable()) {
			UrlGenerator::generatePathFile([]);
		}
		
		return $error;
	}
	
	/**
	 * Reassigns priority to all Categories in database.
	 */
	private function setPriority() {
		// Pull prios from database
		$query = "SELECT category_id, priority FROM ". rex::getTablePrefix() ."d2u_immo_categories "
			."WHERE category_id <> ". $this->category_id ." ORDER BY priority";
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		// When priority is too small, set at beginning
		if($this->priority <= 0) {
			$this->priority = 1;
		}
		
		// When prio is too high, simply add at end 
		if($this->priority > $result->getRows()) {
			$this->priority = $result->getRows() + 1;
		}

		$categories = array();
		for($i = 0; $i < $result->getRows(); $i++) {
			$categories[$result->getValue("priority")] = $result->getValue("category_id");
			$result->next();
		}
		array_splice($categories, ($this->priority - 1), 0, array($this->category_id));

		// Save all prios
		foreach($categories as $prio => $category_id) {
			$query = "UPDATE ". rex::getTablePrefix() ."d2u_immo_categories "
					."SET priority = ". ($prio + 1) ." " // +1 because array_splice recounts at zero
					."WHERE category_id = ". $category_id;
			$result = rex_sql::factory();
			$result->setQuery($query);
		}
	}
}