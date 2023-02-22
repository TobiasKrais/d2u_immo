<?php
// save settings
if ('save' === filter_input(INPUT_POST, 'btn_save')) {
    $settings = rex_post('settings', 'array', []);

    // Linkmap Link and media needs special treatment
    $link_ids = filter_input_array(INPUT_POST, ['REX_INPUT_LINK' => ['filter' => FILTER_VALIDATE_INT, 'flags' => FILTER_REQUIRE_ARRAY]]);
    $settings['article_id'] = !is_array($link_ids) ? 0 : $link_ids['REX_INPUT_LINK'][1];
    if (rex_plugin::get('d2u_immo', 'window_advertising')->isAvailable()) {
        $settings['window_advertising_settings_article'] = !is_array($link_ids) ? 0 : $link_ids['REX_INPUT_LINK'][2];
    }

    $input_media = rex_post('REX_INPUT_MEDIA', 'array', []);
    $settings['even_informative_pdf'] = $input_media['even_informative_pdf'];

    // Checkbox also need special treatment if empty
    $settings['export_autoexport'] = array_key_exists('export_autoexport', $settings) ? 'active' : 'inactive';
    $settings['lang_wildcard_overwrite'] = array_key_exists('lang_wildcard_overwrite', $settings) ? 'true' : 'false';

    // Save settings
    if (rex_config::set('d2u_immo', $settings)) {
        echo rex_view::success(rex_i18n::msg('form_saved'));

        // Update url schemes
        if (\rex_addon::get('url')->isAvailable() && $settings['article_id'] > 0) {
            d2u_addon_backend_helper::update_url_scheme(rex::getTablePrefix() .'d2u_immo_url_categories', $settings['article_id']);
            d2u_addon_backend_helper::update_url_scheme(rex::getTablePrefix() .'d2u_immo_url_properties', $settings['article_id']);
        }

        // Install / update language replacements
        d2u_immo_lang_helper::factory()->install();
        if (rex_plugin::get('d2u_immo', 'export')->isAvailable()) {
            export_lang_helper::factory()->install();
        }

        // Install / remove Cronjob
        if (rex_plugin::get('d2u_immo', 'export')->isAvailable()) {
            $export_cronjob = d2u_immo_export_cronjob::factory();
            if ('active' == $this->getConfig('export_autoexport')) {
                if (!$export_cronjob->isInstalled()) {
                    $export_cronjob->install();
                }
            } else {
                $export_cronjob->delete();
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
                        d2u_addon_backend_helper::form_linkfield('d2u_immo_settings_article', '1', $this->getConfig('article_id'), (int) rex_config::get('d2u_helper', 'default_lang'));
                    ?>
				</div>
			</fieldset>
			<fieldset>
				<legend><small><i class="rex-icon rex-icon-language"></i></small> <?= rex_i18n::msg('d2u_helper_lang_replacements') ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
                        d2u_addon_backend_helper::form_checkbox('d2u_helper_lang_wildcard_overwrite', 'settings[lang_wildcard_overwrite]', 'true', 'true' == $this->getConfig('lang_wildcard_overwrite'));
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
                                $selected = $value == $this->getConfig('lang_replacement_'. $rex_clang->getId()) ? ' selected="selected"' : '';
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
                        d2u_addon_backend_helper::form_select('d2u_helper_sort', 'settings[default_category_sort]', $options_category_sort, [$this->getConfig('default_category_sort')]);
                    ?>
				</div>
			</fieldset>
			<fieldset>
				<legend><small><i class="rex-icon rex-icon-module"></i></small> <?= rex_i18n::msg('d2u_immo_properties') ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
                        $options = ['name' => rex_i18n::msg('d2u_helper_name'), 'priority' => rex_i18n::msg('header_priority')];
                        d2u_addon_backend_helper::form_select('d2u_helper_sort', 'settings[default_property_sort]', $options, [$this->getConfig('default_property_sort')]);
                        d2u_addon_backend_helper::form_mediafield('d2u_immo_settings_even_informative_pdf', 'even_informative_pdf', $this->hasConfig('even_informative_pdf') ? $this->getConfig('even_informative_pdf') : '')
                    ?>
				</div>
			</fieldset>
			<fieldset>
				<legend><small><i class="rex-icon fa-money"></i></small> <?= rex_i18n::msg('d2u_immo_settings_finance_calculator') ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
                        echo '<p>'. rex_i18n::msg('d2u_immo_settings_finance_calculator_hint') .'</p>';
                        d2u_addon_backend_helper::form_input('d2u_immo_settings_finance_calculator_real_estate_tax', 'settings[finance_calculator_real_estate_tax]', $this->getConfig('finance_calculator_real_estate_tax'), true, false, 'text');
                        d2u_addon_backend_helper::form_input('d2u_immo_settings_finance_calculator_notary_costs', 'settings[finance_calculator_notary_costs]', $this->getConfig('finance_calculator_notary_costs'), true, false, 'text');
                        d2u_addon_backend_helper::form_input('d2u_immo_settings_finance_calculator_interest_rate', 'settings[finance_calculator_interest_rate]', $this->getConfig('finance_calculator_interest_rate'), true, false, 'text');
                        d2u_addon_backend_helper::form_input('d2u_immo_settings_finance_calculator_repayment', 'settings[finance_calculator_repayment]', $this->getConfig('finance_calculator_repayment'), true, false, 'text');
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
                        d2u_addon_backend_helper::form_checkbox('d2u_immo_export_settings_autoexport', 'settings[export_autoexport]', 'active', 'active' == $this->getConfig('export_autoexport'));
                        d2u_addon_backend_helper::form_input('d2u_immo_export_settings_email', 'settings[export_failure_email]', $this->getConfig('export_failure_email'), true, false, 'email');
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
                        d2u_addon_backend_helper::form_linkfield('d2u_immo_window_advertising_settings_article', '2', $this->getConfig('window_advertising_settings_article'), (int) rex_config::get('d2u_helper', 'default_lang'))
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
    echo d2u_addon_backend_helper::getCSS();
    echo d2u_addon_backend_helper::getJS();
    echo d2u_addon_backend_helper::getJSOpenAll();
