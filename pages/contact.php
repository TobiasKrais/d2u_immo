<?php
$func = rex_request('func', 'string');
$entry_id = rex_request('entry_id', 'int');
$message = rex_get('message', 'string');

// Print comments
if($message != "") {
	print rex_view::success(rex_i18n::msg($message));
}

// save settings
if (filter_input(INPUT_POST, "btn_save") == 1 || filter_input(INPUT_POST, "btn_apply") == 1) {
	$form = (array) rex_post('form', 'array', []);

	// Media fields and links need special treatment
	$input_media = (array) rex_post('REX_INPUT_MEDIA', 'array', []);

	$contact = new D2U_Immo\Contact($form['contact_id']);
	$contact->city = $form['city'];
	$contact->company = $form['company'];
	$contact->country_code = $form['country_code'];
	$contact->email = $form['email'];
	$contact->fax = $form['fax'];
	$contact->firstname = $form['firstname'];
	$contact->house_number = $form['house_number'];
	$contact->lastname = $form['lastname'];
	$contact->mobile = $form['mobile'];
	$contact->phone = $form['phone'];
	$contact->picture = $input_media[1];
	$contact->street = $form['street'];
	$contact->zip_code = $form['zip_code'];

	// message output
	$message = 'form_save_error';
	if($contact->save() == 0) {
		$message = 'form_saved';
	}
	
	// Redirect to make reload and thus double save impossible
	if(filter_input(INPUT_POST, "btn_apply") == 1 && $contact !== FALSE) {
		header("Location: ". rex_url::currentBackendPage(array("entry_id"=>$contact->contact_id, "func"=>'edit', "message"=>$message), FALSE));
	}
	else {
		header("Location: ". rex_url::currentBackendPage(array("message"=>$message), FALSE));
	}
	exit;
}
// Delete
else if(filter_input(INPUT_POST, "btn_delete") == 1 || $func == 'delete') {
	$contact_id = $entry_id;
	if($contact_id == 0) {
		$form = (array) rex_post('form', 'array', []);
		$contact_id = $form['contact_id'];
	}
	$contact = new D2U_Immo\Contact($contact_id);
	
	// Check if object is used
	$uses_properties = $contact->getProperties();
	
	// If not used, delete
	if(count($uses_properties) == 0) {
		$contact = new D2U_Immo\Contact($contact_id);
		$contact->delete();
	}
	else {
		$message = '<ul>';
		foreach($uses_properties as $uses_property) {
			$message .= '<li><a href="index.php?page=d2u_immo/property&func=edit&entry_id='. $uses_property->property_id .'">'. $uses_property->name.'</a></li>';
		}
		$message .= '</ul>';

		print rex_view::error(rex_i18n::msg('d2u_helper_could_not_delete') . $message);
	}
	
	$func = '';
}

// Eingabeformular
if ($func == 'edit' || $func == 'add') {
?>
	<form action="<?php print rex_url::currentBackendPage(); ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?php print rex_i18n::msg('d2u_immo_contact'); ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[contact_id]" value="<?php echo $entry_id; ?>">
				<fieldset>
					<legend><?php echo rex_i18n::msg('d2u_immo_contact'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							$contact = new D2U_Immo\Contact($entry_id);
							$readonly = TRUE;
							if(\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_immo[edit_data]')) {
								$readonly = FALSE;
							}
							
							d2u_addon_backend_helper::form_input('d2u_immo_contact_firstname', 'form[firstname]', $contact->firstname, TRUE, $readonly);
							d2u_addon_backend_helper::form_input('d2u_immo_contact_lastname', 'form[lastname]', $contact->lastname, TRUE, $readonly);
							d2u_addon_backend_helper::form_input('d2u_immo_contact_company', 'form[company]', $contact->company, FALSE, $readonly);
							d2u_addon_backend_helper::form_input('d2u_immo_contact_street', 'form[street]', $contact->street, TRUE, $readonly);
							d2u_addon_backend_helper::form_input('d2u_immo_house_number', 'form[house_number]', $contact->house_number, TRUE, $readonly);
							d2u_addon_backend_helper::form_input('d2u_immo_contact_zip_code', 'form[zip_code]', $contact->zip_code, TRUE, $readonly);
							d2u_addon_backend_helper::form_input('d2u_immo_city', 'form[city]', $contact->city, TRUE, $readonly);
							d2u_addon_backend_helper::form_input('d2u_immo_country_code', 'form[country_code]', $contact->country_code, FALSE, $readonly);
							d2u_addon_backend_helper::form_input('d2u_immo_contact_email', 'form[email]', $contact->email, TRUE, $readonly, 'email');
							d2u_addon_backend_helper::form_input('d2u_immo_contact_phone', 'form[phone]', $contact->phone, TRUE, $readonly);
							d2u_addon_backend_helper::form_input('d2u_immo_contact_mobile', 'form[mobile]', $contact->mobile, FALSE, $readonly);
							d2u_addon_backend_helper::form_input('d2u_immo_contact_fax', 'form[fax]', $contact->fax, FALSE, $readonly);
							d2u_addon_backend_helper::form_mediafield('d2u_helper_picture', '1', $contact->picture, $readonly);
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
							if(\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_immo[edit_data]')) {
								print '<button class="btn btn-delete" type="submit" name="btn_delete" formnovalidate="formnovalidate" data-confirm="'. rex_i18n::msg('form_delete') .'?" value="1">'. rex_i18n::msg('form_delete') .'</button>';
							}
						?>
					</div>
				</div>
			</footer>
		</div>
	</form>
	<br>
	<script type='text/javascript'>
		jQuery(document).ready(function($) {
			$('legend').each(function() {
				$(this).addClass('open');
				$(this).next('.panel-body-wrapper.slide').slideToggle();
			});
		});
	</script>
	<?php
		print d2u_addon_backend_helper::getCSS();
//		print d2u_addon_backend_helper::getJS();
}

if ($func == '') {
	$query = 'SELECT contact_id, firstname, lastname, company '
		. 'FROM '. \rex::getTablePrefix() .'d2u_immo_contacts '
		. 'ORDER BY lastname, firstname ASC';
    $list = rex_list::factory($query);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon rex-icon-user"></i>';
 	$thIcon = "";
	if(\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_immo[edit_data]')) {
		$thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
	}
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###contact_id###']);

    $list->setColumnLabel('contact_id', rex_i18n::msg('id'));
    $list->setColumnLayout('contact_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);

    $list->setColumnLabel('firstname', rex_i18n::msg('d2u_immo_contact_firstname'));
    $list->setColumnParams('firstname', ['func' => 'edit', 'entry_id' => '###contact_id###']);

    $list->setColumnLabel('lastname', rex_i18n::msg('d2u_immo_contact_lastname'));
    $list->setColumnParams('lastname', ['func' => 'edit', 'entry_id' => '###contact_id###']);

    $list->setColumnLabel('company', rex_i18n::msg('d2u_immo_contact_company'));

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###contact_id###']);

	if(rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_immo[edit_data]')) {
		$list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
		$list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
		$list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###contact_id###']);
		$list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));
	}

    $list->setNoRowsMessage(rex_i18n::msg('d2u_immo_contact_no_contacts_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_immo_contacts'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}