<?php

use D2U_Immo\Category;

$func = rex_request('func', 'string');
$entry_id = (int) rex_request('entry_id', 'int');
$message = rex_get('message', 'string');

// Print comments
if ('' !== $message) {
    echo rex_view::success(rex_i18n::msg($message));
}

// save settings
if (1 === (int) filter_input(INPUT_POST, 'btn_save') || 1 === (int) filter_input(INPUT_POST, 'btn_apply')) {
    $form = rex_post('form', 'array', []);

    // Media fields and links need special treatment
    $input_media_list = rex_post('REX_INPUT_MEDIALIST', 'array', []);

    $success = true;
    $property = false;
    $property_id = $form['property_id'];
    foreach (rex_clang::getAll() as $rex_clang) {
        if (false === $property) {
            $property = new D2U_Immo\Property($property_id, $rex_clang->getId());
            $property->property_id = $property_id; // Ensure correct ID in case first language has no object
            $property->additional_costs = (int) $form['additional_costs'];
            $property->animals = array_key_exists('animals', $form);
            $property->apartment_type = $form['apartment_type'];
            $property->available_from = $form['available_from'];
            $property->bath = is_array($form['bath']) ? $form['bath'] : [];
            $property->broadband_internet = is_array($form['broadband_internet']) ? $form['broadband_internet'] : [];
            $property->cable_sat_tv = array_key_exists('cable_sat_tv', $form);
            if (isset($form['category_id']) && (int) $form['category_id'] > 0) {
                $property->category = new D2U_Immo\Category((int) $form['category_id'], (int) rex_config::get('d2u_helper', 'default_lang'));
            }
            $property->city = $form['city'];
            $property->cold_rent = (int) $form['cold_rent'];
            $property->price_plus_vat = array_key_exists('price_plus_vat', $form);
            $property->condition_type = $form['condition_type'];
            $property->construction_year = (int) $form['construction_year'];
            if (isset($form['contact_id']) && (int) $form['contact_id'] > 0) {
                $property->contact = new D2U_Immo\Contact((int) $form['contact_id']);
            }
            $property->country_code = $form['country_code'];
            $property->courtage = $form['courtage'];
            $property->courtage_incl_vat = array_key_exists('courtage_incl_vat', $form);
            $property->currency_code = $form['currency_code'];
            $property->deposit = (int) $form['deposit'];
            $property->elevator = is_array($form['elevator']) ? $form['elevator'] : [];
            $property->energy_consumption = $form['energy_consumption'];
            $property->energy_pass = $form['energy_pass'];
            $property->energy_pass_valid_until = $form['energy_pass_valid_until'];
            $property->firing_type = is_array($form['firing_type']) ? $form['firing_type'] : [];
            $property->floor = (int) $form['floor'];
            $property->floor_type = is_array($form['floor_type']) ? $form['floor_type'] : [];
            $ground_plans = preg_grep('/^\s*$/s', explode(',', $input_media_list[2]), PREG_GREP_INVERT);
            $property->ground_plans = is_array($ground_plans) ? $ground_plans : [];
            $property->hall_warehouse_type = $form['hall_warehouse_type'];
            $property->heating_type = is_array($form['heating_type']) ? $form['heating_type'] : [];
            $property->house_number = $form['house_number'];
            $property->house_type = $form['house_type'];
            $property->including_warm_water = array_key_exists('including_warm_water', $form);
            $property->internal_object_number = $form['internal_object_number'];
            $property->kitchen = is_array($form['kitchen']) ? $form['kitchen'] : [];
            $property->land_area = (float) $form['land_area'];
            $property->land_type = $form['land_type'];
            $property->latitude = (float) $form['latitude'];
            $property->living_area = (float) $form['living_area'];
            $location_plans = preg_grep('/^\s*$/s', explode(',', $input_media_list[3]), PREG_GREP_INVERT);
            $property->location_plans = is_array($location_plans) ? $location_plans : [];
            $property->longitude = (float) $form['longitude'];
            $property->market_type = $form['market_type'];
            $property->object_reserved = array_key_exists('object_reserved', $form);
            $property->object_sold = array_key_exists('object_sold', $form);
            $property->object_type = $form['object_type'];
            $property->office_type = $form['office_type'];
            $property->online_status = $form['online_status'];
            $property->other_type = $form['other_type'];
            $property->parking_space_duplex = (int) $form['parking_space_duplex'];
            $property->parking_space_garage = (int) $form['parking_space_garage'];
            $property->parking_space_simple = (int) $form['parking_space_simple'];
            $property->parking_space_undergroundcarpark = (int) $form['parking_space_undergroundcarpark'];
            $property->parking_type = $form['parking_type'];
            $pictures = preg_grep('/^\s*$/s', explode(',', $input_media_list[1]), PREG_GREP_INVERT);
            $property->pictures = is_array($pictures) ? $pictures : [];
            $pictures_360 = preg_grep('/^\s*$/s', explode(',', $input_media_list[4]), PREG_GREP_INVERT);
            $property->pictures_360 = is_array($pictures_360) ? $pictures_360 : [];
            $property->priority = (int) $form['priority'];
            $property->publish_address = array_key_exists('publish_address', $form);
            $property->purchase_price = (int) $form['purchase_price'];
            $property->purchase_price_m2 = (int) $form['purchase_price_m2'];
            $property->rented = array_key_exists('rented', $form);
            $property->flat_sharing_possible = array_key_exists('flat_sharing_possible', $form);
            $property->rooms = (float) $form['rooms'];
            $property->street = $form['street'];
            $property->total_area = (float) $form['total_area'];
            $property->type_of_use = $form['type_of_use'];
            $property->wheelchair_accessable = array_key_exists('wheelchair_accessable', $form);
            $property->zip_code = $form['zip_code'];
            if (rex_plugin::get('d2u_immo', 'window_advertising')->isAvailable()) {
                $property->window_advertising_status = array_key_exists('window_advertising_status', $form) ? 'online' : 'offline';
            }
        } else {
            $property->clang_id = $rex_clang->getId();
        }
        $property->description = $form['lang'][$rex_clang->getId()]['description'];
        $property->description_equipment = $form['lang'][$rex_clang->getId()]['description_equipment'];
        $property->description_location = $form['lang'][$rex_clang->getId()]['description_location'];
        $property->description_others = $form['lang'][$rex_clang->getId()]['description_others'];
        $documents = preg_grep('/^\s*$/s', explode(',', $input_media_list['1'. $rex_clang->getId()]), PREG_GREP_INVERT);
        $property->documents = is_array($documents) ? $documents : [];
        $property->name = $form['lang'][$rex_clang->getId()]['name'];
        $property->teaser = $form['lang'][$rex_clang->getId()]['teaser'];
        $property->translation_needs_update = $form['lang'][$rex_clang->getId()]['translation_needs_update'];

        if ('delete' === $property->translation_needs_update) {
            $property->delete(false);
        } elseif ($property->save() > 0) {
            $success = false;
        } else {
            // remember id, for each database lang object needs same id
            $property_id = $property->property_id;
        }
    }

    // message output
    $message = 'form_save_error';
    if ($success) {
        $message = 'form_saved';
    }

    // Redirect to make reload and thus double save impossible
    if (1 === (int) filter_input(INPUT_POST, 'btn_apply', FILTER_VALIDATE_INT) && false !== $property) {
        header('Location: '. rex_url::currentBackendPage(['entry_id' => $property->property_id, 'func' => 'edit', 'message' => $message], false));
    } else {
        header('Location: '. rex_url::currentBackendPage(['message' => $message], false));
    }
    exit;
}
// Delete
if (1 === (int) filter_input(INPUT_POST, 'btn_delete', FILTER_VALIDATE_INT) || 'delete' === $func) {
    $property_id = $entry_id;
    if (0 === $property_id) {
        $form = rex_post('form', 'array', []);
        $property_id = $form['property_id'];
    }
    $property = new D2U_Immo\Property($property_id, (int) rex_config::get('d2u_helper', 'default_lang'));
    $property->property_id = $property_id; // Ensure correct ID in case language has no object
    $property->delete();

    $func = '';
}
// Change online status of machine
elseif ('changestatus' === $func) {
    $property = new D2U_Immo\Property($entry_id, (int) rex_config::get('d2u_helper', 'default_lang'));
    $property->property_id = $entry_id; // Ensure correct ID in case language has no object
    $property->changeStatus();

    header('Location: '. rex_url::currentBackendPage());
    exit;
}

// Eingabeformular
if ('edit' === $func || 'clone' === $func || 'add' === $func) {
?>
	<form action="<?= rex_url::currentBackendPage() ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?= rex_i18n::msg('d2u_immo_property') ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[property_id]" value="<?= 'edit' === $func ? $entry_id : 0 ?>">
				<?php
                    foreach (rex_clang::getAll() as $rex_clang) {
                        $property = new D2U_Immo\Property($entry_id, $rex_clang->getId());
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
                                    d2u_addon_backend_helper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, [$property->translation_needs_update], 1, false, $readonly_lang);
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
                                    d2u_addon_backend_helper::form_input('d2u_helper_name', 'form[lang]['. $rex_clang->getId() .'][name]', $property->name, $required, $readonly_lang, 'text');
                                    d2u_addon_backend_helper::form_input('d2u_immo_teaser', 'form[lang]['. $rex_clang->getId() .'][teaser]', $property->teaser, $required, $readonly_lang, 'text');
                                    d2u_addon_backend_helper::form_textarea('d2u_helper_description', 'form[lang]['. $rex_clang->getId() .'][description]', $property->description, 10, false, $readonly_lang, true);
                                    d2u_addon_backend_helper::form_textarea('d2u_immo_property_description_location', 'form[lang]['. $rex_clang->getId() .'][description_location]', $property->description_location, 5, false, $readonly_lang, true);
                                    d2u_addon_backend_helper::form_textarea('d2u_immo_property_description_equipment', 'form[lang]['. $rex_clang->getId() .'][description_equipment]', $property->description_equipment, 5, false, $readonly_lang, true);
                                    d2u_addon_backend_helper::form_textarea('d2u_immo_property_description_others', 'form[lang]['. $rex_clang->getId() .'][description_others]', $property->description_others, 5, false, $readonly_lang, true);
                                    d2u_addon_backend_helper::form_medialistfield('d2u_immo_property_documents', 10 + $rex_clang->getId(), $property->documents, $readonly_lang);
                                ?>
							</div>
						</div>
					</fieldset>
				<?php
                    }

                    // Do not use last object from translations, because you don't know if it exists in DB
                    $property = new D2U_Immo\Property($entry_id, (int) rex_config::get('d2u_helper', 'default_lang'));
                    $readonly = true;
                    if (rex::getUser() instanceof rex_user && (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_immo[edit_data]'))) {
                        $readonly = false;
                    }

                ?>
				<fieldset>
					<legend><?= rex_i18n::msg('d2u_helper_categories') ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
                            $options_categories = [];
                            foreach (D2U_Immo\Category::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $category) {
                                if ('' !== $category->name) {
                                    $options_categories[$category->category_id] = $category->name;
                                }
                            }
                            d2u_addon_backend_helper::form_select('d2u_helper_category', 'form[category_id]', $options_categories, $property->category instanceof Category ? [$property->category->category_id] : [], 1, false, $readonly);
                            $options_type_of_use = ['WOHNEN' => rex_i18n::msg('d2u_immo_property_type_of_use_WOHNEN'),
                                'GEWERBE' => rex_i18n::msg('d2u_immo_property_type_of_use_GEWERBE'),
                                'ANLAGE' => rex_i18n::msg('d2u_immo_property_type_of_use_ANLAGE'),
                                'WAZ' => rex_i18n::msg('d2u_immo_property_type_of_use_WAZ')];
                            d2u_addon_backend_helper::form_select('d2u_immo_property_type_of_use', 'form[type_of_use]', $options_type_of_use, [$property->type_of_use], 1, false, $readonly);
                            $options_market_type = ['KAUF' => rex_i18n::msg('d2u_immo_property_market_type_KAUF'),
                                //								'ERBPACHT' => rex_i18n::msg('d2u_immo_property_market_type_ERBPACHT'),
                                //								'LEASING' => rex_i18n::msg('d2u_immo_property_market_type_LEASING'),
                                'MIETE_PACHT' => rex_i18n::msg('d2u_immo_property_market_type_MIETE_PACHT')];
                            d2u_addon_backend_helper::form_select('d2u_immo_property_market_type', 'form[market_type]', $options_market_type, [$property->market_type], 1, false, $readonly);
                            $options_object_type = ['wohnung' => rex_i18n::msg('d2u_immo_property_object_type_wohnung'),
                                //								'zimmer' => rex_i18n::msg('d2u_immo_property_object_type_zimmer'),
                                'haus' => rex_i18n::msg('d2u_immo_property_object_type_haus'),
                                'buero_praxen' => rex_i18n::msg('d2u_immo_property_object_type_buero_praxen'),
                                'grundstueck' => rex_i18n::msg('d2u_immo_property_object_type_grundstueck'),
                                //								'einzelhandel' => rex_i18n::msg('d2u_immo_property_object_type_einzelhandel'),
                                //								'gastgewerbe' => rex_i18n::msg('d2u_immo_property_object_type_gastgewerbe'),
                                'hallen_lager_prod' => rex_i18n::msg('d2u_immo_property_object_type_hallen_lager_prod'),
                                'parken' => rex_i18n::msg('d2u_immo_property_object_type_parken'),
                                //								'land_und_forstwirtschaft' => rex_i18n::msg('d2u_immo_property_object_type_land_und_forstwirtschaft'),
                                //								'freizeitimmobilie_gewerblich' => rex_i18n::msg('d2u_immo_property_object_type_freizeitimmobilie_gewerblich'),
                                //								'zinshaus_renditeobjekt' => rex_i18n::msg('d2u_immo_property_object_type_zinshaus_renditeobjekt'),
                                'sonstige' => rex_i18n::msg('d2u_immo_property_object_type_sonstige')];
                            d2u_addon_backend_helper::form_select('d2u_immo_property_object_type', 'form[object_type]', $options_object_type, [$property->object_type], 1, false, $readonly);
                            $options_apartment_type = ['KEINE_ANGABE' => rex_i18n::msg('d2u_immo_property_type_KEINE_ANGABE'),
                                'DACHGESCHOSS' => rex_i18n::msg('d2u_immo_property_apartment_type_DACHGESCHOSS'),
                                'MAISONETTE' => rex_i18n::msg('d2u_immo_property_apartment_type_MAISONETTE'),
                                'LOFTSTUDIOATELIER' => rex_i18n::msg('d2u_immo_property_apartment_type_LOFTSTUDIOATELIER'),
                                'PENTHOUSE' => rex_i18n::msg('d2u_immo_property_apartment_type_PENTHOUSE'),
                                'TERRASSEN' => rex_i18n::msg('d2u_immo_property_apartment_type_TERRASSEN'),
                                'WETAGE' => rex_i18n::msg('d2u_immo_property_apartment_type_WETAGE'),
                                'ERDGESCHOSS' => rex_i18n::msg('d2u_immo_property_apartment_type_ERDGESCHOSS'),
                                'SOUTERRAIN' => rex_i18n::msg('d2u_immo_property_apartment_type_SOUTERRAIN')];
                            d2u_addon_backend_helper::form_select('d2u_immo_property_object_subtype', 'form[apartment_type]', $options_apartment_type, [$property->apartment_type], 1, false, $readonly);
                            $options_house_type = ['KEINE_ANGABE' => rex_i18n::msg('d2u_immo_property_type_KEINE_ANGABE'),
                                'APARTMENTHAUS' => rex_i18n::msg('d2u_immo_property_house_type_APARTMENTHAUS'),
                                'BAUERNHAUS' => rex_i18n::msg('d2u_immo_property_house_type_BAUERNHAUS'),
                                'BERGHUETTE' => rex_i18n::msg('d2u_immo_property_house_type_BERGHUETTE'),
                                'BUNGALOW' => rex_i18n::msg('d2u_immo_property_house_type_BUNGALOW'),
                                'BURG' => rex_i18n::msg('d2u_immo_property_house_type_BURG'),
                                'CHALET' => rex_i18n::msg('d2u_immo_property_house_type_CHALET'),
                                'DOPPELHAUSHAELFTE' => rex_i18n::msg('d2u_immo_property_house_type_DOPPELHAUSHAELFTE'),
                                'EINFAMILIENHAUS' => rex_i18n::msg('d2u_immo_property_house_type_EINFAMILIENHAUS'),
                                'FERIENHAUS' => rex_i18n::msg('d2u_immo_property_house_type_FERIENHAUS'),
                                'FERTIGHAUS' => rex_i18n::msg('d2u_immo_property_house_type_FERTIGHAUS'),
                                'FINCA' => rex_i18n::msg('d2u_immo_property_house_type_FINCA'),
                                'HERRENHAUS' => rex_i18n::msg('d2u_immo_property_house_type_HERRENHAUS'),
                                'LANDHAUS' => rex_i18n::msg('d2u_immo_property_house_type_LANDHAUS'),
                                'LAUBEDATSCHEGARTENHAUS' => rex_i18n::msg('d2u_immo_property_house_type_LAUBEDATSCHEGARTENHAUS'),
                                'MEHRFAMILIENHAUS' => rex_i18n::msg('d2u_immo_property_house_type_MEHRFAMILIENHAUS'),
                                'REIHENHAUS' => rex_i18n::msg('d2u_immo_property_house_type_REIHENHAUS'),
                                'REIHENEND' => rex_i18n::msg('d2u_immo_property_house_type_REIHENEND'),
                                'REIHENMITTEL' => rex_i18n::msg('d2u_immo_property_house_type_REIHENMITTEL'),
                                'REIHENECK' => rex_i18n::msg('d2u_immo_property_house_type_REIHENECK'),
                                'RESTHOF' => rex_i18n::msg('d2u_immo_property_house_type_RESTHOF'),
                                'RUSTICO' => rex_i18n::msg('d2u_immo_property_house_type_RUSTICO'),
                                'SCHLOSS' => rex_i18n::msg('d2u_immo_property_house_type_SCHLOSS'),
                                'STADTHAUS' => rex_i18n::msg('d2u_immo_property_house_type_STADTHAUS'),
                                'STRANDHAUS' => rex_i18n::msg('d2u_immo_property_house_type_STRANDHAUS'),
                                'VILLA' => rex_i18n::msg('d2u_immo_property_house_type_VILLA'),
                                'ZWEIFAMILIENHAUS' => rex_i18n::msg('d2u_immo_property_house_type_ZWEIFAMILIENHAUS')];
                            d2u_addon_backend_helper::form_select('d2u_immo_property_object_subtype', 'form[house_type]', $options_house_type, [$property->house_type], 1, false, $readonly);
                            $options_land_type = ['WOHNEN' => rex_i18n::msg('d2u_immo_property_land_type_WOHNEN'),
                                'GEWERBE' => rex_i18n::msg('d2u_immo_property_land_type_GEWERBE'),
                                'INDUSTRIE' => rex_i18n::msg('d2u_immo_property_land_type_INDUSTRIE'),
                                'LAND_FORSTWIRSCHAFT' => rex_i18n::msg('d2u_immo_property_land_type_LAND_FORSTWIRSCHAFT'),
                                'FREIZEIT' => rex_i18n::msg('d2u_immo_property_land_type_FREIZEIT'),
                                'GEMISCHT' => rex_i18n::msg('d2u_immo_property_land_type_GEMISCHT'),
                                'GEWERBEPARK' => rex_i18n::msg('d2u_immo_property_land_type_GEWERBEPARK'),
                                'SEELIEGENSCHAFT' => rex_i18n::msg('d2u_immo_property_land_type_SEELIEGENSCHAFT'),
                                'SONDERNUTZUNG' => rex_i18n::msg('d2u_immo_property_land_type_SONDERNUTZUNG')];
                            d2u_addon_backend_helper::form_select('d2u_immo_property_object_subtype', 'form[land_type]', $options_land_type, [$property->land_type], 1, false, $readonly);
                            $options_office_type = ['BUEROFLAECHE' => rex_i18n::msg('d2u_immo_property_office_type_BUEROFLAECHE'),
                                'BUEROHAUS' => rex_i18n::msg('d2u_immo_property_office_type_BUEROHAUS'),
                                'BUEROZENTRUM' => rex_i18n::msg('d2u_immo_property_office_type_BUEROZENTRUM'),
                                'COWORKING' => rex_i18n::msg('d2u_immo_property_office_type_COWORKING'),
                                'LOFT_ATELIER' => rex_i18n::msg('d2u_immo_property_office_type_LOFT_ATELIER'),
                                'PRAXIS' => rex_i18n::msg('d2u_immo_property_office_type_PRAXIS'),
                                'PRAXISFLAECHE' => rex_i18n::msg('d2u_immo_property_office_type_PRAXISFLAECHE'),
                                'PRAXISHAUS' => rex_i18n::msg('d2u_immo_property_office_type_PRAXISHAUS'),
                                'SHARED_OFFICE' => rex_i18n::msg('d2u_immo_property_office_type_SHARED_OFFICE'),
                                'AUSSTELLUNGSFLAECHE' => rex_i18n::msg('d2u_immo_property_office_type_AUSSTELLUNGSFLAECHE')];
                            d2u_addon_backend_helper::form_select('d2u_immo_property_object_subtype', 'form[office_type]', $options_office_type, [$property->office_type], 1, false, $readonly);
                            $options_hall_warehouse_type = ['HALLE' => rex_i18n::msg('d2u_immo_property_hall_warehouse_type_HALLE'),
                                'INDUSTRIEHALLE' => rex_i18n::msg('d2u_immo_property_hall_warehouse_type_INDUSTRIEHALLE'),
                                'LAGER' => rex_i18n::msg('d2u_immo_property_hall_warehouse_type_LAGER'),
                                'LAGERFLAECHEN' => rex_i18n::msg('d2u_immo_property_hall_warehouse_type_LAGERFLAECHEN'),
                                'LAGER_MIT_FREIFLAECHE' => rex_i18n::msg('d2u_immo_property_hall_warehouse_type_LAGER_MIT_FREIFLAECHE'),
                                'HOCHREGALLAGER' => rex_i18n::msg('d2u_immo_property_hall_warehouse_type_HOCHREGALLAGER'),
                                'SPEDITIONSLAGER' => rex_i18n::msg('d2u_immo_property_hall_warehouse_type_SPEDITIONSLAGER'),
                                'PRODUKTION' => rex_i18n::msg('d2u_immo_property_hall_warehouse_type_PRODUKTION'),
                                'WERKSTATT' => rex_i18n::msg('d2u_immo_property_hall_warehouse_type_WERKSTATT'),
                                'SERVICE' => rex_i18n::msg('d2u_immo_property_hall_warehouse_type_SERVICE'),
                                'FREIFLAECHEN' => rex_i18n::msg('d2u_immo_property_hall_warehouse_type_FREIFLAECHEN'),
                                'KUEHLHAUS' => rex_i18n::msg('d2u_immo_property_hall_warehouse_type_KUEHLHAUS')];
                            d2u_addon_backend_helper::form_select('d2u_immo_property_object_subtype', 'form[hall_warehouse_type]', $options_hall_warehouse_type, [$property->hall_warehouse_type], 1, false, $readonly);
                            $options_parking_type = ['BOOTSLIEGEPLATZ' => rex_i18n::msg('d2u_immo_property_parking_type_BOOTSLIEGEPLATZ'),
                                'CARPORT' => rex_i18n::msg('d2u_immo_property_parking_type_CARPORT'),
                                'DOPPELGARAGE' => rex_i18n::msg('d2u_immo_property_parking_type_DOPPELGARAGE'),
                                'DUPLEX' => rex_i18n::msg('d2u_immo_property_parking_type_DUPLEX'),
                                'EINZELGARAGE' => rex_i18n::msg('d2u_immo_property_parking_type_EINZELGARAGE'),
                                'PARKHAUS' => rex_i18n::msg('d2u_immo_property_parking_type_PARKHAUS'),
                                'PARKPLATZ_STROM' => rex_i18n::msg('d2u_immo_property_parking_type_PARKPLATZ_STROM'),
                                'STELLPLATZ' => rex_i18n::msg('d2u_immo_property_parking_type_STELLPLATZ'),
                                'TIEFGARAGENSTELLPLATZ' => rex_i18n::msg('d2u_immo_property_parking_type_TIEFGARAGENSTELLPLATZ'),
                                'TIEFGARAGE' => rex_i18n::msg('d2u_immo_property_parking_type_TIEFGARAGE')];
                            d2u_addon_backend_helper::form_select('d2u_immo_property_object_subtype', 'form[parking_type]', $options_parking_type, [$property->parking_type], 1, false, $readonly);
                            $options_other_type = ['PARKHAUS' => rex_i18n::msg('d2u_immo_property_other_type_PARKHAUS'),
                                'TANKSTELLE' => rex_i18n::msg('d2u_immo_property_other_type_TANKSTELLE'),
                                'KRANKENHAUS' => rex_i18n::msg('d2u_immo_property_other_type_KRANKENHAUS'),
                                'SONSTIGE' => rex_i18n::msg('d2u_immo_property_other_type_SONSTIGE')];
                            d2u_addon_backend_helper::form_select('d2u_immo_property_object_subtype', 'form[other_type]', $options_other_type, [$property->other_type], 1, false, $readonly);
                            $options_condition_type = ['ABRISSOBJEKT' => rex_i18n::msg('d2u_immo_property_condition_type_ABRISSOBJEKT'),
                                'BAUFAELLIG' => rex_i18n::msg('d2u_immo_property_condition_type_BAUFAELLIG'),
                                'ENTKERNT' => rex_i18n::msg('d2u_immo_property_condition_type_ENTKERNT'),
                                'ERSTBEZUG' => rex_i18n::msg('d2u_immo_property_condition_type_ERSTBEZUG'),
                                'GEPFLEGT' => rex_i18n::msg('d2u_immo_property_condition_type_GEPFLEGT'),
                                'MODERNISIERT' => rex_i18n::msg('d2u_immo_property_condition_type_MODERNISIERT'),
                                'NACH_VEREINBARUNG' => rex_i18n::msg('d2u_immo_property_condition_type_NACH_VEREINBARUNG'),
                                'NEUWERTIG' => rex_i18n::msg('d2u_immo_property_condition_type_NEUWERTIG'),
                                'PROJEKTIERT' => rex_i18n::msg('d2u_immo_property_condition_type_PROJEKTIERT'),
                                'ROHBAU' => rex_i18n::msg('d2u_immo_property_condition_type_ROHBAU'),
                                'SANIERUNGSBEDUERFTIG' => rex_i18n::msg('d2u_immo_property_condition_type_SANIERUNGSBEDUERFTIG'),
                                'TEIL_SANIERT' => rex_i18n::msg('d2u_immo_property_condition_type_TEIL_SANIERT'),
                                'TEIL_VOLLRENOVIERT' => rex_i18n::msg('d2u_immo_property_condition_type_TEIL_VOLLRENOVIERT'),
                                'TEIL_VOLLRENOVIERUNGSBED' => rex_i18n::msg('d2u_immo_property_condition_type_TEIL_VOLLRENOVIERUNGSBED'),
                                'VOLL_SANIERT' => rex_i18n::msg('d2u_immo_property_condition_type_VOLL_SANIERT')];
                            d2u_addon_backend_helper::form_select('d2u_immo_property_condition_type', 'form[condition_type]', $options_condition_type, [$property->condition_type], 1, false, $readonly);
                        ?>
					</div>
				</fieldset>
				<fieldset>
					<legend><?= rex_i18n::msg('d2u_immo_property_address') ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
                            d2u_addon_backend_helper::form_input('d2u_immo_contact_street', 'form[street]', $property->street, false, $readonly, 'text');
                            d2u_addon_backend_helper::form_input('d2u_immo_property_house_number', 'form[house_number]', $property->house_number, false, $readonly, 'text');
                            d2u_addon_backend_helper::form_input('d2u_immo_contact_zip_code', 'form[zip_code]', $property->zip_code, false, $readonly, 'text');
                            d2u_addon_backend_helper::form_input('d2u_immo_contact_city', 'form[city]', $property->city, false, $readonly, 'text');
                            $options_country_code = ['DEU' => rex_i18n::msg('d2u_immo_property_country_code_DEU'),
                                'FIN' => rex_i18n::msg('d2u_immo_property_country_code_FIN'),
                                'FRA' => rex_i18n::msg('d2u_immo_property_country_code_FRA'),
                                'ITA' => rex_i18n::msg('d2u_immo_property_country_code_ITA'),
                                'NOR' => rex_i18n::msg('d2u_immo_property_country_code_NOR'),
                                'AUT' => rex_i18n::msg('d2u_immo_property_country_code_AUT'),
                                'CHE' => rex_i18n::msg('d2u_immo_property_country_code_CHE'),
                                'SWE' => rex_i18n::msg('d2u_immo_property_country_code_SWE'),
                                'ESP' => rex_i18n::msg('d2u_immo_property_country_code_ESP')];
                            d2u_addon_backend_helper::form_select('d2u_immo_property_country_code', 'form[country_code]', $options_country_code, [$property->country_code], 1, false, $readonly);

                            $d2u_helper = rex_addon::get('d2u_helper');
                            $api_key = '';
                            if ($d2u_helper->hasConfig('maps_key')) {
                                $api_key = '?key='. $d2u_helper->getConfig('maps_key');

                        ?>
						<script src="https://maps.googleapis.com/maps/api/js<?= $api_key ?>"></script>
						<script>
							function geocode() {
								if($("input[name='form[street]']").val() === "" || $("input[name='form[house_number]']").val() === "" || $("input[name='form[city]']").val() === "") {
									alert("<?= rex_i18n::msg('d2u_helper_geocode_fields') ?>");
									return;
								}

								// Geocode
								var geocoder = new google.maps.Geocoder();
								geocoder.geocode({'address': $("input[name='form[street]']").val() + " " + $("input[name='form[house_number]']").val() + ", " + $("input[name='form[zip_code]']").val() + " " + $("input[name='form[city]']").val() },
									function(results, status) {
										if (status === google.maps.GeocoderStatus.OK) {
											$("input[name='form[latitude]']").val(results[0].geometry.location.lat);
											$("input[name='form[longitude]']").val(results[0].geometry.location.lng);
											// Show check geolocation button and set link to button
											$("#check_geocode").attr('href', "https://maps.google.com/?q=" + $("input[name='form[latitude]']").val() + "," + $("input[name='form[longitude]']").val() + "&z=17");
											$("#check_geocode").parent().show();
										}
										else {
											alert("<?= rex_i18n::msg('d2u_helper_geocode_failure') ?>");
										}
									}
								);
							}
						</script>
						<?php
                                echo '<dl class="rex-form-group form-group" id="geocode">';
                                echo '<dt><label></label></dt>';
                                echo '<dd><input type="submit" value="'. rex_i18n::msg('d2u_helper_geocode') .'" onclick="geocode(); return false;" class="btn btn-save">'
                                    . ' <div class="btn btn-abort"><a href="https://maps.google.com/?q='. $property->latitude .','. $property->longitude .'&z=17" id="check_geocode" target="_blank">'. rex_i18n::msg('d2u_helper_geocode_check') .'</a></div>'
                                    . '</dd>';
                                echo '</dl>';
                                if (0.0 === $property->latitude && 0.0 === $property->longitude) {
                                    echo '<script>jQuery(document).ready(function($) { $("#check_geocode").parent().hide(); });</script>';
                                }
                            }
                            d2u_addon_backend_helper::form_infotext('d2u_helper_geocode_hint', 'hint_geocoding');
                            d2u_addon_backend_helper::form_input('d2u_immo_property_longitude', 'form[longitude]', (string) $property->longitude, false, $readonly, 'text');
                            d2u_addon_backend_helper::form_input('d2u_immo_property_latitude', 'form[latitude]', (string) $property->latitude, false, $readonly, 'text');
                            d2u_addon_backend_helper::form_input('d2u_immo_property_floor', 'form[floor]', $property->floor, false, $readonly, 'number');
                            d2u_addon_backend_helper::form_checkbox('d2u_immo_property_publish_address', 'form[publish_address]', 'true', $property->publish_address, $readonly);
                        ?>
					</div>
				</fieldset>
				<fieldset>
					<legend><?= rex_i18n::msg('d2u_immo_property_equipment') ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
                            d2u_addon_backend_helper::form_checkbox('d2u_immo_property_flat_sharing_possible', 'form[flat_sharing_possible]', 'true', $property->flat_sharing_possible, $readonly);
                            $options_bath = ['BIDET' => rex_i18n::msg('d2u_immo_property_bath_BIDET'),
                                'DUSCHE' => rex_i18n::msg('d2u_immo_property_bath_DUSCHE'),
                                'FENSTER' => rex_i18n::msg('d2u_immo_property_bath_FENSTER'),
                                'PISSOIR' => rex_i18n::msg('d2u_immo_property_bath_PISSOIR'),
                                'WANNE' => rex_i18n::msg('d2u_immo_property_bath_WANNE')];
                            d2u_addon_backend_helper::form_select('d2u_immo_property_bath', 'form[bath][]', $options_bath, $property->bath, 5, true, $readonly);
                            $options_kitchen = ['EBK' => rex_i18n::msg('d2u_immo_property_kitchen_EBK'),
                                'OFFEN' => rex_i18n::msg('d2u_immo_property_kitchen_OFFEN'),
                                'PANTRY' => rex_i18n::msg('d2u_immo_property_kitchen_PANTRY')];
                            d2u_addon_backend_helper::form_select('d2u_immo_property_kitchen', 'form[kitchen][]', $options_kitchen, $property->kitchen, 3, true, $readonly);
                            $options_floor_type = ['DIELEN' => rex_i18n::msg('d2u_immo_property_floor_type_DIELEN'),
                                'DOPPELBODEN' => rex_i18n::msg('d2u_immo_property_floor_type_DOPPELBODEN'),
                                'ESTRICH' => rex_i18n::msg('d2u_immo_property_floor_type_ESTRICH'),
                                'FERTIGPARKETT' => rex_i18n::msg('d2u_immo_property_floor_type_FERTIGPARKETT'),
                                'FLIESEN' => rex_i18n::msg('d2u_immo_property_floor_type_FLIESEN'),
                                'GRANIT' => rex_i18n::msg('d2u_immo_property_floor_type_GRANIT'),
                                'KUNSTSTOFF' => rex_i18n::msg('d2u_immo_property_floor_type_KUNSTSTOFF'),
                                'LAMINAT' => rex_i18n::msg('d2u_immo_property_floor_type_LAMINAT'),
                                'LINOLEUM' => rex_i18n::msg('d2u_immo_property_floor_type_LINOLEUM'),
                                'MARMOR' => rex_i18n::msg('d2u_immo_property_floor_type_MARMOR'),
                                'PARKETT' => rex_i18n::msg('d2u_immo_property_floor_type_PARKETT'),
                                'STEIN' => rex_i18n::msg('d2u_immo_property_floor_type_STEIN'),
                                'TEPPICH' => rex_i18n::msg('d2u_immo_property_floor_type_TEPPICH'),
                                'TERRAKOTTA' => rex_i18n::msg('d2u_immo_property_floor_type_TERRAKOTTA')];
                            d2u_addon_backend_helper::form_select('d2u_immo_property_floor_type', 'form[floor_type][]', $options_floor_type, $property->floor_type, 5, true, $readonly);
                            $options_heating_type = ['ETAGE' => rex_i18n::msg('d2u_immo_property_heating_type_ETAGE'),
                                'FERN' => rex_i18n::msg('d2u_immo_property_heating_type_FERN'),
                                'FUSSBODEN' => rex_i18n::msg('d2u_immo_property_heating_type_FUSSBODEN'),
                                'OFEN' => rex_i18n::msg('d2u_immo_property_heating_type_OFEN'),
                                'ZENTRAL' => rex_i18n::msg('d2u_immo_property_heating_type_ZENTRAL')];
                            d2u_addon_backend_helper::form_select('d2u_immo_property_heating_type', 'form[heating_type][]', $options_heating_type, $property->heating_type, 5, true, $readonly);
                            $options_elevator = ['LASTEN' => rex_i18n::msg('d2u_immo_property_elevator_LASTEN'),
                                'PERSONEN' => rex_i18n::msg('d2u_immo_property_elevator_PERSONEN')];
                            d2u_addon_backend_helper::form_select('d2u_immo_property_elevator', 'form[elevator][]', $options_elevator, $property->elevator, 2, true, $readonly);
                            d2u_addon_backend_helper::form_checkbox('d2u_immo_property_wheelchair_accessable', 'form[wheelchair_accessable]', 'true', $property->wheelchair_accessable, $readonly);
                            d2u_addon_backend_helper::form_checkbox('d2u_immo_property_cable_sat_tv', 'form[cable_sat_tv]', 'true', $property->cable_sat_tv, $readonly);
                            $options_broadband_internet = ['ADSL' => rex_i18n::msg('d2u_immo_property_broadband_internet_ADSL'),
                                'DSL' => rex_i18n::msg('d2u_immo_property_broadband_internet_DSL'),
                                'IPTV' => rex_i18n::msg('d2u_immo_property_broadband_internet_IPTV'),
                                'SDSL' => rex_i18n::msg('d2u_immo_property_broadband_internet_SDSL'),
                                'SKYDSL' => rex_i18n::msg('d2u_immo_property_broadband_internet_SKYDSL'),
                                'VDSL' => rex_i18n::msg('d2u_immo_property_broadband_internet_VDSL')];
                            d2u_addon_backend_helper::form_select('d2u_immo_property_broadband_internet', 'form[broadband_internet][]', $options_broadband_internet, $property->broadband_internet, 5, true, $readonly);
                        ?>
					</div>
				</fieldset>
				<fieldset>
					<legend><?= rex_i18n::msg('d2u_immo_property_energy_pass') ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
                            $options_energy_pass = ['BEDARF' => rex_i18n::msg('d2u_immo_property_energy_pass_BEDARF'),
                                'VERBRAUCH' => rex_i18n::msg('d2u_immo_property_energy_pass_VERBRAUCH')];
                            d2u_addon_backend_helper::form_select('d2u_immo_property_energy_pass_kind', 'form[energy_pass]', $options_energy_pass, [$property->energy_pass], 1, false, $readonly);
                            d2u_addon_backend_helper::form_input('d2u_immo_property_energy_pass_valid_until', 'form[energy_pass_valid_until]', $property->energy_pass_valid_until, false, $readonly, 'date');
                            d2u_addon_backend_helper::form_input('d2u_immo_property_energy_consumption', 'form[energy_consumption]', $property->energy_consumption, false, $readonly, 'text');
                            d2u_addon_backend_helper::form_checkbox('d2u_immo_property_energy_including_warm_water', 'form[including_warm_water]', 'true', $property->including_warm_water, $readonly);
                            $options_firing_type = ['ALTERNATIV' => rex_i18n::msg('d2u_immo_property_firing_type_ALTERNATIV'),
                                'BLOCK' => rex_i18n::msg('d2u_immo_property_firing_type_BLOCK'),
                                'ELEKTRO' => rex_i18n::msg('d2u_immo_property_firing_type_ELEKTRO'),
                                'WASSER-ELEKTRO' => rex_i18n::msg('d2u_immo_property_firing_type_WASSER-ELEKTRO'),
                                'GAS' => rex_i18n::msg('d2u_immo_property_firing_type_GAS'),
                                'HOLZ' => rex_i18n::msg('d2u_immo_property_firing_type_HOLZ'),
                                'FERN' => rex_i18n::msg('d2u_immo_property_firing_type_FERN'),
                                'FLUESSIGGAS' => rex_i18n::msg('d2u_immo_property_firing_type_FLUESSIGGAS'),
                                'OEL' => rex_i18n::msg('d2u_immo_property_firing_type_OEL'),
                                'PELLET' => rex_i18n::msg('d2u_immo_property_firing_type_PELLET'),
                                'SOLAR' => rex_i18n::msg('d2u_immo_property_firing_type_SOLAR'),
                                'LUFTWP' => rex_i18n::msg('d2u_immo_property_firing_type_LUFTWP'),
                                'ERDWAERME' => rex_i18n::msg('d2u_immo_property_firing_type_ERDWAERME')];
                            d2u_addon_backend_helper::form_select('d2u_immo_property_firing_type', 'form[firing_type][]', $options_firing_type, $property->firing_type, 5, true, $readonly);
                        ?>
					</div>
				</fieldset>
				<fieldset>
					<legend><?= rex_i18n::msg('d2u_immo_property_prices') ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
                            $options_currency_code = ['EUR' => rex_i18n::msg('d2u_immo_property_currency_code_EUR'),
                                'CHF' => rex_i18n::msg('d2u_immo_property_currency_code_CHF'),
                                'USD' => rex_i18n::msg('d2u_immo_property_currency_code_USD')];
                            d2u_addon_backend_helper::form_select('d2u_immo_property_currency_code', 'form[currency_code]', $options_currency_code, [$property->currency_code], 1, false, $readonly);
                            d2u_addon_backend_helper::form_input('d2u_immo_property_purchase_price', 'form[purchase_price]', $property->purchase_price, false, $readonly, 'number');
                            d2u_addon_backend_helper::form_input('d2u_immo_property_purchase_price_m2', 'form[purchase_price_m2]', $property->purchase_price_m2, false, $readonly, 'number');
                            d2u_addon_backend_helper::form_input('d2u_immo_property_cold_rent', 'form[cold_rent]', $property->cold_rent, false, $readonly, 'number');
                            d2u_addon_backend_helper::form_input('d2u_immo_property_additional_costs', 'form[additional_costs]', $property->additional_costs, false, $readonly, 'number');
                            d2u_addon_backend_helper::form_checkbox('d2u_immo_property_price_plus_vat', 'form[price_plus_vat]', 'true', $property->price_plus_vat, $readonly);
                            d2u_addon_backend_helper::form_input('d2u_immo_property_deposit', 'form[deposit]', $property->deposit, false, $readonly, 'number');
                            d2u_addon_backend_helper::form_input('d2u_immo_property_courtage', 'form[courtage]', $property->courtage, false, $readonly, 'text');
                            d2u_addon_backend_helper::form_checkbox('d2u_immo_property_courtage_incl_vat', 'form[courtage_incl_vat]', 'true', $property->courtage_incl_vat, $readonly);
                        ?>
					</div>
				</fieldset>
				<fieldset>
					<legend><?= rex_i18n::msg('d2u_immo_property_data') ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
                            d2u_addon_backend_helper::form_input('d2u_immo_property_construction_year', 'form[construction_year]', $property->construction_year, false, $readonly, 'number');
                            d2u_addon_backend_helper::form_input('d2u_immo_property_living_area', 'form[living_area]', (string) $property->living_area, false, $readonly, 'text');
                            d2u_addon_backend_helper::form_input('d2u_immo_property_total_area', 'form[total_area]', (string) $property->total_area, false, $readonly, 'text');
                            d2u_addon_backend_helper::form_input('d2u_immo_property_land_area', 'form[land_area]', (string) $property->land_area, false, $readonly, 'text');
                            d2u_addon_backend_helper::form_input('d2u_immo_property_rooms', 'form[rooms]', (string) $property->rooms, false, $readonly, 'text');
                            d2u_addon_backend_helper::form_input('d2u_immo_property_parking_space_duplex', 'form[parking_space_duplex]', $property->parking_space_duplex, false, $readonly, 'number');
                            d2u_addon_backend_helper::form_input('d2u_immo_property_parking_space_simple', 'form[parking_space_simple]', $property->parking_space_simple, false, $readonly, 'number');
                            d2u_addon_backend_helper::form_input('d2u_immo_property_parking_space_garage', 'form[parking_space_garage]', $property->parking_space_garage, false, $readonly, 'number');
                            d2u_addon_backend_helper::form_input('d2u_immo_property_parking_space_undergroundcarpark', 'form[parking_space_undergroundcarpark]', $property->parking_space_undergroundcarpark, false, $readonly, 'number');
                        ?>
					</div>
				</fieldset>
				<fieldset>
					<legend><?= rex_i18n::msg('d2u_immo_property_other_data') ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
                            $options_status = ['online' => rex_i18n::msg('clang_online'),
                                'offline' => rex_i18n::msg('clang_offline'),
                                'archived' => rex_i18n::msg('d2u_immo_status_archived')];
                            d2u_addon_backend_helper::form_select('d2u_immo_status', 'form[online_status]', $options_status, [$property->online_status], 1, false, $readonly);
                            if (rex_plugin::get('d2u_immo', 'window_advertising')->isAvailable()) {
                                d2u_addon_backend_helper::form_checkbox('d2u_immo_window_advertising_show', 'form[window_advertising_status]', 'true', $property->window_advertising_status === 'online', $readonly);
                            }
                            d2u_addon_backend_helper::form_input('d2u_immo_property_internal_object_number', 'form[internal_object_number]', $property->internal_object_number, true, $readonly, 'text');
                            d2u_addon_backend_helper::form_input('header_priority', 'form[priority]', $property->priority, true, $readonly, 'number');
                            d2u_addon_backend_helper::form_input('d2u_immo_property_available_from', 'form[available_from]', $property->available_from, false, $readonly, 'date');
                            d2u_addon_backend_helper::form_checkbox('d2u_immo_property_rented', 'form[rented]', 'true', $property->rented, $readonly);
                            d2u_addon_backend_helper::form_checkbox('d2u_immo_property_animals', 'form[animals]', 'true', $property->animals, $readonly);
                            d2u_addon_backend_helper::form_checkbox('d2u_immo_property_object_reserved', 'form[object_reserved]', 'true', $property->object_reserved, $readonly);
                            d2u_addon_backend_helper::form_checkbox('d2u_immo_property_object_sold', 'form[object_sold]', 'true', $property->object_sold, $readonly);
                            $options_contacts = [];
                            foreach (D2U_Immo\Contact::getAll() as $contact) {
                                if ('' !== $contact->lastname) {
                                    $options_contacts[$contact->contact_id] = $contact->lastname .', '. $contact->firstname;
                                }
                            }
                            d2u_addon_backend_helper::form_select('d2u_immo_contact', 'form[contact_id]', $options_contacts, false === $property->contact ? [] : [$property->contact->contact_id], 1, false, $readonly);
                            d2u_addon_backend_helper::form_medialistfield('d2u_helper_pictures', 1, $property->pictures, $readonly);
                            d2u_addon_backend_helper::form_medialistfield('d2u_helper_pictures_360', 4, $property->pictures_360, $readonly);
                            d2u_addon_backend_helper::form_medialistfield('d2u_immo_property_ground_plans', 2, $property->ground_plans, $readonly);
                            d2u_addon_backend_helper::form_medialistfield('d2u_immo_property_location_plans', 3, $property->location_plans, $readonly);
                            d2u_addon_backend_helper::form_input('d2u_immo_property_openimmo_object_id', 'form[openimmo_object_id]', $property->openimmo_object_id, true, true, 'text');
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
    ?>
	<script>
		function energy_pass_changer() {
			// Engery pass is not necessary for object tpye "grundstueck" and "parken", also for condition type "projektiert"
			if ($("select[name='form[object_type]']").val() === "grundstueck"
					|| $("select[name='form[object_type]']").val() === "parken"
					|| $("select[name='form[condition_type]").val() === "PROJEKTIERT") {
				$("select[name='form[energy_pass]']").removeAttr('required');
				$("input[name='form[energy_consumption]']").removeAttr('required');
				$("input[name='form[energy_pass_valid_until]']").removeAttr('required');
				$("dl[id='form[energy_pass]']").parent().parent().hide();
			}
			else {
				$("select[name='form[energy_pass]']").prop('required', true);
				$("input[name='form[energy_consumption]']").prop('required', true);
				$("input[name='form[energy_pass_valid_until]']").prop('required', true);
				$("dl[id='form[energy_pass]']").parent().parent().fadeIn();
			}
		}

		function object_type_changer(value) {
			if (value === "wohnung") {
				$("dl[id='form[apartment_type]']").fadeIn();
				$("dl[id='form[hall_warehouse_type]']").hide();
				$("dl[id='form[house_type]']").hide();
				$("dl[id='form[land_type]']").hide();
				$("dl[id='form[office_type]']").hide();
				$("dl[id='form[other_type]']").hide();
				$("dl[id='form[parking_type]']").hide();
			}
			else if (value === "haus") {
				$("dl[id='form[apartment_type]']").hide();
				$("dl[id='form[hall_warehouse_type]']").hide();
				$("dl[id='form[house_type]']").fadeIn();
				$("dl[id='form[land_type]']").hide();
				$("dl[id='form[office_type]']").hide();
				$("dl[id='form[other_type]']").hide();
				$("dl[id='form[parking_type]']").hide();
			}
			else if (value === "grundstueck") {
				$("dl[id='form[apartment_type]']").hide();
				$("dl[id='form[hall_warehouse_type]']").hide();
				$("dl[id='form[house_type]']").hide();
				$("dl[id='form[land_type]']").fadeIn();
				$("dl[id='form[office_type]']").hide();
				$("dl[id='form[other_type]']").hide();
				$("dl[id='form[parking_type]']").hide();
			}
			else if (value === "buero_praxen") {
				$("dl[id='form[apartment_type]']").hide();
				$("dl[id='form[hall_warehouse_type]']").hide();
				$("dl[id='form[house_type]']").hide();
				$("dl[id='form[land_type]']").hide();
				$("dl[id='form[office_type]']").fadeIn();
				$("dl[id='form[other_type]']").hide();
				$("dl[id='form[parking_type]']").hide();
			}
			else if (value === "hallen_lager_prod") {
				$("dl[id='form[apartment_type]']").hide();
				$("dl[id='form[hall_warehouse_type]']").fadeIn();
				$("dl[id='form[house_type]']").hide();
				$("dl[id='form[land_type]']").hide();
				$("dl[id='form[office_type]']").hide();
				$("dl[id='form[other_type]']").hide();
				$("dl[id='form[parking_type]']").hide();
			}
			else if (value === "parken") {
				$("dl[id='form[apartment_type]']").hide();
				$("dl[id='form[hall_warehouse_type]']").hide();
				$("dl[id='form[house_type]']").hide();
				$("dl[id='form[land_type]']").hide();
				$("dl[id='form[office_type]']").hide();
				$("dl[id='form[other_type]']").hide();
				$("dl[id='form[parking_type]']").fadeIn();
			}
			else if (value === "sonstige") {
				$("dl[id='form[apartment_type]']").hide();
				$("dl[id='form[hall_warehouse_type]']").hide();
				$("dl[id='form[house_type]']").hide();
				$("dl[id='form[land_type]']").hide();
				$("dl[id='form[office_type]']").hide();
				$("dl[id='form[other_type]']").fadeIn();
				$("dl[id='form[parking_type]']").hide();
			};

			// If other stuff is is not necessary for "grundstueck" and parking
			if (value === "grundstueck" || value === "parken") {
				$("dl[id='form[flat_sharing_possible]']").parent().parent().hide();
				$("dl[id='form[floor]']").slideUp();
				$("dl[id='form[living_area]']").slideUp();
				$("dl[id='form[total_area]']").slideUp();
				$("dl[id='form[rooms]']").slideUp();
				$("dl[id='form[parking_space_duplex]']").slideUp();
				$("dl[id='form[parking_space_simple]']").slideUp();
				$("dl[id='form[parking_space_garage]']").slideUp();
				$("dl[id='form[parking_space_undergroundcarpark]']").slideUp();
				$("dl[id='form[animals]']").slideUp();
				if (value === "grundstueck") {
					$("dl[id='form[construction_year]']").slideUp();
				}
				if (value === "parken") {
					$("dl[id='form[land_area]']").slideUp();
				}
			}
			else {
				$("dl[id='form[flat_sharing_possible]']").parent().parent().fadeIn();
				$("dl[id='form[floor]']").slideDown();
				$("dl[id='form[living_area]']").slideDown();
				$("dl[id='form[total_area]']").slideDown();
				$("dl[id='form[rooms]']").slideDown();
				$("dl[id='form[parking_space_duplex]']").slideDown();
				$("dl[id='form[parking_space_simple]']").slideDown();
				$("dl[id='form[parking_space_garage]']").slideDown();
				$("dl[id='form[parking_space_undergroundcarpark]']").slideDown();
				$("dl[id='form[animals]']").slideDown();
				if (value === "grundstueck") {
					$("dl[id='form[construction_year]']").slideDown();
				}
				if (value === "parken") {
					$("dl[id='form[land_area]']").slideDown();
				}
			}
		}

		function market_type_changer(value) {
			if (value === "KAUF") {
				$("dl[id='form[purchase_price]']").fadeIn();
				$("input[name='form[purchase_price]']").prop('required', true);
				$("dl[id='form[purchase_price_m2]']").fadeIn();
				$("input[name='form[purchase_price_m2]").prop('required', true);
				$("dl[id='form[cold_rent]']").hide();
				$("input[name='form[cold_rent]']").removeAttr('required');
				$("dl[id='form[additional_costs]']").hide();
				$("input[name='form[additional_costs]']").removeAttr('required');
				$("dl[id='form[deposit]']").hide();
				$("input[name='form[deposit]']").removeAttr('required');
			}
			else {
				$("dl[id='form[purchase_price]']").hide();
				$("input[name='form[purchase_price]']").removeAttr('required');
				$("dl[id='form[purchase_price_m2]']").hide();
				$("input[name='form[purchase_price_m2]").removeAttr('required');
				$("dl[id='form[cold_rent]']").fadeIn();
				$("input[name='form[cold_rent]']").prop('required', true);
				$("dl[id='form[additional_costs]']").fadeIn();
				$("input[name='form[additional_costs]']").prop('required', true);
				$("dl[id='form[deposit]']").fadeIn();
				$("input[name='form[deposit]']").prop('required', true);
			}
		}

		// Hide on document load
		$(document).ready(function() {
			market_type_changer($("select[name='form[market_type]']").val());
			object_type_changer($("select[name='form[object_type]']").val());
			// Check if energy pass is necessary
			energy_pass_changer();
		});

		// Hide on selection change
		$("select[name='form[market_type]']").on('change', function(e) {
			market_type_changer($(this).val());
		});
		$("select[name='form[object_type]']").on('change', function(e) {
			object_type_changer($(this).val());
			energy_pass_changer();
		});
		$("select[name='form[condition_type]").on('change', function(e) {
			energy_pass_changer();
		});
	</script>
	<?php
}
