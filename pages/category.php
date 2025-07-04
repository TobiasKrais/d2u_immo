<?php

use D2U_Immo\Category;

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

    $success = true;
    $category = false;
    $category_id = (int) $form['category_id'];
    foreach (rex_clang::getAll() as $rex_clang) {
        if (!$category instanceof D2U_Immo\Category) {
            $category = new D2U_Immo\Category($category_id, $rex_clang->getId());
            $category->category_id = $category_id; // Ensure correct ID in case first language has no object
            if (isset($form['parent_category_id']) && (int) $form['parent_category_id'] > 0) {
                $category->parent_category = new D2U_Immo\Category((int) $form['parent_category_id'], $rex_clang->getId());
            } else {
                $category->parent_category = false;
            }
            $category->priority = (int) $form['priority'];
            $category->picture = $input_media[1];
        } else {
            $category->clang_id = $rex_clang->getId();
        }
        $category->name = $form['lang'][$rex_clang->getId()]['name'];
        $category->teaser = $form['lang'][$rex_clang->getId()]['teaser'];
        $category->translation_needs_update = $form['lang'][$rex_clang->getId()]['translation_needs_update'];

        if ('delete' === $category->translation_needs_update) {
            $category->delete(false);
        } elseif ($category->save() > 0) {
            $success = false;
        } else {
            // remember id, for each database lang object needs same id
            $category_id = $category->category_id;
        }
    }

    // message output
    $message = 'form_save_error';
    if ($success) {
        $message = 'form_saved';
    }

    // Redirect to make reload and thus double save impossible
    if (1 === (int) filter_input(INPUT_POST, 'btn_apply', FILTER_VALIDATE_INT) && false !== $category) {
        header('Location: '. rex_url::currentBackendPage(['entry_id' => $category->category_id, 'func' => 'edit', 'message' => $message], false));
    } else {
        header('Location: '. rex_url::currentBackendPage(['message' => $message], false));
    }
    exit;
}
// Delete
if (1 === (int) filter_input(INPUT_POST, 'btn_delete', FILTER_VALIDATE_INT) || 'delete' === $func) {
    $category_id = $entry_id;
    if (0 === $category_id) {
        $form = rex_post('form', 'array', []);
        $category_id = $form['category_id'];
    }
    $category = new D2U_Immo\Category($category_id, (int) rex_config::get('d2u_helper', 'default_lang'));
    $category->category_id = $category_id; // Ensure correct ID in case language has no object

    // Check if category is used
    $uses_properties = $category->getProperties();
    $uses_categories = $category->getChildren();

    // If not used, delete
    if (0 === count($uses_properties) && 0 === count($uses_categories) && $category_id !== (int) rex_config::get('d2u_immo', 'import_category_id', 0)) {
        $category->delete(true);
    } else {
        $message = '<ul>';
        foreach ($uses_categories as $uses_category) {
            $message .= '<li><a href="index.php?page=d2u_immo/category&func=edit&entry_id='. $uses_category->category_id .'">'. $uses_category->name .'</a></li>';
        }
        foreach ($uses_properties as $uses_property) {
            $message .= '<li><a href="index.php?page=d2u_immo/property&func=edit&entry_id='. $uses_property->property_id .'">'. $uses_property->name .'</a></li>';
        }
        if(0 < (int) rex_config::get('d2u_immo', 'import_category_id', 0)) {
            $message .= '<li><a href="index.php?page=d2u_immo/settings">'. rex_i18n::msg('d2u_immo') .' '. rex_i18n::msg('d2u_helper_settings') .'</a></li>';
        }
        $message .= '</ul>';

        echo rex_view::error(rex_i18n::msg('d2u_helper_could_not_delete') . $message);
    }

    $func = '';
}

// Eingabeformular
if ('edit' === $func || 'add' === $func) {
?>
	<form action="<?= rex_url::currentBackendPage() ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?= rex_i18n::msg('d2u_helper_category') ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[category_id]" value="<?= $entry_id ?>">
				<?php
                    foreach (rex_clang::getAll() as $rex_clang) {
                        $category = new D2U_Immo\Category($entry_id, $rex_clang->getId());
                        $required = $rex_clang->getId() === (int) (rex_config::get('d2u_helper', 'default_lang')) ? true : false;

                        $readonly_lang = true;
                        if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || (\rex::getUser()->hasPerm('d2u_immo[edit_lang]') && \rex::getUser()->getComplexPerm('clang') instanceof rex_clang_perm && \rex::getUser()->getComplexPerm('clang')->hasPerm($rex_clang->getId())))) {
                            $readonly_lang = false;
                        }
                ?>
					<fieldset>
						<legend><?= rex_i18n::msg('d2u_helper_text_lang') .' "'. $rex_clang->getName() .'"' ?></legend>
						<div class="panel-body-wrapper slide">
							<?php
                                if ($rex_clang->getId() !== (int) rex_config::get('d2u_helper', 'default_lang')) {
                                    $options_translations = [];
                                    $options_translations['yes'] = rex_i18n::msg('d2u_helper_translation_needs_update');
                                    $options_translations['no'] = rex_i18n::msg('d2u_helper_translation_is_uptodate');
                                    $options_translations['delete'] = rex_i18n::msg('d2u_helper_translation_delete');
                                    \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, [$category->translation_needs_update], 1, false, $readonly_lang);
                                } else {
                                    echo '<input type="hidden" name="form[lang]['. $rex_clang->getId() .'][translation_needs_update]" value="">';
                                }
                            ?>
							<script>
								// Hide on document load
								$(document).ready(function() {
									toggleClangDetailsView(<?= $rex_clang->getId() ?>);
								});

								// Hide on selection change
								$("select[name='form[lang][<?= $rex_clang->getId() ?>][translation_needs_update]']").on('change', function(e) {
									toggleClangDetailsView(<?= $rex_clang->getId() ?>);
								});
							</script>
							<div id="details_clang_<?= $rex_clang->getId() ?>">
								<?php
                                    \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_helper_name', 'form[lang]['. $rex_clang->getId() .'][name]', $category->name, $required, $readonly_lang, 'text');
                                    \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_immo_teaser', 'form[lang]['. $rex_clang->getId() .'][teaser]', $category->teaser, false, $readonly_lang, 'text');
                                ?>
							</div>
						</div>
					</fieldset>
				<?php
                    }
                ?>
				<fieldset>
					<legend><?= rex_i18n::msg('d2u_helper_data_all_lang') ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
                            // Do not use last object from translations, because you don't know if it exists in DB
                            $category = new D2U_Immo\Category($entry_id, (int) rex_config::get('d2u_helper', 'default_lang'));
                            $readonly = true;
                            if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_immo[edit_data]'))) {
                                $readonly = false;
                            }

                            $options = ['-1' => rex_i18n::msg('d2u_immo_category_parent_none')];
                            $selected_values = [];
                            foreach (D2U_Immo\Category::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $parent_category) {
                                if (!$parent_category->isChild() && $parent_category->category_id !== $category->category_id) {
                                    $options[$parent_category->category_id] = $parent_category->name;
                                }
                            }

                            \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_immo_category_parent', 'form[parent_category_id]', $options, $category->parent_category instanceof Category ? [$category->parent_category->category_id] : [], 1, false, $readonly);
                            \TobiasKrais\D2UHelper\BackendHelper::form_input('header_priority', 'form[priority]', $category->priority, true, $readonly, 'number');
                            \TobiasKrais\D2UHelper\BackendHelper::form_mediafield('d2u_helper_picture', '1', $category->picture, $readonly);
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
	<?php
        echo \TobiasKrais\D2UHelper\BackendHelper::getCSS();
        echo \TobiasKrais\D2UHelper\BackendHelper::getJS();
}

if ('' === $func) {
    $query = 'SELECT categories.category_id, lang.name AS categoryname, parents_lang.name AS parentname, priority '
        . 'FROM '. \rex::getTablePrefix() .'d2u_immo_categories AS categories '
        . 'LEFT JOIN '. \rex::getTablePrefix() .'d2u_immo_categories_lang AS lang '
            . 'ON categories.category_id = lang.category_id AND lang.clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang') .' '
        . 'LEFT JOIN '. \rex::getTablePrefix() .'d2u_immo_categories_lang AS parents_lang '
            . 'ON categories.parent_category_id = parents_lang.category_id AND parents_lang.clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang') .' ';
    $default_sort = [];
    if ('priority' === rex_config::get('d2u_immo', 'default_category_sort')) {
        $default_sort = ['priority' => 'ASC'];
    } else {
        $default_sort = ['categoryname' => 'ASC'];
    }
    $list = rex_list::factory(query:$query, rowsPerPage:1000, defaultSort:$default_sort);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon rex-icon-open-category"></i>';
    $thIcon = '';
    if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_immo[edit_data]'))) {
        $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
    }
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###category_id###']);

    $list->setColumnLabel('category_id', rex_i18n::msg('id'));
    $list->setColumnLayout('category_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);
    $list->setColumnSortable('category_id');

    $list->setColumnLabel('categoryname', rex_i18n::msg('d2u_helper_name'));
    $list->setColumnParams('categoryname', ['func' => 'edit', 'entry_id' => '###category_id###']);
    $list->setColumnSortable('categoryname');

    $list->setColumnLabel('parentname', rex_i18n::msg('d2u_immo_category_parent'));
    $list->setColumnSortable('parentname');

    $list->setColumnLabel('priority', rex_i18n::msg('header_priority'));
    $list->setColumnSortable('priority');

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###category_id###']);

    if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_immo[edit_data]'))) {
        $list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
        $list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
        $list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###category_id###']);
        $list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));
    }

    $list->setNoRowsMessage(rex_i18n::msg('d2u_helper_no_categories_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_helper_categories'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}
