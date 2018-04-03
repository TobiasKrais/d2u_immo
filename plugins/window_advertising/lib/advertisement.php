<?php
/**
 * Redaxo D2U Immo Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

namespace D2U_Immo;

/**
 * Advertisement
 */
class Advertisement implements \D2U_Helper\ITranslationHelper {
	/**
	 * @var int Database ID
	 */
	var $ad_id = 0;
	
	/**
	 * @var int Redaxo clang id
	 */
	var $clang_id = 0;
	
	/**
	 * @var int Sort Priority
	 */
	var $priority = 0;
	
	/**
	 * @var string Title
	 */
	var $title = "";
	
	/**
	 * @var string Advertisement 
	 */
	var $description = "";
	
	/**
	 * @var string Preview picture file name 
	 */
	var $picture = "";
	
	/**
	 * @var string Online status. Either "online" or "offline".
	 */
	var $online_status = "";
	
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
	 * Constructor. Reads a object stored in database.
	 * @param int $ad_id ID.
	 * @param int $clang_id Redaxo clang id.
	 */
	 public function __construct($ad_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_immo_window_advertising AS advertisements "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_immo_window_advertising_lang AS lang "
					."ON advertisements.ad_id = lang.ad_id AND clang_id = ". $this->clang_id ." "
				."WHERE advertisements.ad_id = ". $ad_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->ad_id = $result->getValue("ad_id");
			$this->priority = $result->getValue("priority");
			$this->title = stripslashes(htmlspecialchars_decode($result->getValue("title")));
			$this->description = stripslashes(htmlspecialchars_decode($result->getValue("description")));
			$this->picture = $result->getValue("picture");
			$this->online_status = $result->getValue("online_status");
			if($result->getValue("translation_needs_update") != "") {
				$this->translation_needs_update = $result->getValue("translation_needs_update");
			}
			$this->updatedate = $result->getValue("updatedate");
			$this->updateuser = $result->getValue("updateuser");
		}
	}
	
	/**
	 * Changes the online status
	 */
	public function changeStatus() {
		if($this->online_status == "online") {
			if($this->ad_id > 0) {
				$query = "UPDATE ". \rex::getTablePrefix() ."d2u_immo_window_advertising "
					."SET online_status = 'offline' "
					."WHERE ad_id = ". $this->ad_id;
				$result = \rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "offline";
		}
		else {
			if($this->ad_id > 0) {
				$query = "UPDATE ". \rex::getTablePrefix() ."d2u_immo_window_advertising "
					."SET online_status = 'online' "
					."WHERE ad_id = ". $this->ad_id;
				$result = \rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "online";
		}
	}

	/**
	 * Deletes the object in all languages.
	 * @param int $delete_all If TRUE, all translations and main object are deleted. If 
	 * FALSE, only this translation will be deleted.
	 */
	public function delete($delete_all = TRUE) {
		$query_lang = "DELETE FROM ". \rex::getTablePrefix() ."d2u_immo_window_advertising_lang "
			."WHERE ad_id = ". $this->ad_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = \rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_immo_window_advertising_lang "
			."WHERE ad_id = ". $this->ad_id;
		$result_main = \rex_sql::factory();
		$result_main->setQuery($query_main);
		if($result_main->getRows() == 0) {
			$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_immo_window_advertising "
				."WHERE ad_id = ". $this->ad_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);
		}
	}
	
	/**
	 * Get all advertisements.
	 * @param int $clang_id Redaxo clang id.
	 * @param boolean $only_online Show only online advertisements
	 * @return Advertisement[] Array with Advertisement objects.
	 */
	public static function getAll($clang_id, $only_online = FALSE) {
		$query = "SELECT lang.ad_id FROM ". \rex::getTablePrefix() ."d2u_immo_window_advertising_lang AS lang "
			."LEFT JOIN ". \rex::getTablePrefix() ."d2u_immo_window_advertising AS advertisements "
				."ON lang.ad_id = advertisements.ad_id AND clang_id = ". $clang_id ." ";
		if($only_online) {
			$query .= "WHERE online_status = 'online' ";
		}
		$query .= 'ORDER BY priority';
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$advertisements = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$advertisements[] = new Advertisement($result->getValue("ad_id"), $clang_id);
			$result->next();
		}
		return $advertisements;
	}
	
	/**
	 * Get objects concerning translation updates
	 * @param int $clang_id Redaxo language ID
	 * @param string $type 'update' or 'missing'
	 * @return Advertisement[] Array with Advertisement objects.
	 */
	public static function getTranslationHelperObjects($clang_id, $type) {
		$query = 'SELECT ad_id FROM '. \rex::getTablePrefix() .'d2u_immo_window_advertising_lang '
				."WHERE clang_id = ". $clang_id ." AND translation_needs_update = 'yes' "
				.'ORDER BY title';
		if($type == 'missing') {
			$query = 'SELECT main.ad_id FROM '. \rex::getTablePrefix() .'d2u_immo_window_advertising AS main '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_immo_window_advertising_lang AS target_lang '
						.'ON main.ad_id = target_lang.ad_id AND target_lang.clang_id = '. $clang_id .' '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_immo_window_advertising_lang AS default_lang '
						.'ON main.ad_id = default_lang.ad_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
					."WHERE target_lang.ad_id IS NULL "
					.'ORDER BY default_lang.title';
			$clang_id = \rex_config::get('d2u_helper', 'default_lang');
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);

		$objects = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$objects[] = new Advertisement($result->getValue("ad_id"), $clang_id);
			$result->next();
		}
		
		return $objects;
    }
	
	/*
	 * Returns the URL of this object.
	 * @param string $including_domain TRUE if Domain name should be included
	 * @return string URL
	 */
	public function getURL($including_domain = FALSE) {
		if($this->url == "") {
			$d2u_immo = \rex_addon::get("d2u_immo");
				
			$parameterArray = [];
			$parameterArray['ad_id'] = $this->ad_id;
			
			$this->url = \rex_getUrl($d2u_immo->getConfig('article_id'), $this->clang_id, $parameterArray, "&");
		}

		if($including_domain) {
			if(rex_addon::get('yrewrite')->isAvailable())  {
				return str_replace(rex_yrewrite::getCurrentDomain()->getUrl() .'/', rex_yrewrite::getCurrentDomain()->getUrl(), rex_yrewrite::getCurrentDomain()->getUrl() . $this->url);
			}
			else {
				return str_replace(\rex::getServer(). '/', \rex::getServer(), \rex::getServer() . $this->url);
			}
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
		$pre_save_advertisement = new Advertisement($this->ad_id, $this->clang_id);
	
		// save priority, but only if new or changed
		if($this->priority != $pre_save_advertisement->priority || $this->ad_id == 0) {
			$this->setPriority();
		}

		if($this->ad_id == 0 || $pre_save_advertisement != $this) {
			$query = \rex::getTablePrefix() ."d2u_immo_window_advertising SET "
					."priority = ". $this->priority .", "
					."picture = '". $this->picture ."', "
					."online_status = '". $this->online_status ."' ";

			if($this->ad_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE ad_id = ". $this->ad_id;
			}

			$result = \rex_sql::factory();
			$result->setQuery($query);
			if($this->ad_id == 0) {
				$this->ad_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		
		if($error == 0) {
			// Save the language specific part
			$pre_save_advertisement = new Advertisement($this->ad_id, $this->clang_id);
			if($pre_save_advertisement != $this) {
				$query = "REPLACE INTO ". \rex::getTablePrefix() ."d2u_immo_window_advertising_lang SET "
						."ad_id = '". $this->ad_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."title = '". addslashes(htmlspecialchars($this->title)) ."', "
						."description = '". addslashes(htmlspecialchars($this->description)) ."', "
						."translation_needs_update = '". $this->translation_needs_update ."', "
						."updatedate = ". time() .", "
						."updateuser = '". \rex::getUser()->getLogin() ."' ";
				$result = \rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
			}
		}
		
		return $error;
	}
	
	/**
	 * Reassigns priority to all Categories in database.
	 */
	private function setPriority() {
		// Pull prios from database
		$query = "SELECT ad_id, priority FROM ". \rex::getTablePrefix() ."d2u_immo_window_advertising "
			."WHERE ad_id <> ". $this->ad_id ." ORDER BY priority";
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		// When priority is too small, set at beginning
		if($this->priority <= 0) {
			$this->priority = 1;
		}
		
		// When prio is too high, simply add at end 
		if($this->priority > $result->getRows()) {
			$this->priority = $result->getRows() + 1;
		}

		$advertisements = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$advertisements[$result->getValue("priority")] = $result->getValue("ad_id");
			$result->next();
		}
		array_splice($advertisements, ($this->priority - 1), 0, array($this->ad_id));

		// Save all prios
		foreach($advertisements as $prio => $ad_id) {
			$query = "UPDATE ". \rex::getTablePrefix() ."d2u_immo_window_advertising "
					."SET priority = ". ($prio + 1) ." " // +1 because array_splice recounts at zero
					."WHERE ad_id = ". $ad_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);
		}
	}
}