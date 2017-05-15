<?php
/**
 * Offers helper functions for language issues
 */
class d2u_immo_lang_helper {
	/**
	 * @var string[] Array with german replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_german = ['d2u_immo_form_address' => 'Straße, Nr.',
		'd2u_immo_form_captcha' => 'Um Missbrauch vorzubeugen bitten wir Sie das Captcha einzugeben.',
		'd2u_immo_form_city' => 'Ort',
		'd2u_immo_form_company' => 'Firma',
		'd2u_immo_form_country' => 'Land',
		'd2u_immo_form_email' => 'E-Mail Adresse',
		'd2u_immo_form_message' => 'Bemerkungen',
		'd2u_immo_form_name' => 'Name',
		'd2u_immo_form_phone' => 'Telefon',
		'd2u_immo_form_required' => 'Pflichtfelder',
		'd2u_immo_form_send' => 'Abschicken',
		'd2u_immo_form_thanks' => 'Danke für Ihre Nachricht. Wir kümmern uns schnellst möglich um Ihr Anliegen.',
		'd2u_immo_form_validate_address' => 'Bitte geben Sie Straße und Hausnummer an.',
		'd2u_immo_form_validate_captcha' => 'Bitte geben Sie erneut das Captcha ein.',
		'd2u_immo_form_validate_city' => 'Bitte geben Sie Ihren Ort ein.',
		'd2u_immo_form_validate_company' => 'Bitte geben Sie den Namen Ihrer Firma ein.',
		'd2u_immo_form_validate_country' => 'Bitte geben Sie Ihr Land ein.',
		'd2u_immo_form_validate_email' => 'Bitte geben Sie eine E-Mail Adresse ein unter der wir Sie erreichen können.',
		'd2u_immo_form_validate_email_false' => 'Bitte prüfen Sie die E-Mail Adresse auf Korrektheit.',
		'd2u_immo_form_validate_name' => 'Um Sie korrekt ansprechen zu können, geben Sie bitte Ihren Namen an.',
		'd2u_immo_form_validate_phone' => 'Bitte geben Sie Ihre Telefonnummer ein.',
		'd2u_immo_form_validate_title' => 'Fehler beim Senden:',
		'd2u_immo_form_validate_zip' => 'Bitte geben Sie Ihre Postleitzahl ein.',
		'd2u_immo_form_zip' => 'PLZ',
		'd2u_immo_no_machines' => 'Es tut uns Leid. Im Moment haben wir keine Angebote verfügbar. Bitte versuchen Sie es später nocheinmal.',
		'd2u_immo_overview' => 'Übersicht',
		'd2u_immo_request' => 'Anfrage',
	];
	
	/**
	 * Factory method.
	 * @return d2u_immo_lang_helper Object
	 */
	public static function factory() {
		return new d2u_immo_lang_helper();
	}
	
	/**
	 * Installs the replacement table for this addon.
	 */
	public function install() {
		$d2u_immo = rex_addon::get('d2u_immo');
		
		foreach($this->replacements_english as $key => $value) {
			$addWildcard = rex_sql::factory();

			foreach (rex_clang::getAllIds() as $clang_id) {
				// Load values for input
				if($d2u_immo->hasConfig('lang_replacement_'. $clang_id) && $d2u_immo->getConfig('lang_replacement_'. $clang_id) == 'german') {
					$value = $this->replacements_german[$key];
				}
				else { 
					$value = $this->replacements_english[$key];
				}

				if(rex_addon::get('sprog')->isAvailable()) {
					$select_pid_query = "SELECT pid FROM ". rex::getTablePrefix() ."sprog_wildcard WHERE wildcard = '". $key ."' AND clang_id = ". $clang_id;
					$select_pid_sql = rex_sql::factory();
					$select_pid_sql->setQuery($select_pid_query);
					if($select_pid_sql->getRows() > 0) {
						// Update
						$query = "UPDATE ". rex::getTablePrefix() ."sprog_wildcard SET "
							."`replace` = '". addslashes($value) ."', "
							."updatedate = '". rex_sql::datetime() ."', "
							."updateuser = '". rex::getUser()->getValue('login') ."' "
							."WHERE pid = ". $select_pid_sql->getValue('pid');
						$sql = rex_sql::factory();
						$sql->setQuery($query);						
					}
					else {
						$id = 1;
						// Before inserting: id (not pid) must be same in all langs
						$select_id_query = "SELECT id FROM ". rex::getTablePrefix() ."sprog_wildcard WHERE wildcard = '". $key ."' AND id > 0";
						$select_id_sql = rex_sql::factory();
						$select_id_sql->setQuery($select_id_query);
						if($select_id_sql->getRows() > 0) {
							$id = $select_id_sql->getValue('id');
						}
						else {
							$select_id_query = "SELECT MAX(id) + 1 AS max_id FROM ". rex::getTablePrefix() ."sprog_wildcard";
							$select_id_sql = rex_sql::factory();
							$select_id_sql->setQuery($select_id_query);
							if($select_id_sql->getValue('max_id') != NULL) {
								$id = $select_id_sql->getValue('max_id');
							}
						}
						// Save
						$query = "INSERT INTO ". rex::getTablePrefix() ."sprog_wildcard SET "
							."id = ". $id .", "
							."clang_id = ". $clang_id .", "
							."wildcard = '". $key ."', "
							."`replace` = '". addslashes($value) ."', "
							."createdate = '". rex_sql::datetime() ."', "
							."createuser = '". rex::getUser()->getValue('login') ."', "
							."updatedate = '". rex_sql::datetime() ."', "
							."updateuser = '". rex::getUser()->getValue('login') ."'";
						$sql = rex_sql::factory();
						$sql->setQuery($query);
					}
				}
			}
		}
	}

	/**
	 * Uninstalls the replacement table for this addon.
	 */
	public function uninstall() {
		foreach($this->replacements_english as $key => $value) {
			if(rex_addon::get('sprog')->isAvailable()) {
				// Delete 
				$query = "DELETE FROM ". rex::getTablePrefix() ."sprog_wildcard WHERE wildcard = '". $key ."'";
				$select = rex_sql::factory();
				$select->setQuery($query);
			}
		}
	}
}