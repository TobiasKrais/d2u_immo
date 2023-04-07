<?php

namespace D2U_Immo;

use rex;
use rex_addon;
use rex_config;
use rex_i18n;
use rex_mailer;

use rex_sql;

use function function_exists;

/**
 * @api
 * Provider export configurations.
 */
class Provider
{
    /** @var int Database ID */
    public int $provider_id = 0;

    /** @var string Provider name */
    public string $name = '';

    /** @var string provider interface name */
    public string $type = '';

    /**
     * @var int Redaxo language id. Represents the language, the object should
     * be exported.
     */
    public int $clang_id = 0;

    /** @var string Company name (your company name) */
    public string $company_name = '';

    /** @var string Company e-mail address (your e-mail address) */
    public string $company_email = '';

    /** @var string Customer number (your customer number of the provider) */
    public string $customer_number = '';

    /** @var string FTP server address. Needed if transmission type is FTP. */
    public string $ftp_server = '';

    /** @var string FTP server username */
    public string $ftp_username = '';

    /** @var string FTP server password */
    public string $ftp_password = '';

    /** @var string FTP filename (including file type, normally .zip) */
    public string $ftp_filename = '';

    /** @var string media manager type for exporting pictures */
    public string $media_manager_type = 'd2u_immo_list_tile';

    /** @var string app ID of social networks */
    public string $social_app_id = '';

    /** @var string app Secret of social networks */
    public string $social_app_secret = '';

    /** @var string Twitter or LinkedIn OAuth Token. This token is valid until user revokes it. */
    public string $social_oauth_token = '';

    /** @var string Twitter or LinkedIn OAuth Token Secret. This secret is valid until user revokes it. */
    public string $social_oauth_token_secret = '';

    /** @var string Twitter or LinkedIn OAuth Token Secret. Expiry time. */
    public string $social_oauth_token_valid_until = '';

    /** @var string linkedin id */
    public string $linkedin_email = '';

    /** @var string linkedin group id */
    public string $linkedin_groupid = '';

    /** @var string Online status. Either "online" or "offline". */
    public string $online_status = 'online';

    /**
     * Fetches the object from database.
     * @param int $provider_id Object id
     */
    public function __construct($provider_id)
    {
        $query = 'SELECT * FROM '. rex::getTablePrefix() .'d2u_immo_export_provider WHERE provider_id = '. $provider_id;

        $result = rex_sql::factory();
        $result->setQuery($query);
        $num_rows = $result->getRows();

        if ($num_rows > 0) {
            // Wenn Verbindung ueber Redaxo hergestellt wurde
            $this->provider_id = (int) $result->getValue('provider_id');
            $this->name = (string) $result->getValue('name');
            $this->type = (string) $result->getValue('type');
            $this->clang_id = (int) $result->getValue('clang_id');
            $this->customer_number = (string) $result->getValue('customer_number');
            $this->ftp_server = (string) $result->getValue('ftp_server');
            $this->ftp_username = (string) $result->getValue('ftp_username');
            $this->ftp_password = (string) $result->getValue('ftp_password');
            $this->ftp_filename = (string) $result->getValue('ftp_filename');
            $this->company_name = (string) $result->getValue('company_name');
            $this->company_email = (string) $result->getValue('company_email');
            $this->media_manager_type = (string) $result->getValue('media_manager_type');
            $this->online_status = (string) $result->getValue('online_status');
            $this->social_app_id = (string) $result->getValue('social_app_id');
            $this->social_app_secret = (string) $result->getValue('social_app_secret');
            $this->social_oauth_token = (string) $result->getValue('social_oauth_token');
            $this->social_oauth_token_secret = (string) $result->getValue('social_oauth_token_secret');
            $this->social_oauth_token_valid_until = (string) $result->getValue('social_oauth_token_valid_until');
            $this->linkedin_email = (string) $result->getValue('linkedin_email');
            $this->linkedin_groupid = (string) $result->getValue('linkedin_groupid');
        }
    }

    /**
     * Exports property for provider type OpenImmo XML (ftp based exports).
     * Export starts only, if changes were made or last export is older than a week.
     * @return bool true if export was successful
     */
    public static function autoexport()
    {
        $providers = self::getAll();
        $message = [];

        $error = false;

        foreach ($providers as $provider) {
            if ($provider->isExportPossible() && ($provider->isExportNeeded() || $provider->getLastExportTimestamp() < date('Y-m-d H:i:s', strtotime('-1 week')))) {
                if ('openimmo' === strtolower($provider->type)) {
                    $openimmo = new OpenImmo($provider);
                    $openimmo_error = $openimmo->export();
                    if ('' !== $openimmo_error) {
                        $message[] = $provider->name .': '. $openimmo_error;
                        echo $provider->name .': '. $openimmo_error .'; ';
                        $error = true;
                    } else {
                        $message[] = $provider->name .': '. rex_i18n::msg('d2u_immo_export_success');
                    }
                } elseif ('linkedin' === strtolower($provider->type)) {
                    $linkedin = new SocialExportLinkedIn($provider);
                    if ($linkedin->hasAccessToken()) {
                        $linkedin_error = $linkedin->export();
                        if ('' !== $linkedin_error) {
                            $message[] = $provider->name .': '. $linkedin_error;
                            echo $provider->name .': '. $linkedin_error .'; ';
                            $error = true;
                        } else {
                            $message[] = $provider->name .': '. rex_i18n::msg('d2u_immo_export_success');
                        }
                    }
                }
            }
        }

        // Send report
        $d2u_immo = rex_addon::get('d2u_immo');
        if ($d2u_immo->hasConfig('export_failure_email') && $error) {
            $mail = new rex_mailer();
            $mail->isHTML(true);
            $mail->CharSet = 'utf-8';
            $mail->addAddress(trim($d2u_immo->getConfig('export_failure_email')));
            $mail->Subject = rex_i18n::msg('d2u_immo_export_failure_report');
            $mail->Body = implode('<br>', $message);
            $mail->send();
        }

        if ($error) {
            return false;
        }

        echo rex_i18n::msg('d2u_immo_export_success');
        return true;

    }

    /**
     * Changes the status.
     */
    public function changeStatus(): void
    {
        if ('online' === $this->online_status) {
            if ($this->provider_id > 0) {
                $query = 'UPDATE '. rex::getTablePrefix() .'d2u_immo_export_provider '
                    ."SET online_status = 'offline' "
                    .'WHERE provider_id = '. $this->provider_id;
                $result = rex_sql::factory();
                $result->setQuery($query);
            }
            $this->online_status = 'offline';
        } else {
            if ($this->provider_id > 0) {
                $query = 'UPDATE '. rex::getTablePrefix() .'d2u_immo_export_provider '
                    ."SET online_status = 'online' "
                    .'WHERE provider_id = '. $this->provider_id;
                $result = rex_sql::factory();
                $result->setQuery($query);
            }
            $this->online_status = 'online';
        }
    }

    /**
     * Deletes the object.
     */
    public function delete(): void
    {
        // First delete exported objects
        $exported_properties = ExportedProperty::getAll($this);
        foreach ($exported_properties as $exported_property) {
            $exported_property->delete();
        }

        // Next delete object
        $query = 'DELETE FROM '. rex::getTablePrefix() .'d2u_immo_export_provider '
            .'WHERE provider_id = '. $this->provider_id;
        $result = rex_sql::factory();
        $result->setQuery($query);
    }

    /**
     * Exports objects for the provider.
     * @return string Error message
     */
    public function export()
    {
        if ('openimmo' === strtolower($this->type)) {
            $openimmo = new OpenImmo($this);

            return $openimmo->export();
        }
        if ('linkedin' === strtolower($this->type)) {
            // Check requirements
            if (!function_exists('curl_init')) {
                return rex_i18n::msg('d2u_immo_export_failure_curl');
            }
            if (!class_exists('oauth')) {
                return rex_i18n::msg('d2u_immo_export_failure_oauth');
            }

            $linkedin = new SocialExportLinkedIn($this);
            if (!$linkedin->hasAccessToken()) {
                $session_linkedin = \rex_request::session('linkedin');
                if (null !== filter_input(INPUT_GET, 'oauth_verifier', FILTER_NULL_ON_FAILURE) && is_array($session_linkedin) && !array_key_exists('requesttoken', $session_linkedin)) {
                    // Verifier pin and Requesttoken not available? Login
                    $rt_error = $linkedin->getRequestToken();
                    if ('' === $rt_error) {
                        // Forward to login URL
                        header('Location: '. $linkedin->getLoginURL());
                        exit;
                    }

                    return $rt_error;

                }
                if (filter_input(INPUT_GET, 'oauth_verifier', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 && is_array($session_linkedin) && array_key_exists('requesttoken', $session_linkedin)) {
                    // Logged in an verifiert pin available? Get access token and ...
                    $at_error = $linkedin->getAccessToken(filter_input(INPUT_GET, 'oauth_verifier', FILTER_VALIDATE_INT));
                    if ('' !== $at_error) {
                        return $at_error;
                    }
                }
                // Fuer den Fall dass mehrere Profile da sind und Requesttoken schon geholt wurde.
                elseif (is_array($session_linkedin) && array_key_exists('requesttoken', $session_linkedin)) {
                    // Login URL
                    header('Location: '. $linkedin->getLoginURL());
                    exit;
                }
            }
            if ($linkedin->hasAccessToken()) {
                // set the access token so we can make authenticated requests
                $is_logged_in = $linkedin->isUserLoggedIn();
                if (!$is_logged_in) {
                    // Wrong user? Logout and inform user
                    $linkedin->logout();
                    return rex_i18n::msg('d2u_immo_export_linkedin_login_again');
                }
                // Correct user? Perform export
                return $linkedin->export();
            }
        }
        return '';
    }

    /**
     * Get all providers.
     * @param bool $online_only Return only online (active) providers
     * @return Provider[] array with Provider objects
     */
    public static function getAll($online_only = true)
    {
        $query = 'SELECT provider_id FROM '. rex::getTablePrefix() .'d2u_immo_export_provider ';
        if ($online_only) {
            $query .= "WHERE online_status = 'online' ";
        }
        $query .= 'ORDER BY name';
        $result = rex_sql::factory();
        $result->setQuery($query);

        $providers = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $providers[] = new self((int) $result->getValue('provider_id'));
            $result->next();
        }
        return $providers;
    }

    /**
     * Checks if an export is needed. This is the case if:
     * a) An object needs to be deleted from export
     * b) An object is updated after the last export.
     * @return bool true if export is needed
     */
    public function isExportNeeded()
    {
        $query = 'SELECT export_action FROM '. rex::getTablePrefix() .'d2u_immo_export_properties '
            .'WHERE provider_id = '. $this->provider_id ." AND export_action = 'delete'";
        $result = rex_sql::factory();
        $result->setQuery($query);

        if ($result->getRows() > 0) {
            return true;
        }

        $query = 'SELECT properties.updatedate, export.export_timestamp FROM '. rex::getTablePrefix() .'d2u_immo_properties_lang AS properties '
            .'LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_export_properties AS export ON properties.property_id = export.property_id '
            .'WHERE provider_id = '. $this->provider_id .' AND clang_id = '. $this->clang_id .' '
            .'ORDER BY properties.updatedate DESC LIMIT 0, 1';
        $result->setQuery($query);

        if ($result->getRows() > 0 && $result->getValue('updatedate') > $result->getValue('export_timestamp')) {
            return true;
        }

        return false;
    }

    /**
     * Checks if an export is possible. This is the case if there are objects
     * set for export.
     * @return bool true if there are objects for export available
     */
    public function isExportPossible()
    {
        $query = 'SELECT * FROM '. rex::getTablePrefix() .'d2u_immo_export_properties '
            .'WHERE provider_id = '. $this->provider_id;
        $result = rex_sql::factory();
        $result->setQuery($query);

        if ($result->getRows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Get last export timestamp.
     * @return string timestamp of last successful export
     */
    public function getLastExportTimestamp()
    {
        $query = 'SELECT export_timestamp FROM '. rex::getTablePrefix() .'d2u_immo_export_properties '
            .'WHERE provider_id = '. $this->provider_id .' '
            .'ORDER BY export_timestamp DESC LIMIT 0, 1';
        $result = rex_sql::factory();
        $result->setQuery($query);

        $time = '';
        if ($result->getRows() > 0) {
            $time = (string) $result->getValue('export_timestamp');
        }
        return $time;
    }

    /**
     * Counts the number of online properties for this provider.
     * @return int Number of online properties
     */
    public function getNumberOnlineProperties()
    {
        $query = 'SELECT COUNT(*) as number FROM '. rex::getTablePrefix() .'d2u_immo_export_properties '
            . 'WHERE provider_id = '. $this->provider_id ." AND export_action <> 'delete'";
        $result = rex_sql::factory();
        $result->setQuery($query);

        return (int) $result->getValue('number');
    }

    /**
     * Updates or inserts the object into database.
     * @return bool true if successful
     */
    public function save()
    {
        $this->clang_id = 0 === $this->clang_id ? (int) (rex_config::get('d2u_helper', 'default_lang')) : $this->clang_id;

        $query = rex::getTablePrefix() .'d2u_immo_export_provider SET '
                ."name = '". $this->name ."', "
                ."type = '". $this->type ."', "
                .'clang_id = '. $this->clang_id .', '
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
                ."linkedin_groupid = '". $this->linkedin_groupid ."' ";

        if (0 === $this->provider_id) {
            $query = 'INSERT INTO '. $query;
        } else {
            $query = 'UPDATE '. $query .' WHERE provider_id = '. $this->provider_id;
        }

        $result = rex_sql::factory();
        $result->setQuery($query);
        if (0 === $this->provider_id) {
            $this->provider_id = (int) $result->getLastId();
        }

        if ($result->hasError()) {
            return false;
        }

        return true;
    }
}
