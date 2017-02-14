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
	$form = (array) rex_post('form', 'array', array());

	// Media fields and links need special treatment
	$input_media_list = (array) rex_post('REX_INPUT_MEDIALIST', 'array', array());

	$success = TRUE;
	$property = FALSE;
	$property_id = $form['property_id'];
	foreach(rex_clang::getAll() as $rex_clang) {
		if($property === FALSE) {
			$property = new Property($property_id, $rex_clang->getId());
			$property->additional_costs = isset($form['additional_costs']) ? $form['additional_costs'] : 0;
			$property->animals = array_key_exists('animals', $form);
			$property->apartment_type = $form['apartment_type'];
			$property->available_from = $form['available_from'];
			if(isset($form['bath'])) {
				$property->bath = $form['bath'];
			}
			if(isset($form['broadband_internet'])) {
				$property->broadband_internet = $form['broadband_internet'];
			}
			$property->cable_sat_tv = array_key_exists('cable_sat_tv', $form);
			if(isset($form['category_id']) && $form['category_id'] > 0) {
				$property->category = new Category($form['category_id'], rex_config::get("d2u_immo", "default_lang"));
			}
			$property->city = $form['city'];
			$property->cold_rent = isset($form['cold_rent']) ? $form['cold_rent'] : 0;
			$property->condition_type = $form['condition_type'];
			$property->construction_year = $form['construction_year'];
			if(isset($form['contact_id']) && $form['contact_id'] > 0) {
				$property->contact = new Contact($form['contact_id']);
			}
			$property->country_code = $form['country_code'];
			$property->courtage = $form['courtage'];
			$property->courtage_incl_vat = array_key_exists('courtage_incl_vat', $form);
			$property->currency_code = $form['currency_code'];
			$property->deposit = isset($form['deposit']) ? $form['deposit'] : 0;
			if(isset($form['elevator'])) {
				$property->elevator = $form['elevator'];
			}
			$property->energy_consumption = $form['energy_consumption'];
			$property->energy_pass = $form['energy_pass'];
			$property->energy_pass_valid_until = $form['energy_pass_valid_until'];
			if(isset($form['firing_type'])) {
				$property->firing_type = $form['firing_type'];
			}
			$property->floor = $form['floor'];
			if(isset($form['floor_type'])) {
				$property->floor_type = $form['floor_type'];
			}
			$property->ground_plans = preg_grep('/^\s*$/s', explode(",", $input_media_list[2]), PREG_GREP_INVERT);
			if(isset($form['heating_type'])) {
				$property->heating_type = $form['heating_type'];
			}
			$property->house_number = $form['house_number'];
			$property->house_type = isset($form['house_type']) ? $form['house_type'] : '';
			$property->including_warm_water = array_key_exists('including_warm_water', $form);
			$property->internal_object_number = $form['internal_object_number'];
			if(isset($form['kitchen'])) {
				$property->kitchen = $form['kitchen'];
			}
			$property->land_area = $form['land_area'];
			$property->land_type = isset($form['land_type']) ? $form['land_type'] : '';
			$property->latitude = $form['latitude'];
			$property->living_area = $form['living_area'];
			$property->location_plans = preg_grep('/^\s*$/s', explode(",", $input_media_list[3]), PREG_GREP_INVERT);
			$property->longitude = $form['longitude'];
			$property->market_type = $form['market_type'];
			$property->object_archived = array_key_exists('object_archived', $form);
			$property->object_reserved = array_key_exists('object_reserved', $form);
			$property->object_sold = array_key_exists('object_sold', $form);
			$property->object_type = $form['object_type'];
			$property->office_type = isset($form['office_type']) ? $form['office_type'] : '';
			$property->online_status = array_key_exists('online_status', $form) ? 'online' : 'offline';
			$property->other_type = isset($form['other_type']) ? $form['other_type'] : '';
			$property->parking_space_duplex = $form['parking_space_duplex'];
			$property->parking_space_garage = $form['parking_space_garage'];
			$property->parking_space_simple = $form['parking_space_simple'];
			$property->parking_space_undergroundcarpark = $form['parking_space_undergroundcarpark'];
			$property->pictures = preg_grep('/^\s*$/s', explode(",", $input_media_list[1]), PREG_GREP_INVERT);
			$property->priority = $form['priority'];
			$property->publish_address = array_key_exists('publish_address', $form);
			$property->purchase_price = isset($form['purchase_price']) ? $form['purchase_price'] : 0;
			$property->purchase_price_m2 = isset($form['purchase_price_m2']) ? $form['purchase_price_m2'] : 0;
			$property->rented = array_key_exists('rented', $form);
			$property->residential_community_possible = array_key_exists('residential_community_possible', $form);
			$property->rooms = $form['rooms'];
			$property->street = $form['street'];
			$property->total_area = $form['total_area'];
			$property->type_of_use = $form['type_of_use'];
			$property->wheelchair_accessable = array_key_exists('wheelchair_accessable', $form);
			$property->zip_code = $form['zip_code'];
		}
		else {
			$property->clang_id = $rex_clang->getId();
		}
		$property->description = $form['lang'][$rex_clang->getId()]['description'];
		$property->description_equipment = $form['lang'][$rex_clang->getId()]['description_equipment'];
		$property->description_location = $form['lang'][$rex_clang->getId()]['description_location'];
		$property->description_others = $form['lang'][$rex_clang->getId()]['description_others'];
		$property->documents = preg_grep('/^\s*$/s', explode(",", $input_media_list['1'. $rex_clang->getId()]), PREG_GREP_INVERT);
		$property->name = $form['lang'][$rex_clang->getId()]['name'];
		$property->teaser = $form['lang'][$rex_clang->getId()]['teaser'];
		$property->translation_needs_update = $form['lang'][$rex_clang->getId()]['translation_needs_update'];

		if($property->translation_needs_update == "delete") {
			$property->delete(FALSE);
		}
		else if($property->save() > 0){
			$success = FALSE;
		}
		else {
			// remember id, for each database lang object needs same id
			$property_id = $property->property_id;
		}
	}

	// message output
	$message = 'form_save_error';
	if($success) {
		$message = 'form_saved';
	}

	// Redirect to make reload and thus double save impossible
	if(filter_input(INPUT_POST, "btn_apply") == 1 && $property !== FALSE) {
		header("Location: ". rex_url::currentBackendPage(array("entry_id"=>$property->property_id, "func"=>'edit', "message"=>$message), FALSE));
	}
	else {
		header("Location: ". rex_url::currentBackendPage(array("message"=>$message), FALSE));
	}
	exit;
}
// Delete
else if(filter_input(INPUT_POST, "btn_delete") == 1 || $func == 'delete') {
	$property_id = $entry_id;
	if($property_id == 0) {
		$form = (array) rex_post('form', 'array', array());
		$property_id = $form['property_id'];
	}
	$property = new Property($property_id, rex_config::get("d2u_immo", "default_lang"));
	$property->delete();

	$func = '';
}

// Eingabeformular
if ($func == 'edit' || $func == 'add') {
?>
	<form action="<?php print rex_url::currentBackendPage(); ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?php print rex_i18n::msg('d2u_immo_property'); ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[property_id]" value="<?php echo $entry_id; ?>">
				<?php
					foreach(rex_clang::getAll() as $rex_clang) {
						$property = new Property($entry_id, $rex_clang->getId());
						$required = $rex_clang->getId() == rex_config::get("d2u_immo", "default_lang") ? TRUE : FALSE;

						$readonly_lang = TRUE;
						if(rex::getUser()->isAdmin() || (rex::getUser()->hasPerm('d2u_immo[edit_lang]') && rex::getUser()->getComplexPerm('clang')->hasPerm($rex_clang->getId()))) {
							$readonly_lang = FALSE;
						}
				?>
					<fieldset>
						<legend><?php echo rex_i18n::msg('d2u_immo_text_lang') .' "'. $rex_clang->getName() .'"'; ?></legend>
						<div class="panel-body-wrapper slide">
							<?php
								if($rex_clang->getId() != rex_config::get("d2u_immo", "default_lang")) {
									$options_translations = array();
									$options_translations["yes"] = rex_i18n::msg('d2u_immo_translation_needs_update');
									$options_translations["no"] = rex_i18n::msg('d2u_immo_translation_is_uptodate');
									$options_translations["delete"] = rex_i18n::msg('d2u_immo_translation_delete');
									d2u_addon_backend_helper::form_select('d2u_immo_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, array($property->translation_needs_update), 1, FALSE, $readonly_lang);
								}
								else {
									print '<input type="hidden" name="form[lang]['. $rex_clang->getId() .'][translation_needs_update]" value="">';
								}

								d2u_addon_backend_helper::form_input('d2u_immo_name', "form[lang][". $rex_clang->getId() ."][name]", $property->name, $required, $readonly_lang, "text");
								d2u_addon_backend_helper::form_input('d2u_immo_property_teaser', "form[lang][". $rex_clang->getId() ."][teaser]", $property->teaser, $required, $readonly_lang, "text");
								d2u_addon_backend_helper::form_textarea('d2u_immo_property_description', "form[lang][". $rex_clang->getId() ."][description]", $property->description, 10, $required, $readonly_lang, TRUE);
								d2u_addon_backend_helper::form_textarea('d2u_immo_property_description_location', "form[lang][". $rex_clang->getId() ."][description_location]", $property->description_location, 5, FALSE, $readonly_lang, TRUE);
								d2u_addon_backend_helper::form_textarea('d2u_immo_property_description_equipment', "form[lang][". $rex_clang->getId() ."][description_equipment]", $property->description_equipment, 5, FALSE, $readonly_lang, TRUE);
								d2u_addon_backend_helper::form_textarea('d2u_immo_property_description_others', "form[lang][". $rex_clang->getId() ."][description_others]", $property->description_others, 5, FALSE, $readonly_lang, TRUE);
								d2u_addon_backend_helper::form_medialistfield('d2u_immo_property_documents', '1'. $rex_clang->getId(), $property->documents, $readonly_lang);
							?>
						</div>
					</fieldset>
				<?php
					}
					
					// Do not use last object from translations, because you don't know if it exists in DB
					$property = new Property($entry_id, rex_config::get("d2u_immo", "default_lang"));
					$readonly = TRUE;
					if(rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_immo[edit_data]')) {
						$readonly = FALSE;
					}

				?>
				<fieldset>
					<legend><?php echo rex_i18n::msg('d2u_immo_categories'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							$options_categories = [];
							foreach(Category::getAll(rex_config::get("d2u_immo", "default_lang")) as $category) {
								if($category->name != "") {
									$options_categories[$category->category_id] = $category->name;
								}
							}
							d2u_addon_backend_helper::form_select('d2u_immo_category', 'form[category_id]', $options_categories, ($property->category === FALSE ? [] : [$property->category->category_id]), 1, FALSE, $readonly);
							$options_type_of_use = ['WOHNEN' => rex_i18n::msg('d2u_immo_property_type_of_use_WOHNEN'),
								'GEWERBE' => rex_i18n::msg('d2u_immo_property_type_of_use_GEWERBE'),
								'ANLAGE' => rex_i18n::msg('d2u_immo_property_type_of_use_ANLAGE'),
								'WAZ' => rex_i18n::msg('d2u_immo_property_type_of_use_WAZ')];
							d2u_addon_backend_helper::form_select('d2u_immo_property_type_of_use', 'form[type_of_use]', $options_type_of_use, array($property->type_of_use), 1, FALSE, $readonly);
							$options_market_type = ['KAUF' => rex_i18n::msg('d2u_immo_property_market_type_KAUF'),
//								'ERBPACHT' => rex_i18n::msg('d2u_immo_property_market_type_ERBPACHT'),
//								'LEASING' => rex_i18n::msg('d2u_immo_property_market_type_LEASING'),
								'MIETE_PACHT' => rex_i18n::msg('d2u_immo_property_market_type_MIETE_PACHT')];
							d2u_addon_backend_helper::form_select('d2u_immo_property_market_type', 'form[market_type]', $options_market_type, array($property->market_type), 1, FALSE, $readonly);
							$options_object_type = ['wohnung' => rex_i18n::msg('d2u_immo_property_object_type_wohnung'),
//								'zimmer' => rex_i18n::msg('d2u_immo_property_object_type_zimmer'),
								'haus' => rex_i18n::msg('d2u_immo_property_object_type_haus'),
								'buero_praxen' => rex_i18n::msg('d2u_immo_property_object_type_buero_praxen'),
								'grundstueck' => rex_i18n::msg('d2u_immo_property_object_type_grundstueck'),
//								'einzelhandel' => rex_i18n::msg('d2u_immo_property_object_type_einzelhandel'),
//								'gastgewerbe' => rex_i18n::msg('d2u_immo_property_object_type_gastgewerbe'),
//								'hallen_lager_prod' => rex_i18n::msg('d2u_immo_property_object_type_hallen_lager_prod'),
//								'land_und_forstwirtschaft' => rex_i18n::msg('d2u_immo_property_object_type_land_und_forstwirtschaft'),
//								'freizeitimmobilie_gewerblich' => rex_i18n::msg('d2u_immo_property_object_type_freizeitimmobilie_gewerblich'),
//								'zinshaus_renditeobjekt' => rex_i18n::msg('d2u_immo_property_object_type_zinshaus_renditeobjekt'),
								'sonstige' => rex_i18n::msg('d2u_immo_property_object_type_sonstige')];
							d2u_addon_backend_helper::form_select('d2u_immo_property_object_type', 'form[object_type]', $options_object_type, array($property->object_type), 1, FALSE, $readonly);
							$options_apartment_type = ['KEINE_ANGABE' => rex_i18n::msg('d2u_immo_property_type_KEINE_ANGABE'),
								'DACHGESCHOSS' => rex_i18n::msg('d2u_immo_property_apartment_type_DACHGESCHOSS'),
								'MAISONETTE' => rex_i18n::msg('d2u_immo_property_apartment_type_MAISONETTE'),
								'LOFTSTUDIOATELIER' => rex_i18n::msg('d2u_immo_property_apartment_type_LOFTSTUDIOATELIER'),
								'PENTHOUSE' => rex_i18n::msg('d2u_immo_property_apartment_type_PENTHOUSE'),
								'TERRASSEN' => rex_i18n::msg('d2u_immo_property_apartment_type_TERRASSEN'),
								'WETAGE' => rex_i18n::msg('d2u_immo_property_apartment_type_WETAGE'),
								'ERDGESCHOSS' => rex_i18n::msg('d2u_immo_property_apartment_type_ERDGESCHOSS'),
								'SOUTERRAIN' => rex_i18n::msg('d2u_immo_property_apartment_type_SOUTERRAIN')];
							d2u_addon_backend_helper::form_select('d2u_immo_property_apartment_type', 'form[apartment_type]', $options_apartment_type, array($property->apartment_type), 1, FALSE, $readonly);
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
								'DOPPELHAUSHAELFTE' => rex_i18n::msg('d2u_immo_property_house_type_DOPPELHAUSHAELFTE'),
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
							d2u_addon_backend_helper::form_select('d2u_immo_property_house_type', 'form[house_type]', $options_house_type, array($property->house_type), 1, FALSE, $readonly);
							$options_land_type = ['WOHNEN' => rex_i18n::msg('d2u_immo_property_land_type_WOHNEN'),
								'GEWERBE' => rex_i18n::msg('d2u_immo_property_land_type_GEWERBE'),
								'INDUSTRIE' => rex_i18n::msg('d2u_immo_property_land_type_INDUSTRIE'),
								'LAND_FORSTWIRSCHAFT' => rex_i18n::msg('d2u_immo_property_land_type_LAND_FORSTWIRSCHAFT'),
								'FREIZEIT' => rex_i18n::msg('d2u_immo_property_land_type_FREIZEIT'),
								'GEMISCHT' => rex_i18n::msg('d2u_immo_property_land_type_GEMISCHT'),
								'GEWERBEPARK' => rex_i18n::msg('d2u_immo_property_land_type_GEWERBEPARK'),
								'SEELIEGENSCHAFT' => rex_i18n::msg('d2u_immo_property_land_type_SEELIEGENSCHAFT'),
								'SONDERNUTZUNG' => rex_i18n::msg('d2u_immo_property_land_type_SONDERNUTZUNG')];
							d2u_addon_backend_helper::form_select('d2u_immo_property_land_type', 'form[land_type]', $options_land_type, array($property->land_type), 1, FALSE, $readonly);
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
							d2u_addon_backend_helper::form_select('d2u_immo_property_office_type', 'form[office_type]', $options_office_type, array($property->office_type), 1, FALSE, $readonly);
							$options_other_type = ['PARKHAUS' => rex_i18n::msg('d2u_immo_property_other_type_PARKHAUS'),
								'TANKSTELLE' => rex_i18n::msg('d2u_immo_property_other_type_TANKSTELLE'),
								'KRANKENHAUS' => rex_i18n::msg('d2u_immo_property_other_type_KRANKENHAUS'),
								'SONSTIGE' => rex_i18n::msg('d2u_immo_property_other_type_SONSTIGE')];
							d2u_addon_backend_helper::form_select('d2u_immo_property_other_type', 'form[other_type]', $options_other_type, array($property->other_type), 1, FALSE, $readonly);
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
							d2u_addon_backend_helper::form_select('d2u_immo_property_condition_type', 'form[condition_type]', $options_condition_type, [$property->condition_type], 1, FALSE, $readonly);
						?>
					</div>
				</fieldset>
				<fieldset>
					<legend><?php echo rex_i18n::msg('d2u_immo_property_address'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							d2u_addon_backend_helper::form_input('d2u_immo_contact_street', 'form[street]', $property->street, FALSE, $readonly, 'text');
							d2u_addon_backend_helper::form_input('d2u_immo_property_house_number', 'form[house_number]', $property->house_number, FALSE, $readonly, 'text');
							d2u_addon_backend_helper::form_input('d2u_immo_contact_zip_code', 'form[zip_code]', $property->zip_code, FALSE, $readonly, 'text');
							d2u_addon_backend_helper::form_input('d2u_immo_contact_city', 'form[city]', $property->city, FALSE, $readonly, 'text');
							$options_country_code = ['DEU' => rex_i18n::msg('d2u_immo_property_country_code_DEU'),
								'FIN' => rex_i18n::msg('d2u_immo_property_country_code_FIN'),
								'FRA' => rex_i18n::msg('d2u_immo_property_country_code_FRA'),
								'ITA' => rex_i18n::msg('d2u_immo_property_country_code_ITA'),
								'NOR' => rex_i18n::msg('d2u_immo_property_country_code_NOR'),
								'AUT' => rex_i18n::msg('d2u_immo_property_country_code_AUT'),
								'CHE' => rex_i18n::msg('d2u_immo_property_country_code_CHE'),
								'SWE' => rex_i18n::msg('d2u_immo_property_country_code_SWE'),
								'ESP' => rex_i18n::msg('d2u_immo_property_country_code_ESP')];
							d2u_addon_backend_helper::form_select('d2u_immo_property_country_code', 'form[country_code]', $options_country_code, array($property->country_code), 1, FALSE, $readonly);
							d2u_addon_backend_helper::form_input('d2u_immo_property_longitude', 'form[longitude]', $property->longitude, FALSE, $readonly, 'text');
							d2u_addon_backend_helper::form_input('d2u_immo_property_latitude', 'form[latitude]', $property->latitude, FALSE, $readonly, 'text');
							d2u_addon_backend_helper::form_input('d2u_immo_property_floor', 'form[floor]', $property->floor, FALSE, $readonly, 'number');
							d2u_addon_backend_helper::form_checkbox('d2u_immo_property_publish_address', 'form[publish_address]', 'true', $property->publish_address);
						?>
					</div>
				</fieldset>
				<fieldset>
					<legend><?php echo rex_i18n::msg('d2u_immo_property_equipment'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							d2u_addon_backend_helper::form_checkbox('d2u_immo_property_residential_community_possible', 'form[residential_community_possible]', 'true', $property->residential_community_possible);
							$options_bath = ['BIDET' => rex_i18n::msg('d2u_immo_property_bath_BIDET'),
								'DUSCHE' => rex_i18n::msg('d2u_immo_property_bath_DUSCHE'),
								'FENSTER' => rex_i18n::msg('d2u_immo_property_bath_FENSTER'),
								'PISSOIR' => rex_i18n::msg('d2u_immo_property_bath_PISSOIR'),
								'WANNE' => rex_i18n::msg('d2u_immo_property_bath_WANNE')];
							d2u_addon_backend_helper::form_select('d2u_immo_property_bath', 'form[bath][]', $options_bath, $property->bath, 5, TRUE, $readonly);
							$options_kitchen = ['EBK' => rex_i18n::msg('d2u_immo_property_kitchen_EBK'),
								'OFFEN' => rex_i18n::msg('d2u_immo_property_kitchen_OFFEN'),
								'PANTRY' => rex_i18n::msg('d2u_immo_property_kitchen_PANTRY')];
							d2u_addon_backend_helper::form_select('d2u_immo_property_kitchen', 'form[kitchen][]', $options_kitchen, $property->kitchen, 3, TRUE, $readonly);
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
							d2u_addon_backend_helper::form_select('d2u_immo_property_floor_type', 'form[floor_type][]', $options_floor_type, $property->floor_type, 5, TRUE, $readonly);
							$options_heating_type = ['ETAGE' => rex_i18n::msg('d2u_immo_property_heating_type_ETAGE'),
								'FERN' => rex_i18n::msg('d2u_immo_property_heating_type_FERN'),
								'FUSSBODEN' => rex_i18n::msg('d2u_immo_property_heating_type_FUSSBODEN'),
								'OFEN' => rex_i18n::msg('d2u_immo_property_heating_type_OFEN'),
								'ZENTRAL' => rex_i18n::msg('d2u_immo_property_heating_type_ZENTRAL')];
							d2u_addon_backend_helper::form_select('d2u_immo_property_heating_type', 'form[heating_type][]', $options_heating_type, $property->heating_type, 5, TRUE, $readonly);
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
							d2u_addon_backend_helper::form_select('d2u_immo_property_firing_type', 'form[firing_type][]', $options_firing_type, $property->firing_type, 5, TRUE, $readonly);
							$options_elevator = ['LASTEN' => rex_i18n::msg('d2u_immo_property_elevator_LASTEN'),
								'PERSONEN' => rex_i18n::msg('d2u_immo_property_elevator_PERSONEN')];
							d2u_addon_backend_helper::form_select('d2u_immo_property_elevator', 'form[elevator][]', $options_elevator, $property->elevator, 2, TRUE, $readonly);
							d2u_addon_backend_helper::form_checkbox('d2u_immo_property_wheelchair_accessable', 'form[wheelchair_accessable]', 'true', $property->wheelchair_accessable);
							d2u_addon_backend_helper::form_checkbox('d2u_immo_property_cable_sat_tv', 'form[cable_sat_tv]', 'true', $property->cable_sat_tv);
							$options_broadband_internet = ['ADSL' => rex_i18n::msg('d2u_immo_property_broadband_internet_ADSL'),
								'DSL' => rex_i18n::msg('d2u_immo_property_broadband_internet_DSL'),
								'IPTV' => rex_i18n::msg('d2u_immo_property_broadband_internet_IPTV'),
								'SDSL' => rex_i18n::msg('d2u_immo_property_broadband_internet_SDSL'),
								'SKYDSL' => rex_i18n::msg('d2u_immo_property_broadband_internet_SKYDSL'),
								'VDSL' => rex_i18n::msg('d2u_immo_property_broadband_internet_VDSL')];
							d2u_addon_backend_helper::form_select('d2u_immo_property_broadband_internet', 'form[broadband_internet][]', $options_broadband_internet, $property->broadband_internet, 5, TRUE, $readonly);
						?>
					</div>
				</fieldset>
				<fieldset>
					<legend><?php echo rex_i18n::msg('d2u_immo_property_energy_pass'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							$options_energy_pass = ['BEDARF' => rex_i18n::msg('d2u_immo_property_energy_pass_BEDARF'),
								'VERBRAUCH' => rex_i18n::msg('d2u_immo_property_energy_pass_VERBRAUCH')];
							d2u_addon_backend_helper::form_select('d2u_immo_property_energy_pass_kind', 'form[energy_pass]', $options_energy_pass, [$property->energy_pass], 1, FALSE, $readonly);
							d2u_addon_backend_helper::form_input('d2u_immo_property_energy_pass_valid_until', 'form[energy_pass_valid_until]', $property->energy_pass_valid_until, FALSE, $readonly, 'date');
							d2u_addon_backend_helper::form_input('d2u_immo_property_energy_consumption', 'form[energy_consumption]', $property->energy_consumption, FALSE, $readonly, 'text');
							d2u_addon_backend_helper::form_checkbox('d2u_immo_property_energy_including_warm_water', 'form[including_warm_water]', 'true', $property->including_warm_water);
						?>
					</div>
				</fieldset>
				<fieldset>
					<legend><?php echo rex_i18n::msg('d2u_immo_property_prices'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							$options_currency_code = ['EUR' => rex_i18n::msg('d2u_immo_property_currency_code_EUR'),
								'CHF' => rex_i18n::msg('d2u_immo_property_currency_code_CHF'),
								'USD' => rex_i18n::msg('d2u_immo_property_currency_code_USD')];
							d2u_addon_backend_helper::form_select('d2u_immo_property_currency_code', 'form[currency_code]', $options_currency_code, array($property->currency_code), 1, FALSE, $readonly);
							d2u_addon_backend_helper::form_input('d2u_immo_property_purchase_price', 'form[purchase_price]', $property->purchase_price, FALSE, $readonly, 'number');
							d2u_addon_backend_helper::form_input('d2u_immo_property_purchase_price_m2', 'form[purchase_price_m2]', $property->purchase_price_m2, FALSE, $readonly, 'number');
							d2u_addon_backend_helper::form_input('d2u_immo_property_cold_rent', 'form[cold_rent]', $property->cold_rent, FALSE, $readonly, 'number');
							d2u_addon_backend_helper::form_input('d2u_immo_property_additional_costs', 'form[additional_costs]', $property->additional_costs, FALSE, $readonly, 'number');
							d2u_addon_backend_helper::form_input('d2u_immo_property_deposit', 'form[deposit]', $property->deposit, FALSE, $readonly, 'text');
							d2u_addon_backend_helper::form_input('d2u_immo_property_courtage', 'form[courtage]', $property->courtage, FALSE, $readonly, 'text');
							d2u_addon_backend_helper::form_checkbox('d2u_immo_property_courtage_incl_vat', 'form[courtage_incl_vat]', 'true', $property->courtage_incl_vat);
						?>
					</div>
				</fieldset>
				<fieldset>
					<legend><?php echo rex_i18n::msg('d2u_immo_property_data'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							d2u_addon_backend_helper::form_input('d2u_immo_property_construction_year', 'form[construction_year]', $property->construction_year, FALSE, $readonly, 'number');
							d2u_addon_backend_helper::form_input('d2u_immo_property_living_area', 'form[living_area]', $property->living_area, FALSE, $readonly, 'text');
							d2u_addon_backend_helper::form_input('d2u_immo_property_total_area', 'form[total_area]', $property->total_area, FALSE, $readonly, 'text');
							d2u_addon_backend_helper::form_input('d2u_immo_property_land_area', 'form[land_area]', $property->land_area, FALSE, $readonly, 'text');
							d2u_addon_backend_helper::form_input('d2u_immo_property_rooms', 'form[rooms]', $property->rooms, FALSE, $readonly, 'text');
							d2u_addon_backend_helper::form_input('d2u_immo_property_parking_space_duplex', 'form[parking_space_duplex]', $property->parking_space_duplex, FALSE, $readonly, 'number');
							d2u_addon_backend_helper::form_input('d2u_immo_property_parking_space_simple', 'form[parking_space_simple]', $property->parking_space_simple, FALSE, $readonly, 'number');
							d2u_addon_backend_helper::form_input('d2u_immo_property_parking_space_garage', 'form[parking_space_garage]', $property->parking_space_garage, FALSE, $readonly, 'number');
							d2u_addon_backend_helper::form_input('d2u_immo_property_parking_space_undergroundcarpark', 'form[parking_space_undergroundcarpark]', $property->parking_space_undergroundcarpark, FALSE, $readonly, 'number');
						?>
					</div>
				</fieldset>
				<fieldset>
					<legend><?php echo rex_i18n::msg('d2u_immo_property_other_data'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							d2u_addon_backend_helper::form_input('d2u_immo_property_openimmo_object_id', 'form[openimmo_object_id]', $property->openimmo_object_id, TRUE, TRUE, 'text');
							d2u_addon_backend_helper::form_input('d2u_immo_property_internal_object_number', 'form[internal_object_number]', $property->internal_object_number, TRUE, $readonly, 'text');
							d2u_addon_backend_helper::form_input('header_priority', 'form[priority]', $property->priority, TRUE, FALSE, 'number');
							d2u_addon_backend_helper::form_input('d2u_immo_property_available_from', 'form[available_from]', $property->available_from, FALSE, $readonly, 'date');
							d2u_addon_backend_helper::form_checkbox('d2u_immo_property_rented', 'form[rented]', 'true', $property->rented);
							d2u_addon_backend_helper::form_checkbox('d2u_immo_property_animals', 'form[animals]', 'true', $property->animals);
							d2u_addon_backend_helper::form_checkbox('d2u_immo_property_object_reserved', 'form[object_reserved]', 'true', $property->object_reserved);
							d2u_addon_backend_helper::form_checkbox('d2u_immo_property_object_sold', 'form[object_sold]', 'true', $property->object_sold);
							d2u_addon_backend_helper::form_checkbox('d2u_immo_property_object_archived', 'form[object_archived]', 'true', $property->object_archived);
							$options_contacts = [];
							foreach(Contact::getAll() as $contact) {
								if($contact->lastname != "") {
									$options_contacts[$contact->contact_id] = $contact->lastname .", ". $contact->firstname;
								}
							}
							d2u_addon_backend_helper::form_select('d2u_immo_contact', 'form[contact_id]', $options_contacts, ($property->contact === FALSE ? [] : [$property->contact->contact_id]), 1, FALSE, $readonly);
							d2u_addon_backend_helper::form_medialistfield('d2u_immo_property_pictures', '1', $property->pictures, $readonly);
							d2u_addon_backend_helper::form_medialistfield('d2u_immo_property_ground_plans', '2', $property->ground_plans, $readonly);
							d2u_addon_backend_helper::form_medialistfield('d2u_immo_property_location_plans', '3', $property->location_plans, $readonly);
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
						<button class="btn btn-delete" type="submit" name="btn_delete" formnovalidate="formnovalidate" data-confirm="<?php echo rex_i18n::msg('form_delete'); ?>?" value="1"><?php echo rex_i18n::msg('form_delete'); ?></button>
					</div>
				</div>
			</footer>
		</div>
	</form>
	<br>
	<?php
		print d2u_addon_backend_helper::getCSS('d2u_immo');
		print d2u_addon_backend_helper::getJS();
	?>
	<script type="text/javascript">
		function object_type_changer(value) {
			if (value === "wohnung") {
				$("select[name='form[apartment_type]']").prop('disabled', false);
				$("select[name='form[house_type]']").prop('disabled', true);
				$("select[name='form[land_type]']").prop('disabled', true);
				$("select[name='form[office_type]']").prop('disabled', true);
				$("select[name='form[other_type]']").prop('disabled', true);
			}
			else if (value === "haus") {
				$("select[name='form[apartment_type]']").prop('disabled', true);
				$("select[name='form[house_type]']").prop('disabled', false);
				$("select[name='form[land_type]']").prop('disabled', true);
				$("select[name='form[office_type]']").prop('disabled', true);
				$("select[name='form[other_type]").prop('disabled', true);
			}
			else if (value === "grundstueck") {
				$("select[name='form[apartment_type]']").prop('disabled', true);
				$("select[name='form[house_type]']").prop('disabled', true);
				$("select[name='form[land_type]']").prop('disabled', false);
				$("select[name='form[office_type]']").prop('disabled', true);
				$("select[name='form[other_type]").prop('disabled', true);
			}
			else if (value === "buero_praxen") {
				$("select[name='form[apartment_type]']").prop('disabled', true);
				$("select[name='form[house_type]']").prop('disabled', true);
				$("select[name='form[land_type]']").prop('disabled', true);
				$("select[name='form[office_type]']").prop('disabled', false);
				$("select[name='form[other_type]").prop('disabled', true);
			}
			else if (value === "sonstige") {
				$("select[name='form[apartment_type]']").prop('disabled', true);
				$("select[name='form[house_type]']").prop('disabled', true);
				$("select[name='form[land_type]']").prop('disabled', true);
				$("select[name='form[office_type]']").prop('disabled', true);
				$("select[name='form[other_type]").prop('disabled', false);
			};
			if (value === "grundstueck") {
				$("select[name='form[energy_pass]']").removeAttr('required');
				$("select[name='form[energy_pass]']").prop('disabled', true);
				$("input[name='form[energy_consumption]']").removeAttr('required');
				$("input[name='form[energy_consumption]']").prop('disabled', true);
				$("input[name='form[energy_pass_valid_until]']").removeAttr('required');
				$("input[name='form[energy_pass_valid_until]']").prop('disabled', true);
				$("select[name='form[condition_type]']").prop('disabled', true);
			}
			else {
				$("select[name='form[energy_pass]']").prop('required', true);
				$("select[name='form[energy_pass]']").prop('disabled', false);
				$("input[name='form[energy_consumption]']").prop('required', true);
				$("input[name='form[energy_consumption]']").prop('disabled', false);
				$("input[name='form[energy_pass_valid_until]']").prop('required', true);
				$("input[name='form[energy_pass_valid_until]']").prop('disabled', false);
				$("select[name='form[condition_type]']").prop('disabled', false);
			}
		}
		
		function market_type_changer(value) {
			if (value === "KAUF") {
				$("input[name='form[purchase_price]']").prop('disabled', false);
				$("input[name='form[purchase_price]']").prop('required', true);
				$("input[name='form[purchase_price_m2]").prop('disabled', false);
				$("input[name='form[purchase_price_m2]").prop('required', true);
				$("input[name='form[cold_rent]']").prop('disabled', true);
				$("input[name='form[cold_rent]']").removeAttr('required');
				$("input[name='form[additional_costs]']").prop('disabled', true);
				$("input[name='form[additional_costs]']").removeAttr('required');
				$("input[name='form[deposit]']").prop('disabled', true);
				$("input[name='form[deposit]']").removeAttr('required');
			}
			else {
				$("input[name='form[purchase_price]']").prop('disabled', true);
				$("input[name='form[purchase_price]']").removeAttr('required');
				$("input[name='form[purchase_price_m2]").prop('disabled', true);
				$("input[name='form[purchase_price_m2]").removeAttr('required');
				$("input[name='form[cold_rent]']").prop('disabled', false);
				$("input[name='form[cold_rent]']").prop('required', true);
				$("input[name='form[additional_costs]']").prop('disabled', false);
				$("input[name='form[additional_costs]']").prop('required', true);
				$("input[name='form[deposit]']").prop('disabled', false);
				$("input[name='form[deposit]']").prop('required', true);
			};			
		}

		// Disable on document load
		$(document).ready(function() {
			market_type_changer($("select[name='form[market_type]']").val());
			object_type_changer($("select[name='form[object_type]']").val());
		});

		// Disable on selection change
		$("select[name='form[market_type]']").on('change', function(e) {
			market_type_changer($(this).val());
		});
		$("select[name='form[object_type]']").on('change', function(e) {
			object_type_changer($(this).val());
		});
	</script>
	<?php
}

if ($func == '') {
	$query = 'SELECT properties.property_id, lang.name AS propertyname, categories.name AS categoryname, priority '
		. 'FROM '. rex::getTablePrefix() .'d2u_immo_properties AS properties '
		. 'LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_properties_lang AS lang '
			. 'ON properties.property_id = lang.property_id AND lang.clang_id = '. rex_config::get("d2u_immo", "default_lang") .' '
		. 'LEFT JOIN '. rex::getTablePrefix() .'d2u_immo_categories_lang AS categories '
			. 'ON properties.category_id = categories.category_id AND categories.clang_id = '. rex_config::get("d2u_immo", "default_lang") .' ';
	if($this->getConfig('default_property_sort') == 'priority') {
		$query .= 'ORDER BY priority ASC';
	}
	else {
		$query .= 'ORDER BY propertyname ASC';
	}
    $list = rex_list::factory($query);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon fa-home"></i>';
    $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###property_id###']);

    $list->setColumnLabel('property_id', rex_i18n::msg('id'));
    $list->setColumnLayout('property_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);

    $list->setColumnLabel('propertyname', rex_i18n::msg('d2u_immo_name'));
    $list->setColumnParams('propertyname', ['func' => 'edit', 'entry_id' => '###property_id###']);

    $list->setColumnLabel('categoryname', rex_i18n::msg('d2u_immo_category'));

	$list->setColumnLabel('priority', rex_i18n::msg('header_priority'));

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('system_update'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###property_id###']);

    $list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
    $list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###property_id###']);
    $list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_immo_confirm_delete'));

    $list->setNoRowsMessage(rex_i18n::msg('d2u_immo_category_no_categories_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_immo_properties'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}