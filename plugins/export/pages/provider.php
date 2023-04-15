<?php
$func = rex_request('func', 'string');
$entry_id = (int) rex_request('entry_id', 'int');
$message = rex_get('message', 'string');

// Print message
if ('' !== $message) {
    echo rex_view::success(rex_i18n::msg($message));
}

// save settings
if (1 === (int) filter_input(INPUT_POST, 'btn_save') || 1 === (int) filter_input(INPUT_POST, 'btn_apply')) {
    $form = rex_post('form', 'array', []);

    $provider = new D2U_Immo\Provider($form['provider_id']);
    $provider->name = $form['name'];
    $provider->type = $form['type'];
    $provider->clang_id = $form['clang_id'];
    $provider->company_name = $form['company_name'];
    $provider->company_email = $form['company_email'];
    $provider->customer_number = $form['customer_number'];
    $provider->media_manager_type = $form['media_manager_type'];
    $provider->online_status = array_key_exists('online_status', $form) ? 'online' : 'offline';
    $provider->ftp_server = $form['ftp_server'];
    $provider->ftp_username = $form['ftp_username'];
    $provider->ftp_password = $form['ftp_password'];
    $provider->ftp_filename = $form['ftp_filename'];
    $provider->ftp_supports_360_pictures = array_key_exists('ftp_supports_360_pictures', $form);
    $provider->social_app_id = $form['social_app_id'];
    $provider->social_app_secret = $form['social_app_secret'];
    $provider->linkedin_email = $form['linkedin_email'];
    $provider->linkedin_groupid = $form['linkedin_groupid'];

    if (!$provider->save()) {
        $message = 'form_save_error';
    } else {
        $message = 'form_saved';
    }

    // Redirect to make reload and thus double save impossible
    if (1 === (int) filter_input(INPUT_POST, 'btn_apply', FILTER_VALIDATE_INT) && 'form_save_error' !== $message) {
        header('Location: '. rex_url::currentBackendPage(['entry_id' => $provider->provider_id, 'func' => 'edit', 'message' => $message], false));
    } else {
        header('Location: '. rex_url::currentBackendPage(['message' => $message], false));
    }
    exit;
}
// Delete
if (1 === (int) filter_input(INPUT_POST, 'btn_delete', FILTER_VALIDATE_INT) || 'delete' === $func) {
    $provider_id = $entry_id;
    if (0 === $provider_id) {
        $form = rex_post('form', 'array', []);
        $provider_id = $form['entry_id'];
    }
    if ($provider_id > 0) {
        $provider = new D2U_Immo\Provider($provider_id);
        $provider->delete();
    }
    $func = '';
}
// Change online status of machine
elseif ('changestatus' === $func) {
    $provider = new \D2U_Immo\Provider($entry_id);
    $provider->changeStatus();

    header('Location: '. rex_url::currentBackendPage());
    exit;
}
// Eingabeformular
if ('edit' === $func || 'add' === $func) {
?>
	<form action="<?= rex_url::currentBackendPage() ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?= rex_i18n::msg('d2u_immo_export') ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[provider_id]" value="<?= $entry_id ?>">
				<fieldset>
					<legend><?= rex_i18n::msg('d2u_immo_export_basic_settings') ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
                            $provider = new D2U_Immo\Provider($entry_id);
                            $readonly = false;

                            d2u_addon_backend_helper::form_input('d2u_helper_name', 'form[name]', $provider->name, true, $readonly, 'text');
                            $options = ['openimmo' => rex_i18n::msg('d2u_immo_export_openimmo'),
                                'linkedin' => rex_i18n::msg('d2u_immo_export_linkedin')];
                            d2u_addon_backend_helper::form_select('d2u_immo_export_type', 'form[type]', $options, [$provider->type], 1, false, $readonly);

                            $options_lang = [];
                            foreach (rex_clang::getAll() as $rex_clang) {
                                $options_lang[$rex_clang->getId()] = $rex_clang->getName();
                            }
                            d2u_addon_backend_helper::form_select('d2u_immo_export_clang', 'form[clang_id]', $options_lang, [$provider->clang_id]);
                            d2u_addon_backend_helper::form_input('d2u_immo_export_company_name', 'form[company_name]', $provider->company_name, true, $readonly, 'text');
                            d2u_addon_backend_helper::form_input('d2u_immo_export_company_email', 'form[company_email]', $provider->company_email, true, $readonly, 'email');
                            d2u_addon_backend_helper::form_input('d2u_immo_export_customer_number', 'form[customer_number]', $provider->customer_number, false, $readonly, 'text');
                            $options_media = [];
                            $media_sql = rex_sql::factory();
                            $media_sql->setQuery('SELECT name FROM '. rex::getTablePrefix() .'media_manager_type');
                            for ($i = 0; $i < $media_sql->getRows(); ++$i) {
                                $options_media[(string) $media_sql->getValue('name')] = (string) $media_sql->getValue('name');
                                $media_sql->next();
                            }
                            d2u_addon_backend_helper::form_select('d2u_immo_export_media_manager_type', 'form[media_manager_type]', $options_media, [$provider->media_manager_type]);
                            d2u_addon_backend_helper::form_checkbox('d2u_helper_online_status', 'form[online_status]', 'online', 'online' === $provider->online_status, $readonly);
                        ?>
					</div>
				</fieldset>
				<fieldset>
					<legend><?= rex_i18n::msg('d2u_immo_export_ftp_settings') ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
                            d2u_addon_backend_helper::form_input('d2u_immo_export_ftp_server', 'form[ftp_server]', $provider->ftp_server, false, $readonly, 'text');
                            d2u_addon_backend_helper::form_input('d2u_immo_export_ftp_username', 'form[ftp_username]', $provider->ftp_username, false, $readonly, 'text');
                            d2u_addon_backend_helper::form_input('d2u_immo_export_ftp_password', 'form[ftp_password]', $provider->ftp_password, false, $readonly, 'text');
                            d2u_addon_backend_helper::form_input('d2u_immo_export_ftp_filename', 'form[ftp_filename]', $provider->ftp_filename, false, $readonly, 'text');
                            d2u_addon_backend_helper::form_checkbox('d2u_immo_export_ftp_supports_360_pictures', 'form[ftp_supports_360_pictures]', 'true', $provider->ftp_supports_360_pictures, $readonly);
                        ?>
					</div>
				</fieldset>
				<fieldset>
					<legend><?= rex_i18n::msg('d2u_immo_export_social_settings') ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
                            d2u_addon_backend_helper::form_input('d2u_immo_export_social_app_id', 'form[social_app_id]', $provider->social_app_id, false, $readonly, 'text');
                            d2u_addon_backend_helper::form_input('d2u_immo_export_social_app_secret', 'form[social_app_secret]', $provider->social_app_secret, false, $readonly, 'text');
                        ?>
					</div>
				</fieldset>
				<fieldset>
					<legend><?= rex_i18n::msg('d2u_immo_export_social_settings_linkedin') ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
                            d2u_addon_backend_helper::form_input('d2u_immo_export_login_email', 'form[linkedin_email]', $provider->linkedin_email, false, $readonly, 'text');
                            d2u_addon_backend_helper::form_input('d2u_immo_export_linkedin_groupid', 'form[linkedin_groupid]', $provider->linkedin_groupid, false, $readonly, 'text');
                        ?>
					</div>
				</fieldset>
			</div>
			<footer class="panel-footer">
				<div class="rex-form-panel-footer">
					<div class="btn-toolbar">
						<button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="1"><?= rex_i18n::msg('form_save') ?></button>
						<button class="btn btn-apply" type="submit" name="btn_apply" value="1"><?= rex_i18n::msg('form_apply') ?></button>
						<button class="btn btn-abort" type="submit" name="btn_abort" formnovalidate="formnovalidate" value="1"><?= rex_i18n::msg('form_abort') ?></button>
						<?php
                            if (rex::getUser() instanceof rex_user && (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_immo[edit_data]'))) {
                                echo '<button class="btn btn-delete" type="submit" name="btn_delete" formnovalidate="formnovalidate" data-confirm="'. rex_i18n::msg('form_delete') .'?" value="1">'. rex_i18n::msg('form_delete') .'</button>';
                            }
                        ?>
					</div>
				</div>
			</footer>
		</div>
	</form>
	<br>
	<?php
        echo d2u_addon_backend_helper::getCSS();
        echo d2u_addon_backend_helper::getJS();
}

if ('' === $func) {
    $query = 'SELECT provider_id, name, type, online_status '
        .'FROM '. rex::getTablePrefix() .'d2u_immo_export_provider '
        .'ORDER BY name';
    $list = rex_list::factory($query, 1000);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon fa-cloud"></i>';
    $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###provider_id###']);

    $list->setColumnLabel('provider_id', rex_i18n::msg('id'));
    $list->setColumnLayout('provider_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);

    $list->setColumnLabel('name', rex_i18n::msg('d2u_helper_name'));
    $list->setColumnParams('name', ['func' => 'edit', 'entry_id' => '###provider_id###']);

    $list->setColumnLabel('type', rex_i18n::msg('d2u_immo_export_type'));

    $list->removeColumn('online_status');
    $list->addColumn(rex_i18n::msg('status_online'), '<a class="rex-###online_status###" href="' . rex_url::currentBackendPage(['func' => 'changestatus']) . '&entry_id=###provider_id###"><i class="rex-icon rex-icon-###online_status###"></i> ###online_status###</a>');
    $list->setColumnLayout(rex_i18n::msg('status_online'), ['', '<td class="rex-table-action">###VALUE###</td>']);

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###provider_id###']);

    $list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
    $list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###provider_id###']);
    $list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));

    $list->setNoRowsMessage(rex_i18n::msg('d2u_immo_export_no_providers_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_immo_export'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}
