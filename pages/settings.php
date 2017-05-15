<?php
// save settings
if (filter_input(INPUT_POST, "btn_save") == 'save') {
	$settings = (array) rex_post('settings', 'array', array());

	// Linkmap Link and media needs special treatment
	$link_ids = filter_input_array(INPUT_POST, array('REX_INPUT_LINK'=> array('filter' => FILTER_VALIDATE_INT, 'flags' => FILTER_REQUIRE_ARRAY)));

	$settings['article_id'] = $link_ids["REX_INPUT_LINK"][1];

	// Checkbox also need special treatment if empty
	$settings['export_autoexport'] = array_key_exists('export_autoexport', $settings) ? "active" : "inactive";
	
	// Save settings
	if(rex_config::set("d2u_immo", $settings)) {
		echo rex_view::success(rex_i18n::msg('form_saved'));

		// Update url schemes
		if(rex_addon::get('url')->isAvailable()) {
			d2u_addon_backend_helper::update_url_scheme(rex::getTablePrefix() ."d2u_immo_url_categories", $settings['article_id']);
			d2u_addon_backend_helper::update_url_scheme(rex::getTablePrefix() ."d2u_immo_url_properties", $settings['article_id']);
			UrlGenerator::generatePathFile([]);
		}
		
		// Install / update language replacements
		d2u_immo_lang_helper::factory()->install();
		if(rex_plugin::get('d2u_immo', 'export')->isAvailable()) {
			export_lang_helper::factory()->install();
		}

		// Install / remove Cronjob
 		if(rex_plugin::get('d2u_immo', 'export')->isAvailable()) {
			if($this->getConfig('export_autoexport') == 'active') {
				if(!export_backend_helper::autoexportIsInstalled()) {
					export_backend_helper::autoexportInstall();
				}
			}
			else {
				export_backend_helper::autoexportDelete();
			}
		}
	}
	else {
		echo rex_view::error(rex_i18n::msg('form_save_error'));
	}
}
?>
<form action="<?php print rex_url::currentBackendPage(); ?>" method="post">
	<div class="panel panel-edit">
		<header class="panel-heading"><div class="panel-title"><?php print rex_i18n::msg('d2u_immo_settings'); ?></div></header>
		<div class="panel-body">
			<fieldset>
				<legend><small><i class="rex-icon rex-icon-database"></i></small> <?php echo rex_i18n::msg('d2u_immo_settings'); ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
						d2u_addon_backend_helper::form_linkfield('d2u_immo_settings_article', '1', $this->getConfig('article_id'), $this->getConfig('default_lang'));

						// Default language for translations
						if(count(rex_clang::getAll()) > 1) {
							$lang_options = array();
							foreach(rex_clang::getAll() as $rex_clang) {
								$lang_options[$rex_clang->getId()] = $rex_clang->getName();
							}
							d2u_addon_backend_helper::form_select('d2u_immo_settings_defaultlang', 'settings[default_lang]', $lang_options, array($this->getConfig('default_lang')));
						}
					?>
				</div>
			</fieldset>
			<fieldset>
				<legend><small><i class="rex-icon rex-icon-language"></i></small> <?php echo rex_i18n::msg('d2u_immo_settings_lang_replacements'); ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
						foreach(rex_clang::getAll() as $rex_clang) {
							print '<dl class="rex-form-group form-group">';
							print '<dt><label>'. $rex_clang->getName() .'</label></dt>';
							print '<dd>';
							print '<select class="form-control" name="settings[lang_replacement_'. $rex_clang->getId() .']">';
							$replacement_options = array(
								'd2u_immo_settings_german' => 'german'
							);
							foreach($replacement_options as $key => $value) {
								$selected = $value == $this->getConfig('lang_replacement_'. $rex_clang->getId()) ? ' selected="selected"' : '';
								print '<option value="'. $value .'"'. $selected .'>'. rex_i18n::msg('d2u_immo_settings_lang_replacements_install') .' '. rex_i18n::msg($key) .'</option>';
							}
							print '</select>';
							print '</dl>';
						}
					?>
				</div>
			</fieldset>
			<fieldset>
				<legend><small><i class="rex-icon rex-icon-open-category"></i></small> <?php echo rex_i18n::msg('d2u_immo_categories'); ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
						$options_category_sort = ['name' => rex_i18n::msg('d2u_immo_name'), 'priority' => rex_i18n::msg('header_priority')];
						d2u_addon_backend_helper::form_select('d2u_immo_settings_default_sort', 'settings[default_category_sort]', $options_category_sort, [$this->getConfig('default_category_sort')]);
					?>
				</div>
			</fieldset>
			<fieldset>
				<legend><small><i class="rex-icon rex-icon-module"></i></small> <?php echo rex_i18n::msg('d2u_immo_properties'); ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
						$options = array('name' => rex_i18n::msg('d2u_immo_name'), 'priority' => rex_i18n::msg('header_priority'));
						d2u_addon_backend_helper::form_select('d2u_immo_settings_default_sort', 'settings[default_property_sort]', $options, array($this->getConfig('default_property_sort')));
					?>
				</div>
			</fieldset>
			<?php
				if(rex_plugin::get('d2u_immo', 'export')->isAvailable()) {
			?>
				<fieldset>
					<legend><small><i class="rex-icon fa-cloud-upload"></i></small> <?php echo rex_i18n::msg('d2u_immo_export'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
						d2u_addon_backend_helper::form_checkbox('d2u_immo_export_settings_autoexport', 'settings[export_autoexport]', 'active', $this->getConfig('export_autoexport') == 'active');
						d2u_addon_backend_helper::form_input('d2u_immo_export_settings_email', 'settings[export_failure_email]', $this->getConfig('export_failure_email'), TRUE, FALSE, 'email');
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
					<button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="save"><?php echo rex_i18n::msg('form_save'); ?></button>
				</div>
			</div>
		</footer>
	</div>
</form>
<?php
	print d2u_addon_backend_helper::getCSS();
	print d2u_addon_backend_helper::getJS();
	print d2u_addon_backend_helper::getJSOpenAll();