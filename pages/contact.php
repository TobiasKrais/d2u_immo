<?php

$func = rex_request('func', 'string');
$entry_id = rex_request('entry_id', 'int');
$message = rex_get('message', 'string');

// Print comments
if ('' !== $message) {
    echo rex_view::success(rex_i18n::msg($message));
}

// save settings
if (1 === (int) filter_input(INPUT_POST, 'btn_save') || 1 === (int) filter_input(INPUT_POST, 'btn_apply')) {
    $form = rex_post('form', 'array', []);

    // Media fields and links need special treatment
    $input_media = rex_post('REX_INPUT_MEDIA', 'array', []);

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
    if ($contact->save()) {
        $message = 'form_saved';
    }

    // Redirect to make reload and thus double save impossible
    if (1 === (int) filter_input(INPUT_POST, 'btn_apply', FILTER_VALIDATE_INT) && $contact->contact_id > 0) {
        header('Location: '. rex_url::currentBackendPage(['entry_id' => $contact->contact_id, 'func' => 'edit', 'message' => $message], false));
    } else {
        header('Location: '. rex_url::currentBackendPage(['message' => $message], false));
    }
    exit;
}
// Delete
if (1 === (int) filter_input(INPUT_POST, 'btn_delete', FILTER_VALIDATE_INT) || 'delete' === $func) {
    $contact_id = $entry_id;
    if (0 === $contact_id) {
        $form = rex_post('form', 'array', []);
        $contact_id = $form['contact_id'];
    }
    $contact = new D2U_Immo\Contact($contact_id);

    // If contact is not used by at least one property, delete it
    if (!$contact->hasProperties()) {
        $contact->delete();
    } else {
        // Check if object is used
        $uses_properties = $contact->getProperties();

        $message = '<ul>';
        foreach ($uses_properties as $uses_property) {
            $message .= '<li><a href="index.php?page=d2u_immo/property&func=edit&entry_id='. $uses_property->property_id .'">'. $uses_property->name.'</a></li>';
        }
        $message .= '</ul>';

        echo rex_view::error(rex_i18n::msg('d2u_helper_could_not_delete') . $message);
    }

    $func = '';
}

// Eingabeformular
if ('edit' === $func || 'clone' === $func || 'add' === $func) {
?>
	<form action="<?= rex_url::currentBackendPage() ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?= rex_i18n::msg('d2u_immo_contact') ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[contact_id]" value="<?= 'edit' === $func ? $entry_id : 0 ?>">
				<fieldset>
					<legend><?= rex_i18n::msg('d2u_immo_contact') ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
                            $contact = new D2U_Immo\Contact($entry_id);
                            $readonly = true;
                            if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_immo[edit_data]'))) {
                                $readonly = false;
                            }

                            \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_immo_contact_firstname', 'form[firstname]', $contact->firstname, true, $readonly);
                            \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_immo_contact_lastname', 'form[lastname]', $contact->lastname, true, $readonly);
                            \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_immo_contact_company', 'form[company]', $contact->company, false, $readonly);
                            \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_immo_contact_street', 'form[street]', $contact->street, true, $readonly);
                            \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_immo_house_number', 'form[house_number]', $contact->house_number, true, $readonly);
                            \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_immo_contact_zip_code', 'form[zip_code]', $contact->zip_code, true, $readonly);
                            \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_immo_city', 'form[city]', $contact->city, true, $readonly);
                            \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_immo_country_code', 'form[country_code]', $contact->country_code, false, $readonly);
                            \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_immo_contact_email', 'form[email]', $contact->email, true, $readonly, 'email');
                            \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_immo_contact_phone', 'form[phone]', $contact->phone, true, $readonly);
                            \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_immo_contact_mobile', 'form[mobile]', $contact->mobile, false, $readonly);
                            \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_immo_contact_fax', 'form[fax]', $contact->fax, false, $readonly);
                            \TobiasKrais\D2UHelper\BackendHelper::form_mediafield('d2u_helper_picture', '1', $contact->picture, $readonly);
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
                            if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_immo[edit_data]'))) {
                                echo '<button class="btn btn-delete" type="submit" name="btn_delete" formnovalidate="formnovalidate" data-confirm="'. rex_i18n::msg('form_delete') .'?" value="1">'. rex_i18n::msg('form_delete') .'</button>';
                            }
                        ?>
					</div>
				</div>
			</footer>
		</div>
	</form>
	<br>
	<script>
		jQuery(document).ready(function($) {
			$('legend').each(function() {
				$(this).addClass('open');
				$(this).next('.panel-body-wrapper.slide').slideToggle();
			});
		});
	</script>
	<?php
        echo \TobiasKrais\D2UHelper\BackendHelper::getCSS();
//		print \TobiasKrais\D2UHelper\BackendHelper::getJS();
}

if ('' === $func) {
    $query = 'SELECT contact_id, firstname, lastname, company '
        . 'FROM '. \rex::getTablePrefix() .'d2u_immo_contacts ';
    $list = rex_list::factory(query:$query, rowsPerPage:1000, defaultSort:['lastname' => 'ASC', 'firstname' => 'ASC']);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon rex-icon-user"></i>';
    $thIcon = '';
    if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_immo[edit_data]'))) {
        $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
    }
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###contact_id###']);

    $list->setColumnLabel('contact_id', rex_i18n::msg('id'));
    $list->setColumnLayout('contact_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);
    $list->setColumnSortable('contact_id');

    $list->setColumnLabel('firstname', rex_i18n::msg('d2u_immo_contact_firstname'));
    $list->setColumnParams('firstname', ['func' => 'edit', 'entry_id' => '###contact_id###']);
    $list->setColumnSortable('firstname');

    $list->setColumnLabel('lastname', rex_i18n::msg('d2u_immo_contact_lastname'));
    $list->setColumnParams('lastname', ['func' => 'edit', 'entry_id' => '###contact_id###']);
    $list->setColumnSortable('lastname');

    $list->setColumnLabel('company', rex_i18n::msg('d2u_immo_contact_company'));
    $list->setColumnSortable('company');

    if (rex::getUser() instanceof rex_user && (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_immo[edit_data]'))) {
        $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
        $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="3">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
        $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###contact_id###']);

        $list->addColumn(rex_i18n::msg('d2u_helper_clone'), '<i class="rex-icon fa-copy"></i> ' . rex_i18n::msg('d2u_helper_clone'));
        $list->setColumnLayout(rex_i18n::msg('d2u_helper_clone'), ['', '<td class="rex-table-action">###VALUE###</td>']);
        $list->setColumnParams(rex_i18n::msg('d2u_helper_clone'), ['func' => 'clone', 'entry_id' => '###contact_id###']);

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
