<?php
namespace D2U_Immo;

/**
 * Provider export configurations.
 */
class Provider {
	/**
	 * @var string Database ID
	 */
	var $provider_id = "";

	/**
	 * @var string Provider name
	 */
	var $name = "";
	
	/**
	 * @var string Provider interface name.
	 */
	var $type = "";
		
	/**
	 * @var int Redaxo language id. Represents the language, the object should
	 * be exported.
	 */
	var $clang_id = 0;

	/**
	 * @var string Company name (your company name)
	 */
	var $company_name = "";
		
	/**
	 * @var string Company e-mail address (your e-mail address)
	 */
	var $company_email = "";
	
	/**
	 * @var string Customer number (your customer number of the provider)
	 */
	var $customer_number = "";
	
	/**
	 * @var string FTP server address. Needed if transmission type is FTP.
	 */
	var $ftp_server = "";
	
	/**
	 * @var string FTP server username
	 */
	var $ftp_username = "";
	
	/**
	 * @var string FTP server password
	 */
	var $ftp_password = "";

	/**
	 * @var string FTP filename (including file type, normally .zip)
	 */
	var $ftp_filename = "";

	/**
	 * @var string Media manager type for exporting pictures.
	 */
	var $media_manager_type = "d2u_immo_list_tile";
	
	/**
	 * @var string Path where attachments can be found.
	 */
	var $attachment_path = "";
	
	/**
	 * @var string App ID of social networks.
	 */
	var $social_app_id = "";	

	/**
	 * @var string App Secret of social networks.
	 */
	var $social_app_secret = "";

	/**
	 * @var string Twitter or LinkedIn OAuth Token. This token is valid until user revokes it.
	 */
	var $social_oauth_token = "";

	/**
	 * @var string Twitter or LinkedIn OAuth Token Secret. This secret is valid until user
	 * revokes it.
	 */
	var $social_oauth_token_secret = "";

	/**
	 * @var string Twitter or LinkedIn OAuth Token Secret. Expiry time.
	 */
	var $social_oauth_token_valid_until = "";

	/**
	 * @var string Linkedin id.
	 */
	var $linkedin_email = "";

	/**
	 * @var string Linkedin group id.
	 */
	var $linkedin_groupid = "";

	/**
	 * @var string Twitter id.
	 */
	var $twitter_id = "";

	/**
	 * @var string Online status. Either "online" or "offline".
	 */
	var $online_status = "online";

	/**
	 * Fetches the object from database.
	 * @param int $provider_id Object id
	 */
	public function __construct($provider_id) {
		$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_immo_export_provider WHERE provider_id = ". $provider_id;

		$result = \rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			// Wenn Verbindung ueber Redaxo hergestellt wurde
			$this->provider_id = $result->getValue("provider_id");
			$this->name = $result->getValue("name");
			$this->type = $result->getValue("type");
			$this->clang_id = $result->getValue("clang_id");
			$this->customer_number = $result->getValue("customer_number");
			$this->ftp_server = $result->getValue("ftp_server");
			$this->ftp_username = $result->getValue("ftp_username");
			$this->ftp_password = $result->getValue("ftp_password");
			$this->ftp_filename = $result->getValue("ftp_filename");
			$this->company_name = $result->getValue("company_name");
			$this->company_email = $result->getValue("company_email");
			$this->media_manager_type = $result->getValue("media_manager_type");
			$this->online_status = $result->getValue("online_status");
			$this->social_app_id = $result->getValue("social_app_id");
			$this->social_app_secret = $result->getValue("social_app_secret");
			$this->social_oauth_token = $result->getValue("social_oauth_token");
			$this->social_oauth_token_secret = $result->getValue("social_oauth_token_secret");
			$this->social_oauth_token_valid_until = $result->getValue("social_oauth_token_valid_until");
			$this->linkedin_email = $result->getValue("linkedin_email");
			$this->linkedin_groupid = $result->getValue("linkedin_groupid");
			$this->twitter_id = $result->getValue("twitter_id");
		}
	}

	/**
	 * Exports property for provider type OpenImmo XML (ftp based exports).
	 * Export starts only, if changes were made or last export is older than a week.
	 * @return string HTML formatted string with success or failure message.
	 */
	public static function autoexport() {
		$providers = Provider::getAll();
		$message = [];
		
		$error = FALSE;
		
		foreach($providers as $provider) {
			if($provider->isExportPossible() && ($provider->isExportNeeded() || $provider->getLastExportTimestamp() < strtotime("-1 week"))) {
				if(strtolower($provider->type) == "openimmo") {
					$openimmo = new OpenImmo($provider);
					$openimmo_error = $openimmo->export();
					if($openimmo_error != "") {
						$message[] = $provider->name .": ". $openimmo_error;
						print $provider->name .": ". $openimmo_error ."; ";
						$error = TRUE;
					}
					else {
						$message[] = $provider->name .": ". \rex_i18n::msg('d2u_immo_export_success');
					}
				}
				else if(strtolower($provider->type) == "linkedin") {
					$linkedin = new SocialExportLinkedIn($provider);
					if($linkedin->hasAccessToken()) {
						$linkedin_error = $linkedin->export();
						if($mascus_error != "") {
							$message[] = $provider->name .": ". $linkedin_error;
							print $provider->name .": ". $linkedin_error ."; ";
							$error = TRUE;
						}
						else {
							$message[] = $provider->name .": ". \rex_i18n::msg('d2u_immo_export_success');
						}
					}
				}
			}
		}
		
		// Send report
		$d2u_immo = \rex_addon::get("d2u_immo");
		if($d2u_immo->hasConfig('export_failure_email') && $error) {
			$mail = new \rex_mailer();
			$mail->IsHTML(true);
			$mail->CharSet = "utf-8";
			$mail->AddAddress(trim($d2u_immo->getConfig('export_failure_email')));
			$mail->Subject = \rex_i18n::msg('d2u_immo_export_failure_report');
			$mail->Body = implode("<br>", $message);
			$mail->Send();
		}
		
		if($error) {
			return false;
		}
		else {
			print \rex_i18n::msg('d2u_immo_export_success');
			return true;
		}
	}

	/**
	 * Changes the status
	 */
	public function changeStatus() {
		if($this->online_status == "online") {
			if($this->provider_id > 0) {
				$query = "UPDATE ". \rex::getTablePrefix() ."d2u_immo_export_provider "
					."SET online_status = 'offline' "
					."WHERE provider_id = ". $this->provider_id;
				$result = \rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "offline";
		}
		else {
			if($this->provider_id > 0) {
				$query = "UPDATE ". \rex::getTablePrefix() ."d2u_immo_export_provider "
					."SET online_status = 'online' "
					."WHERE provider_id = ". $this->provider_id;
				$result = \rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "online";			
		}
	}
	
	/**
	 * Deletes the object.
	 */
	public function delete() {
		// First delete exported objects
		$exported_properties = ExportedProperty::getAll($this);
		foreach($exported_properties as $exported_property) {
			$exported_property->delete();
		}
		
		// Next delete object
		$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_immo_export_provider "
			."WHERE provider_id = ". $this->provider_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);
	}

	/**
	 * Exports objects for the provider.
	 * @return string Error message
	 */
	public function export() {
		if(strtolower($this->type) == "openimmo") {
			$openimmo = new OpenImmo($this);

			return $openimmo->export();
		}
		else if(strtolower($this->type) == "twitter") {
			return "Schnittstelle ist nicht programmiert.";
		}
		else if(strtolower($this->type) == "linkedin") {
			// Check requirements
			if (!function_exists('curl_init')) {
				return \rex_i18n::msg('d2u_immo_export_failure_curl');
			}
			else if (!class_exists('oauth')) {
				return \rex_i18n::msg('d2u_immo_export_failure_oauth');				
			}

			$linkedin = new SocialExportLinkedIn($this);
			if(!$linkedin->hasAccessToken()) {
				if(!filter_input(INPUT_GET, 'oauth_verifier', FILTER_NULL_ON_FAILURE) && !isset($_SESSION['linkedin']['requesttoken'])) {
					// Verifier pin and Requesttoken not available? Login
					$rt_error = $linkedin->getRequestToken();
					if($rt_error == "") {
						// Forward to login URL
						header("Location: ". $linkedin->getLoginURL());
						exit;
					}
					else {
						return $rt_error;
					}
				}
				else if(filter_input(INPUT_GET, 'oauth_verifier', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 && isset($_SESSION['linkedin']['requesttoken'])) {
					// Logged in an verifiert pin available? Get access token and ...
					$at_error = $linkedin->getAccessToken(filter_input(INPUT_GET, 'oauth_verifier', FILTER_VALIDATE_INT));
					if($at_error != "") {
						return $at_error;
					}
				}
				// Fuer den Fall dass mehrere Profile da sind und Requesttoken schon geholt wurde.
				else if (isset($_SESSION['linkedin']['requesttoken'])) {
					// Login URL
					header("Location: ". $linkedin->getLoginURL());
					exit;
				}
			}
			if($linkedin->hasAccessToken()) {
				// set the access token so we can make authenticated requests
				$is_logged_in = $linkedin->isUserLoggedIn();
				if($is_logged_in === FALSE) {
					// Wrong user? Logout and inform user
					$linkedin->logout();
					return \rex_i18n('d2u_immo_export_linkedin_login_again');
				}
				else if($is_logged_in === TRUE) {
					// Correct user? Perform export
					return $linkedin->export();
				}
				else {
					// Login error occured: inform user
					return $is_logged_in;
				}
			}
		}
	}
	
	/**
	 * Get all providers.
	 * @param boolean $online_only Return only online (active) providers
	 * @return Provider[] Array with Provider objects.
	 */
	public static function getAll($online_only = TRUE) {
		$query = "SELECT provider_id FROM ". \rex::getTablePrefix() ."d2u_immo_export_provider ";
		if($online_only) {
			$query .= "WHERE online_status = 'online' ";
		}
		$query .= "ORDER BY name";
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$providers = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$providers[] = new Provider($result->getValue("provider_id"));
			$result->next();
		}
		return $providers;
	}
	
	/**
	 * Checks if an export is needed. This is the case if:
	 * a) An object needs to be deleted from export
	 * b) An object is updated after the last export
	 * @return int Timestamp of latest object update.
	 */
	public function isExportNeeded() {
		$query = "SELECT export_action FROM ". \rex::getTablePrefix() ."d2u_immo_export_properties "
			."WHERE provider_id = ". $this->provider_id ." AND export_action = 'delete'";
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		if($result->getRows() > 0) {
			return TRUE;
		}
		
		$query = "SELECT properties.updatedate, export.export_timestamp FROM ". \rex::getTablePrefix() ."d2u_immo_properties_lang AS properties "
			."LEFT JOIN ". \rex::getTablePrefix() ."d2u_immo_export_properties AS export ON properties.property_id = export.property_id "
			."WHERE provider_id = ". $this->provider_id ." AND clang_id = ". $this->clang_id ." "
			."ORDER BY properties.updatedate DESC LIMIT 0, 1";
		$result->setQuery($query);
		
		if($result->getRows() > 0 && $result->getValue("updatedate") > $result->getValue("export_timestamp")) {
			return TRUE;
		}
		
		return FALSE;
	}

	/**
	 * Checks if an export is possible. This is the case if there are objects
	 * set for export.
	 * @return int Timestamp of latest object update.
	 */
	public function isExportPossible() {
		$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_immo_export_properties "
			."WHERE provider_id = ". $this->provider_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		if($result->getRows() > 0) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Get last export timestamp.
	 * @return string Timestamp of last successful export.
	 */
	public function getLastExportTimestamp() {
		$query = "SELECT export_timestamp FROM ". \rex::getTablePrefix() ."d2u_immo_export_properties "
			."WHERE provider_id = ". $this->provider_id ." "
			."ORDER BY export_timestamp DESC LIMIT 0, 1";
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$time = "";
		if($result->getRows() > 0) {
			$time = $result->getValue("export_timestamp");
		}
		return $time;
	}

	/**
	 * Counts the number of online properties for this provider.
	 * @return int Number of online properties
	 */
	public function getNumberOnlineProperties() {
		$query = "SELECT COUNT(*) as number FROM ". \rex::getTablePrefix() ."d2u_immo_export_properties "
			. "WHERE provider_id = ". $this->provider_id ." AND export_action <> 'delete'";
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		return $result->getValue("number");
	}
	
	/**
	 * Updates or inserts the object into database.
	 * @return boolean TRUE if successful
	 */
	public function save() {
		$this->clang_id = $this->clang_id === 0 ? \intval(rex_config::get("d2u_helper", "default_lang")) : $this->clang_id;

		$query = \rex::getTablePrefix() ."d2u_immo_export_provider SET "
				."name = '". $this->name ."', "
				."type = '". $this->type ."', "
				."clang_id = ". $this->clang_id .", "
				."company_name = '". $this->company_name ."', "
				."company_email = '". $this->company_email ."', "
				."customer_number = '". $this->customer_number ."', "
				."media_manager_type = '". $this->media_manager_type ."', "
				."online_status = '". $this->online_status ."', "
				."ftp_server = '". $this->ftp_server ."', "
				."ftp_username = '". $this->ftp_username ."', "
				."ftp_password = '". $this->ftp_password ."', "
				."ftp_filename = '". $this->ftp_filename ."', "
				."social_app_id = '". $this->social_app_id ."', "
				."social_app_secret = '". $this->social_app_secret ."', "
				."social_oauth_token = '". $this->social_oauth_token ."', "
				."social_oauth_token_secret = '". $this->social_oauth_token_secret ."', "
				."social_oauth_token_valid_until = '". $this->social_oauth_token_valid_until ."', "
				."linkedin_email = '". $this->linkedin_email ."', "
				."linkedin_groupid = '". $this->linkedin_groupid ."', "
				."twitter_id = '". $this->twitter_id ."' ";

		if($this->provider_id == 0) {
			$query = "INSERT INTO ". $query;
		}
		else {
			$query = "UPDATE ". $query ." WHERE provider_id = ". $this->provider_id;
		}

		$result = \rex_sql::factory();
		$result->setQuery($query);
		if($this->provider_id == 0) {
			$this->provider_id = $result->getLastId();
		}

		if($result->hasError()) {
			return FALSE;
		}
		
		return TRUE;
	}
}