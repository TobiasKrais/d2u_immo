<?php
$func = rex_request('func', 'string');
$entry_id = rex_request('entry_id', 'int');
$message = rex_get('message', 'string');

// Print message
if($message != "") {
	print rex_view::success(rex_i18n::msg($message));
}

// save settings
if (filter_input(INPUT_POST, "btn_save") == 1 || filter_input(INPUT_POST, "btn_apply") == 1) {
	$form = (array) rex_post('form', 'array', []);

	$provider = new D2U_Immo\Provider($form['provider_id']);
	$provider->name = $form['name'];
	$provider->type = $form['type'];
	$provider->clang_id = $form['clang_id'];
	$provider->company_name = $form['company_name'];
	$provider->company_email = $form['company_email'];
	$provider->customer_number = $form['customer_number'];
	$provider->media_manager_type = $form['media_manager_type'];
	$provider->ftp_server = $form['ftp_server'];
	$provider->ftp_username = $form['ftp_username'];
	$provider->ftp_password = $form['ftp_password'];
	$provider->ftp_filename = $form['ftp_filename'];
	$provider->social_app_id = $form['social_app_id'];
	$provider->social_app_secret = $form['social_app_secret'];
	$provider->facebook_email = $form['facebook_email'];
	$provider->facebook_pageid = $form['facebook_pageid'];
	$provider->linkedin_email = $form['linkedin_email'];
	$provider->linkedin_groupid = $form['linkedin_groupid'];
//	$provider->twitter_id = $form['twitter_id'];

	if($provider->save() == FALSE){
		$message = 'form_save_error';
	}
	else {
		$message = 'form_saved';
	}
	
	// Redirect to make reload and thus double save impossible
	if(filter_input(INPUT_POST, "btn_apply") == 1 && $message != 'form_save_error') {
		header("Location: ". rex_url::currentBackendPage(array("entry_id"=>$provider->provider_id, "func"=>'edit', "message"=>$message), FALSE));
	}
	else {
		header("Location: ". rex_url::currentBackendPage(array("message"=>$message), FALSE));
	}
	exit;
}
// Delete
else if(filter_input(INPUT_POST, "btn_delete") == 1 || $func == 'delete') {
	$provider_id = $entry_id;
	if($provider_id == 0) {
		$form = (array) rex_post('form', 'array', []);
		$provider_id = $form['entry_id'];
	}
	if($provider_id > 0) {
		$provider = new D2U_Immo\Provider($provider_id);
		$provider->delete();
	}
	$func = '';
}

// Eingabeformular
if ($func == 'edit' || $func == 'add') {
?>
	<form action="<?php print rex_url::currentBackendPage(); ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?php print rex_i18n::msg('d2u_immo_export'); ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[provider_id]" value="<?php echo $entry_id; ?>">
				<fieldset>
					<legend><?php echo rex_i18n::msg('d2u_immo_export_basic_settings'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							$provider = new D2U_Immo\Provider($entry_id);
							$readonly = FALSE;
							
							d2u_addon_backend_helper::form_input('d2u_immo_name', 'form[name]', $provider->name, TRUE, $readonly, 'text');
							$options = ['openimmo' => rex_i18n::msg('d2u_immo_export_openimmo'),
								'immobilienscout24' => rex_i18n::msg('d2u_immo_export_immobilienscout24'),
								'facebook' => rex_i18n::msg('d2u_immo_export_facebook'),
								'linkedin' => rex_i18n::msg('d2u_immo_export_linkedin')];
							d2u_addon_backend_helper::form_select('d2u_immo_export_type', 'form[type]', $options, array($provider->type), 1, FALSE, $readonly);
							
							$options_lang = [];
							foreach(rex_clang::getAll() as $rex_clang) {
								$options_lang[$rex_clang->getId()] = $rex_clang->getName();
							}
							d2u_addon_backend_helper::form_select('d2u_immo_export_clang', 'form[clang_id]', $options_lang, array($provider->clang_id));
							d2u_addon_backend_helper::form_input('d2u_immo_export_company_name', 'form[company_name]', $provider->company_name, TRUE, $readonly, 'text');
							d2u_addon_backend_helper::form_input('d2u_immo_export_company_email', 'form[company_email]', $provider->company_email, TRUE, $readonly, 'email');
							d2u_addon_backend_helper::form_input('d2u_immo_export_customer_number', 'form[customer_number]', $provider->customer_number, FALSE, $readonly, 'text');
							$options_media = [];
							$media_sql = rex_sql::factory();
							$media_sql->setQuery("SELECT name FROM ". rex::getTablePrefix() ."media_manager_type");
							for($i = 0; $i < $media_sql->getRows(); $i++) {
								$options_media[$media_sql->getValue("name")] = $media_sql->getValue("name");
								$media_sql->next();
							}
							d2u_addon_backend_helper::form_select('d2u_immo_export_media_manager_type', 'form[media_manager_type]', $options_media, array($provider->media_manager_type));
						?>
					</div>
				</fieldset>
				<fieldset>
					<legend><?php echo rex_i18n::msg('d2u_immo_export_ftp_settings'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							d2u_addon_backend_helper::form_input('d2u_immo_export_ftp_server', "form[ftp_server]", $provider->ftp_server, FALSE, $readonly, "text");
							d2u_addon_backend_helper::form_input('d2u_immo_export_ftp_username', "form[ftp_username]", $provider->ftp_username, FALSE, $readonly, "text");
							d2u_addon_backend_helper::form_input('d2u_immo_export_ftp_password', "form[ftp_password]", $provider->ftp_password, FALSE, $readonly, "text");
							d2u_addon_backend_helper::form_input('d2u_immo_export_ftp_filename', "form[ftp_filename]", $provider->ftp_filename, FALSE, $readonly, "text");
						?>
					</div>
				</fieldset>
				<fieldset>
					<legend><?php echo rex_i18n::msg('d2u_immo_export_social_settings'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							d2u_addon_backend_helper::form_input('d2u_immo_export_social_app_id', "form[social_app_id]", $provider->social_app_id, FALSE, $readonly, "text");
							d2u_addon_backend_helper::form_input('d2u_immo_export_social_app_secret', "form[social_app_secret]", $provider->social_app_secret, FALSE, $readonly, "text");
						?>
					</div>
				</fieldset>
				<fieldset>
					<legend><?php echo rex_i18n::msg('d2u_immo_export_social_settings_facebook'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							d2u_addon_backend_helper::form_input('d2u_immo_export_login_email', "form[facebook_email]", $provider->facebook_email, FALSE, $readonly, "text");
							d2u_addon_backend_helper::form_input('d2u_immo_export_facebook_pageid', "form[facebook_pageid]", $provider->facebook_pageid, FALSE, $readonly, "text");
						?>
					</div>
				</fieldset>
				<fieldset>
					<legend><?php echo rex_i18n::msg('d2u_immo_export_social_settings_linkedin'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							d2u_addon_backend_helper::form_input('d2u_immo_export_login_email', "form[linkedin_email]", $provider->linkedin_email, FALSE, $readonly, "text");
							d2u_addon_backend_helper::form_input('d2u_immo_export_linkedin_groupid', "form[linkedin_groupid]", $provider->linkedin_groupid, FALSE, $readonly, "text");
						?>
					</div>
				</fieldset>
			</div>
			<footer class="panel-footer">
				<div class="rex-form-panel-footer">
					<div class="btn-toolbar">
						<button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="1"><?php echo rex_i18n::msg('form_save'); ?></button>
						<button class="btn btn-apply" type="submit" name="btn_apply" value="1"><?php echo rex_i18n::msg('form_apply'); ?></button>
						<button class="btn btn-abort" type="submit" name="btn_abort" formnovalidate="formnovalidate" value="1"><?php echo rex_i18n::msg('form_abort'); ?></button>
						<?php
							if(rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_immo[edit_data]')) {
								print '<button class="btn btn-delete" type="submit" name="btn_delete" formnovalidate="formnovalidate" data-confirm="'. rex_i18n::msg('form_delete') .'?" value="1">'. rex_i18n::msg('form_delete') .'</button>';
							}
						?>
					</div>
				</div>
			</footer>
		</div>
	</form>
	<br>
	<?php
		print d2u_addon_backend_helper::getCSS();
		print d2u_addon_backend_helper::getJS();
}

if ($func == '') {
	$query = 'SELECT provider_id, name, type '
		.'FROM '. rex::getTablePrefix() .'d2u_immo_export_provider '
		.'ORDER BY name';
    $list = rex_list::factory($query);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon fa-cloud"></i>';
    $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###provider_id###']);

    $list->setColumnLabel('provider_id', rex_i18n::msg('id'));
    $list->setColumnLayout('provider_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);

    $list->setColumnLabel('name', rex_i18n::msg('d2u_immo_name'));
    $list->setColumnParams('name', ['func' => 'edit', 'entry_id' => '###provider_id###']);

    $list->setColumnLabel('type', rex_i18n::msg('d2u_immo_export_type'));

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('system_update'));
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