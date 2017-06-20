<?php
/**
 * LinkedIn export.
 */
class SocialExportLinkedIn extends AExport {
	/**
	 * @var OAuth LinkedIn OAuth object
	 */
	public $oauth;
	
	/**
	 * Constructor. Initializes variables
	 * @param Provider $provider Export Provider
	 */
	public function __construct($provider) {
		parent::__construct($provider);
		
		$this->export_properties = ExportedProperty::getAll($this->provider);
		$this->oauth = new OAuth($this->provider->social_app_id, $this->provider->social_app_secret);
		
		if($this->hasAccessToken()) {
			$this->oauth->setToken($this->provider->social_oauth_token, $this->provider->social_oauth_token_secret);
		}
	}	

	/**
	 * Get callback URL
	 * @return string callback URL
	 */
	public function getCallbackURL() {
		return rex::getServer() ."redaxo/". rex_url::currentBackendPage(['func'=>'export', 'provider_id'=>$this->provider->provider_id], FALSE);
	}

	/**
	 * Get and set access token
	 * @param int $verifier_pin OAuth verifier pin
	 * @return string error message
	 */
	public function getAccessToken($verifier_pin) {
		$requesttoken = $_SESSION['linkedin']['requesttoken'];
		$requesttoken_secret = $_SESSION['linkedin']['requesttoken_secret'];
		unset($_SESSION['linkedin']['requesttoken']);
		unset($_SESSION['linkedin']['requesttoken_secret']);

		try {
			// now set the token so we can get our access token
			$this->oauth->setToken($requesttoken, $requesttoken_secret);

			// get the access token now that we have the verifier pin
			$at_info = $this->oauth->getAccessToken("https://api.linkedin.com/uas/oauth/accessToken", "", $verifier_pin);
			// store in DB
			$this->provider->social_oauth_token = $at_info["oauth_token"];
			$this->provider->social_oauth_token_secret = $at_info["oauth_token_secret"];
			$this->provider->social_oauth_token_valid_until =  time() + $at_info["oauth_expires_in"];
			$this->provider->save();

			// set the access token so we can make authenticated requests
			$this->oauth->setToken($this->provider->social_oauth_token, $this->provider->social_oauth_token_secret);
		}
		catch(OAuthException $e) {
			return $e->getMessage();
		}
		return "";
	}

	/**
	 * Get login URL
	 * @return string Login url
	 */
	public function getLoginURL() {
		return "https://www.linkedin.com/uas/oauth/authenticate?oauth_token=". $_SESSION['linkedin']['requesttoken'];
	}

	/**
	 * Get and set request token
	 * @return string error message
	 */
	public function getRequestToken() {
		try {
			$rt_info = $this->oauth->getRequestToken("https://api.linkedin.com/uas/oauth/requestToken?scope=r_basicprofile+r_emailaddress+w_share", $this->getCallbackURL());
			$_SESSION['linkedin']['requesttoken'] = $rt_info["oauth_token"];
			$_SESSION['linkedin']['requesttoken_secret'] = $rt_info["oauth_token_secret"];
		}
		catch(OAuthException $e) {
			return $e->getMessage();
		}
		return "";
	}
	
	/**
	 * Check if access token is set.
	 * @return boolean TRUE if yes, otherwise FALSE
	 */
	public function hasAccessToken() {
		if($this->provider->social_oauth_token != "" && $this->provider->social_oauth_token_secret != "") {
			if($this->provider->social_oauth_token_valid_until > time()) {
				return TRUE;
			}
			else {
				$this->provider->social_oauth_token = "";
				$this->provider->social_oauth_token_secret = "";
				$this->provider->social_oauth_token_valid_until = 0;
				$this->provider->save();
			}
		}
		return FALSE;
	}

	/**
	 * Is user mentioned in provider logged in?
	 * @return boolean TRUE id yes, FALSE if no or a string with error message
	 */
	public function isUserLoggedIn() {
		try {
			// Fetch id from LinkedIn
			$api_url = "https://api.linkedin.com/v1/people/~:(id,email-address)";
			$this->oauth->fetch($api_url, null, OAUTH_HTTP_METHOD_GET);
			$response = $this->oauth->getLastResponse();

			$linkedin_email = "";
			$xml = new DOMDocument(); 
			$xml->loadXML($response);
			$persons = $xml->getElementsByTagName("person"); 
			foreach($persons as $person) {
				$email = $person->getElementsByTagName("email-address"); 
				$linkedin_email = $email->item(0)->nodeValue;
			}
			if($linkedin_email == "") {
				return rex_i18n::msg('d2u_immo_export_linkedin_mail_failed');
			}
			if(strtolower($linkedin_email) != strtolower($this->provider->linkedin_email)) {
				unset($_SESSION['linkedin']);
				return rex_i18n::msg('d2u_immo_export_linkedin_login_again');
			}
			else {
				return TRUE;
			}
		}
		catch(OAuthException $e) {
			return "Error: ". $e->getMessage() ."<br />";
		}	 
	}	
	
	/**
	 * Export properties.
	 * @return string Error message, empty if no error occured
	 */
	public function export() {
		// First remove all deleted
		ExportedProperty::removeAllDeletedFromExport();
		
		foreach($this->export_properties as $exported_property) {
			$property = new Property($exported_property->property_id, $this->provider->clang_id);
			// Delete from stream
			if($exported_property->export_action == "delete" || $exported_property->export_action == "update") {
				// State April 2015: deleting is not supported
				/*
				if($exported_property->provider_import_id != "") {
					try {
						$this->oauth->fetch($exported_property->provider_import_id, false, OAUTH_HTTP_METHOD_DELETE);
					} catch (OAuthException $e) {
					}
				}
				*/
				
				// delete in database
				if($exported_property->export_action == "delete") {
					$exported_property->delete();
				}
				else {
					$exported_property->export_action = "add";
					$exported_property->provider_import_id = "";
				}
			}

			// Post on wall
			if($exported_property->export_action == "add") {
				// Create XML for LinkedIn Social Stream
				// Documentation: https://developer.linkedin.com/documents/share-api
				// <?xml version="1.0" encoding="UTF-8">
				$xml = new DOMDocument("1.0", "UTF-8");
				$xml->formatOutput = true;

				// Post on Social Stream: prepare XML
				if($this->provider->linkedin_groupid == "") {
					// <share>
					$share = $xml->createElement("share");

					// <comment>Bester Kran auf dem Markt</comment>
					$comment = $xml->createElement("comment");
					$comment->appendChild($xml->createTextNode(Wildcard::get('d2u_immo_export_linkedin_comment_text', $this->provider->clang_id)));
					$share->appendChild($comment);

					// <content>
					$content = $xml->createElement("content");

					// <title>Potain IGO 32</title>
					$title = $xml->createElement("title");
					$title->appendChild($xml->createTextNode($property->name));
					$content->appendChild($title);

					// <description>Price: on request; ... </description>
					$description = $xml->createElement("description");
					$description->appendChild($xml->createTextNode($property->teaser));
					$content->appendChild($description);

					// <submitted-url>http://www.meier-krantechnik.de/de/produkte/gebrauchte-krane?action=detail&item=13</submitted-url>
					$submitted_url = $xml->createElement("submitted-url");
					$submitted_url->appendChild($xml->createTextNode($property->getURL(TRUE)));
					$content->appendChild($submitted_url);

					// <submitted-image-url>http://www.meier-krantechnik.de/index.php?rex_img_type=d2u_baumaschinen_list&amp;rex_img_file=sjjdc_826.jpg</submitted-image-url>
					if(count($property->pictures) > 0) {
						$submitted_image_url = $xml->createElement("submitted-image-url");
						$submitted_image_url->appendChild($xml->createTextNode(rex::getServer() .'index.php?rex_media_type='. $this->provider->media_manager_type .'&rex_media_file='. $property->pictures[0]));
						$content->appendChild($submitted_image_url);
					}

					// </content>
					$share->appendChild($content);

					// <visibility>
					$visibility = $xml->createElement("visibility");

					// <code>anyone</code>
					$code = $xml->createElement("code");
					$code->appendChild($xml->createTextNode("anyone"));
					$visibility->appendChild($code);

					// </visibility>
					$share->appendChild($visibility);

					// </share>
					$xml->appendChild($share);
				}
				// Post on group stream: prepare XML
				else {
					$title_text = $this->provider->company_name ." ". Wildcard::get('d2u_immo_export_linkedin_offers', $this->provider->clang_id) .": ". $property->name;
					$summary_text = Wildcard::get('d2u_immo_export_linkedin_details', $this->provider->clang_id) ." ". $property->getURL(TRUE) ;

					// <post>
					$post = $xml->createElement("post");

					// <title>Potain IGO 32</title>
					$title = $xml->createElement("title");
					$title->appendChild($xml->createTextNode($title_text));
					$post->appendChild($title);

					// <summary>Price: on request; ... </summary>
					$summary = $xml->createElement("summary");
					$summary->appendChild($xml->createTextNode($summary_text));
					$post->appendChild($summary);

					// </share>
					$xml->appendChild($post);
				}

				// Let's post it
				try {
					$api_url = "https://api.linkedin.com/v1/people/~/shares";
					if($this->provider->linkedin_groupid != "") {
						$api_url = "https://api.linkedin.com/v1/groups/". $this->provider->linkedin_groupid ."/posts";
					}

					$this->oauth->fetch($api_url, $xml->saveXML(), OAUTH_HTTP_METHOD_POST, array("Content-Type"=>"text/xml"));

					// Getting stream id
					$response_headers = SocialExportLinkedIn::http_parse_headers($this->oauth->getLastResponseHeaders());
					if($response_headers !== false) {
						if(isset($response_headers["Location"])) {
							$exported_property->provider_import_id = $response_headers["Location"];
						}
						// Save results
						$exported_property->export_action = "";
						$exported_property->export_timestamp = time();
						$exported_property->save();
					}
				} catch (OAuthException $e) {
					return rex_i18n::msg("d2u_immo_export_linkedin_upload_failed") ." ". $e;
				}
			}
		}

		return "";
	}
	
	/**
	 * Parses HTTP headers into an associative array.
	 * @param String $header string containing HTTP headers
	 * @return Returns an array on success or FALSE on failure.
	 */
	private static function http_parse_headers($r) {
		$o = [];
		$r = substr($r, stripos($r, "\r\n"));
		$r = explode("\r\n", $r);
		foreach ($r as $h) {
			list($v, $val) = explode(": ", $h);
			if ($v == null) continue;
			$o[$v] = $val;
		}
		return $o;
	}
	
	/**
	 * Logout by cleaning LinkedIn session vars and removing access token.
	 */
	public function logout() {
		if(isset($_SESSION['linkedin'])) {
			unset($_SESSION['linkedin']);
		}
		
		$this->provider->social_oauth_token = "";
		$this->provider->social_oauth_token_secret = "";
		$this->save();
	}
}