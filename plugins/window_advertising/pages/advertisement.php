<?php
$func = rex_request('func', 'string');
$entry_id = rex_request('entry_id', 'int');
$message = rex_get('message', 'string');

// Print comments
if($message !== '') {
	print rex_view::success(rex_i18n::msg($message));
}

// save settings
if (intval(filter_input(INPUT_POST, "btn_save")) === 1 || intval(filter_input(INPUT_POST, "btn_apply")) === 1) {
	$form = rex_post('form', 'array', []);

	// Media fields and links need special treatment
	$input_media = rex_post('REX_INPUT_MEDIA', 'array', []);

	$success = true;
	$advertisement = false;
	$ad_id = $form['ad_id'];

	foreach(rex_clang::getAll() as $rex_clang) {
		if($advertisement === false) {
			$advertisement = new D2U_Immo\Advertisement($ad_id, $rex_clang->getId());
			$advertisement->ad_id = $ad_id; // Ensure correct ID in case first language has no object
			$advertisement->priority = $form['priority'];
			$advertisement->picture = $input_media[1];
			$advertisement->online_status = array_key_exists('online_status', $form) ? 'online' : 'offline';
		}
		else {
			$advertisement->clang_id = $rex_clang->getId();
		}
		$advertisement->title = $form['lang'][$rex_clang->getId()]['title'];
		$advertisement->description = $form['lang'][$rex_clang->getId()]['description'];
		$advertisement->translation_needs_update = $form['lang'][$rex_clang->getId()]['translation_needs_update'];
		
		if($advertisement->translation_needs_update === "delete") {
			$advertisement->delete(false);
		}
		else if($advertisement->save() > 0){
			$success = false;
		}
		else {
			// remember id, for each database lang object needs same id
			$ad_id = $advertisement->ad_id;
		}
	}

	// message output
	$message = 'form_save_error';
	if($success) {
		$message = 'form_saved';
	}

	// Redirect to make reload and thus double save impossible
	if(intval(filter_input(INPUT_POST, "btn_apply", FILTER_VALIDATE_INT)) === 1 &&$advertisement !== false) {
		header("Location: ". rex_url::currentBackendPage(array("entry_id"=>$advertisement->ad_id, "func"=>'edit', "message"=>$message), false));
	}
	else {
		header("Location: ". rex_url::currentBackendPage(["message"=>$message], false));
	}
	exit;
}
// Delete
else if(intval(filter_input(INPUT_POST, "btn_delete", FILTER_VALIDATE_INT)) === 1 || $func === 'delete') {
	$ad_id = $entry_id;
	if($ad_id === 0) {
		$form = rex_post('form', 'array', []);
		$ad_id = $form['ad_id'];
	}
	$advertisement = new D2U_Immo\Advertisement($ad_id, intval(rex_config::get("d2u_helper", "default_lang")));
	$advertisement->ad_id = $ad_id; // Ensure correct ID in case language has no object
	$advertisement->delete();
	
	$func = '';
}
// Change online status of machine
else if($func === 'changestatus') {
	$advertisement = new D2U_Immo\Advertisement($entry_id, intval(rex_config::get("d2u_helper", "default_lang")));
	$advertisement->ad_id = $ad_id; // Ensure correct ID in case language has no object
	$advertisement->changeStatus();
	
	header("Location: ". rex_url::currentBackendPage());
	exit;
}

// Eingabeformular
if ($func === 'edit' || $func === 'add') {
?>
	<form action="<?php print rex_url::currentBackendPage(); ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?php print rex_i18n::msg('d2u_immo_window_advertising_ad'); ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[ad_id]" value="<?php echo $entry_id; ?>">
				<?php
					foreach(rex_clang::getAll() as $rex_clang) {
						$advertisement = new D2U_Immo\Advertisement($entry_id, $rex_clang->getId());
						$required = $rex_clang->getId() === intval(rex_config::get("d2u_helper", "default_lang")) ? true : false;
						
						$readonly_lang = true;
						if(rex::getUser()->isAdmin() || (rex::getUser()->hasPerm('d2u_immo[edit_lang]') && rex::getUser()->getComplexPerm('clang')->hasPerm($rex_clang->getId()))) {
							$readonly_lang = false;
						}
				?>
					<fieldset>
						<legend><?php echo rex_i18n::msg('d2u_helper_text_lang') .' "'. $rex_clang->getName() .'"'; ?></legend>
						<div class="panel-body-wrapper slide">
							<?php
								if($rex_clang->getId() !== intval(rex_config::get("d2u_helper", "default_lang"))) {
									$options_translations = [];
									$options_translations["yes"] = rex_i18n::msg('d2u_helper_translation_needs_update');
									$options_translations["no"] = rex_i18n::msg('d2u_helper_translation_is_uptodate');
									$options_translations["delete"] = rex_i18n::msg('d2u_helper_translation_delete');
									d2u_addon_backend_helper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, [$advertisement->translation_needs_update], 1, false, $readonly_lang);
								}
								else {
									print '<input type="hidden" title="form[lang]['. $rex_clang->getId() .'][translation_needs_update]" value="">';
								}
							?>
							<script>
								// Hide on document load
								$(document).ready(function() {
									toggleClangDetailsView(<?php print $rex_clang->getId(); ?>);
								});

								// Hide on selection change
								$("select[name='form[lang][<?php print $rex_clang->getId(); ?>][translation_needs_update]']").on('change', function(e) {
									toggleClangDetailsView(<?php print $rex_clang->getId(); ?>);
								});
							</script>
							<div id="details_clang_<?php print $rex_clang->getId(); ?>">
								<?php
									d2u_addon_backend_helper::form_input('d2u_immo_window_advertising_title', "form[lang][". $rex_clang->getId() ."][title]", $advertisement->title, $required, $readonly_lang, "text");
									d2u_addon_backend_helper::form_textarea('d2u_immo_window_advertising_description', "form[lang][". $rex_clang->getId() ."][description]", $advertisement->description, 10, false, $readonly_lang, true);
								?>
							</div>
						</div>
					</fieldset>
				<?php
					}
				?>
				<fieldset>
					<legend><?php echo rex_i18n::msg('d2u_helper_data_all_lang'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							// Do not use last object from translations, because you don't know if it exists in DB
							$advertisement = new D2U_Immo\Advertisement($entry_id, intval(rex_config::get("d2u_helper", "default_lang")));
							$readonly = true;
							if(rex::getUser() instanceof rex_user && (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_immo[edit_data]'))) {
								$readonly = false;
							}
							
							d2u_addon_backend_helper::form_input('header_priority', 'form[priority]', $advertisement->priority, true, $readonly, 'number');
							d2u_addon_backend_helper::form_mediafield('d2u_helper_picture', '1', $advertisement->picture, $readonly);
							$options_status = ['online' => rex_i18n::msg('clang_online'),
								'offline' => rex_i18n::msg('clang_offline')];
							d2u_addon_backend_helper::form_select('d2u_immo_status', 'form[online_status]', $options_status, [$advertisement->online_status], 1, false, $readonly);
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
							if(rex::getUser() instanceof rex_user && (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_immo[edit_data]'))) {
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

if ($func === '') {
	$query = 'SELECT advertisements.ad_id, title, priority, online_status '
		. 'FROM '. rex::getTablePrefix() .'d2u_immo_window_advertising AS advertisements '
		. 'LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_window_advertising_lang AS lang '
			. 'ON advertisements.ad_id = lang.ad_id AND lang.clang_id = '. intval(rex_config::get("d2u_helper", "default_lang")) .' '
		.'ORDER BY priority ASC';
    $list = rex_list::factory($query, 1000);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon rex-icon-open-category"></i>';
 	$thIcon = "";
	if(rex::getUser() instanceof rex_user && (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_immo[edit_data]'))) {
		$thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
	}
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###ad_id###']);

    $list->setColumnLabel('ad_id', rex_i18n::msg('id'));
    $list->setColumnLayout('ad_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);

    $list->setColumnLabel('title', rex_i18n::msg('d2u_immo_window_advertising_title'));
    $list->setColumnParams('title', ['func' => 'edit', 'entry_id' => '###ad_id###']);

	$list->setColumnLabel('priority', rex_i18n::msg('header_priority'));

	$list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###ad_id###']);

  	$list->removeColumn('online_status');
	if(rex::getUser() instanceof rex_user && (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_immo[edit_data]'))) {
		$list->addColumn(rex_i18n::msg('status_online'), '<a class="rex-###online_status###" href="' . rex_url::currentBackendPage(['func' => 'changestatus']) . '&entry_id=###ad_id###"><i class="rex-icon rex-icon-###online_status###"></i> ###online_status###</a>');
		$list->setColumnLayout(rex_i18n::msg('status_online'), ['', '<td class="rex-table-action">###VALUE###</td>']);

		$list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
		$list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
		$list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###ad_id###']);
		$list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));
	}

    $list->setNoRowsMessage(rex_i18n::msg('d2u_immo_window_advertising_no_advertisements_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_immo_window_advertising'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}