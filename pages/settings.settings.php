<?php
// save settings
if ('save' === filter_input(INPUT_POST, 'btn_save')) {
    $settings = rex_post('settings', 'array', []);

    // Linkmap Link and media needs special treatment
    $link_ids = filter_input_array(INPUT_POST, ['REX_INPUT_LINK' => ['filter' => FILTER_VALIDATE_INT, 'flags' => FILTER_REQUIRE_ARRAY]]);
    $settings['article_id'] = is_array($link_ids['REX_INPUT_LINK']) ? $link_ids['REX_INPUT_LINK'][1] : 0;
    if (rex_plugin::get('d2u_immo', 'window_advertising')->isAvailable()) {
        $settings['window_advertising_settings_article'] = !is_array($link_ids['REX_INPUT_LINK']) ? 0 : $link_ids['REX_INPUT_LINK'][2];
    }

    $input_media = rex_post('REX_INPUT_MEDIA', 'array', []);
    $settings['even_informative_pdf'] = $input_media['even_informative_pdf'];

    // Checkbox also need special treatment if empty
    $settings['lang_wildcard_overwrite'] = array_key_exists('lang_wildcard_overwrite', $settings) ? 'true' : 'false';
    if (rex_plugin::get('d2u_immo', 'export')->isAvailable()) {
        $settings['export_autoexport'] = array_key_exists('export_autoexport', $settings);
    }
    if (rex_plugin::get('d2u_immo', 'import')->isAvailable()) {
        $settings['import_autoimport'] = array_key_exists('import_autoimport', $settings);
    }

    // Save settings
    if (rex_config::set('d2u_immo', $settings)) {
        echo rex_view::success(rex_i18n::msg('form_saved'));

        // Update url schemes
        if (\rex_addon::get('url')->isAvailable() && $settings['article_id'] > 0) {
            \TobiasKrais\D2UHelper\BackendHelper::update_url_scheme(rex::getTablePrefix() .'d2u_immo_url_categories', $settings['article_id']);
            \TobiasKrais\D2UHelper\BackendHelper::update_url_scheme(rex::getTablePrefix() .'d2u_immo_url_properties', $settings['article_id']);
        }

        // Install / update language replacements
        d2u_immo_lang_helper::factory()->install();
        if (rex_plugin::get('d2u_immo', 'export')->isAvailable()) {
            export_lang_helper::factory()->install();
        }

        // Install / remove Cronjob(s)
        if (rex_plugin::get('d2u_immo', 'export')->isAvailable()) {
            $export_cronjob = d2u_immo_export_cronjob::factory();
            if ((bool) rex_config::get('d2u_immo', 'export_autoexport')) {
                if (!$export_cronjob->isInstalled()) {
                    $export_cronjob->install();
                }
            } else {
                $export_cronjob->delete();
            }
        }
        if (rex_plugin::get('d2u_immo', 'import')->isAvailable()) {
            $import_cronjob = \D2U_Immo\ImportCronjob::factory();
            if ((bool) rex_config::get('d2u_immo', 'import_autoimport')) {
                if (!$import_cronjob->isInstalled()) {
                    $import_cronjob->install();
                }
            } else {
                $import_cronjob->delete();
            }
        }
    } else {
        echo rex_view::error(rex_i18n::msg('form_save_error'));
    }
}
?>
<form action="<?= rex_url::currentBackendPage() ?>" method="post">
	<div class="panel panel-edit">
		<header class="panel-heading"><div class="panel-title"><?= rex_i18n::msg('d2u_helper_settings') ?></div></header>
		<div class="panel-body">
			<fieldset>
				<legend><small><i class="rex-icon rex-icon-database"></i></small> <?= rex_i18n::msg('d2u_helper_settings') ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
                        \TobiasKrais\D2UHelper\BackendHelper::form_linkfield('d2u_immo_settings_article', '1', (int) rex_config::get('d2u_immo', 'article_id'), (int) rex_config::get('d2u_helper', 'default_lang'));
                    ?>
				</div>
			</fieldset>
			<fieldset>
				<legend><small><i class="rex-icon rex-icon-language"></i></small> <?= rex_i18n::msg('d2u_helper_lang_replacements') ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
                        \TobiasKrais\D2UHelper\BackendHelper::form_checkbox('d2u_helper_lang_wildcard_overwrite', 'settings[lang_wildcard_overwrite]', 'true', 'true' === rex_config::get('d2u_immo', 'lang_wildcard_overwrite'));
                        foreach (rex_clang::getAll() as $rex_clang) {
                            echo '<dl class="rex-form-group form-group">';
                            echo '<dt><label>'. $rex_clang->getName() .'</label></dt>';
                            echo '<dd>';
                            echo '<select class="form-control" name="settings[lang_replacement_'. $rex_clang->getId() .']">';
                            $replacement_options = [
                                'd2u_helper_lang_english' => 'english',
                                'd2u_helper_lang_german' => 'german',
                            ];
                            foreach ($replacement_options as $key => $value) {
                                $selected = $value === rex_config::get('d2u_immo', 'lang_replacement_'. $rex_clang->getId()) ? ' selected="selected"' : '';
                                echo '<option value="'. $value .'"'. $selected .'>'. rex_i18n::msg('d2u_helper_lang_replacements_install') .' '. rex_i18n::msg($key) .'</option>';
                            }
                            echo '</select>';
                            echo '</dl>';
                        }
                    ?>
				</div>
			</fieldset>
			<fieldset>
				<legend><small><i class="rex-icon rex-icon-open-category"></i></small> <?= rex_i18n::msg('d2u_helper_categories') ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
                        $options_category_sort = ['name' => rex_i18n::msg('d2u_helper_name'), 'priority' => rex_i18n::msg('header_priority')];
                        \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_helper_sort', 'settings[default_category_sort]', $options_category_sort, [(string) rex_config::get('d2u_immo', 'default_category_sort')]);
                    ?>
				</div>
			</fieldset>
			<fieldset>
				<legend><small><i class="rex-icon rex-icon-module"></i></small> <?= rex_i18n::msg('d2u_immo_properties') ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
                        $options = ['name' => rex_i18n::msg('d2u_helper_name'), 'priority' => rex_i18n::msg('header_priority')];
                        \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_helper_sort', 'settings[default_property_sort]', $options, [(string) rex_config::get('d2u_immo', 'default_property_sort')]);
                        \TobiasKrais\D2UHelper\BackendHelper::form_mediafield('d2u_immo_settings_even_informative_pdf', 'even_informative_pdf', '' !== rex_config::get('d2u_immo', 'even_informative_pdf') ? (string) rex_config::get('d2u_immo', 'even_informative_pdf') : '')
                    ?>
				</div>
			</fieldset>
			<fieldset>
				<legend><small><i class="rex-icon fa-money"></i></small> <?= rex_i18n::msg('d2u_immo_settings_finance_calculator') ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
                        echo '<p>'. rex_i18n::msg('d2u_immo_settings_finance_calculator_hint') .'</p>';
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_immo_settings_finance_calculator_real_estate_tax', 'settings[finance_calculator_real_estate_tax]', (string) rex_config::get('d2u_immo', 'finance_calculator_real_estate_tax'), true, false, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_immo_settings_finance_calculator_notary_costs', 'settings[finance_calculator_notary_costs]', (string) rex_config::get('d2u_immo', 'finance_calculator_notary_costs'), true, false, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_immo_settings_finance_calculator_interest_rate', 'settings[finance_calculator_interest_rate]', (string) rex_config::get('d2u_immo', 'finance_calculator_interest_rate'), true, false, 'text');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_immo_settings_finance_calculator_repayment', 'settings[finance_calculator_repayment]', (string) rex_config::get('d2u_immo', 'finance_calculator_repayment'), true, false, 'text');
                    ?>
				</div>
			</fieldset>
			<?php
                if (rex_plugin::get('d2u_immo', 'export')->isAvailable()) {
            ?>
				<fieldset>
					<legend><small><i class="rex-icon fa-cloud-upload"></i></small> <?= rex_i18n::msg('d2u_immo_export') ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
                        \TobiasKrais\D2UHelper\BackendHelper::form_checkbox('d2u_immo_export_settings_autoexport', 'settings[export_autoexport]', 'active', (bool) rex_config::get('d2u_immo', 'export_autoexport', false));
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_immo_export_settings_email', 'settings[export_failure_email]', (string) rex_config::get('d2u_immo', 'export_failure_email'), true, false, 'email');
                        ?>
					</div>
				</fieldset>
			<?php
                }
                if (rex_plugin::get('d2u_immo', 'import')->isAvailable()) {
                    // Default language for import
                    if (count(rex_clang::getAll()) > 1) {
                        $lang_options = [];
                        foreach (rex_clang::getAll() as $rex_clang) {
                            $lang_options[$rex_clang->getId()] = $rex_clang->getName();
                        }
                        \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_immo_import_settings_default_lang', 'settings[d2u_immo_default_lang]', $lang_options, [(int) rex_config::get('d2u_immo', 'import_default_lang')]);
                    }
            ?>
                <fieldset>
                    <legend><small><i class="rex-icon fa-cloud-download"></i></small> <?= rex_i18n::msg('d2u_immo_import') ?></legend>
                    <div class="panel-body-wrapper slide">
                        <?php
                        $options_categories = [];
                        foreach (D2U_Immo\Category::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $category) {
                            if ('' !== $category->name) {
                                $options_categories[$category->category_id] = ($category->parent_category instanceof D2U_Immo\Category ? $category->parent_category->name .' → ' : '') . $category->name;
                            }
                        }
                        asort($options_categories);
                        \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_immo_import_settings_category', 'settings[import_category_id]', $options_categories, [(int) rex_config::get('d2u_immo', 'import_category_id', 0)]);
                        ?>
                        <dl class="rex-form-group form-group" id="settings[import_media_category]">
							<dt><label><?= rex_i18n::msg('d2u_immo_import_settings_media_category') ?></label></dt>
							<dd>
								<?php
                                    $media_category = new rex_media_category_select(false);
                                    $media_category->addOption(rex_i18n::msg('pool_kats_no'), 0);
                                    $media_category->get();
                                    $media_category->setSelected((int) rex_config::get('d2u_immo', 'import_media_category', 0));
                                    $media_category->setName('settings[import_media_category]');
                                    $media_category->setAttribute('class', 'form-control');
                                    $media_category->show();
                                ?>
							</dd>
						</dl>
						<?php
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_immo_import_settings_email', 'settings[import_email]', (string) rex_config::get('d2u_immo', 'import_email'), true, false, 'email');
                        \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_immo_import_settings_import_folder', 'settings[import_folder]', (string) rex_config::get('d2u_immo', 'import_folder'), false, false);
                        \TobiasKrais\D2UHelper\BackendHelper::form_infotext('d2u_immo_import_settings_import_folder_hint', 'import_folder_hint');
                        \TobiasKrais\D2UHelper\BackendHelper::form_checkbox('d2u_immo_import_settings_autoimport', 'settings[import_autoimport]', 'active', (bool) rex_config::get('d2u_immo', 'import_autoimport', false));
                        ?>
                    </div>
                </fieldset>
            <?php
                }
                if (rex_plugin::get('d2u_immo', 'window_advertising')->isAvailable()) {
            ?>
				<fieldset>
					<legend><small><i class="rex-icon fa-desktop"></i></small> <?= rex_i18n::msg('d2u_immo_window_advertising') ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
                        \TobiasKrais\D2UHelper\BackendHelper::form_linkfield('d2u_immo_window_advertising_settings_article', '2', (int) rex_config::get('d2u_immo', 'window_advertising_settings_article'), (int) rex_config::get('d2u_helper', 'default_lang'))
                        ?>
					</div>
				</fieldset>
			<?php
                }
            ?>
		</div>
		<footer class="panel-footer">
			<div class="rex-form-panel-footer">
				<div class="btn-toolbar">
					<button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="save"><?= rex_i18n::msg('form_save') ?></button>
				</div>
			</div>
		</footer>
	</div>
</form>
<?php
    echo \TobiasKrais\D2UHelper\BackendHelper::getCSS();
    echo \TobiasKrais\D2UHelper\BackendHelper::getJS();
    echo \TobiasKrais\D2UHelper\BackendHelper::getJSOpenAll();
