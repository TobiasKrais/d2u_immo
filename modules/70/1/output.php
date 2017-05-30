<?php
if(!function_exists('printPropertylist')) {
	/**
	 * Prints property list
	 * @param Property $properties Array with properties
	 */
	function printPropertylist($properties) {
		$sprog = rex_addon::get("sprog");
		$tag_open = $sprog->getConfig('wildcard_open_tag');
		$tag_close = $sprog->getConfig('wildcard_close_tag');
		foreach($properties as $property) {
			print '<div class="row">';
			print '<div class="col-12">';
			
			print '<a href="'. $property->getURL() .'"><div class="expose">';
			print '<div class="row">';
			
			print '<div class="col-12 col-sm-4 col-lg-2">';
			if(count($property->pictures) > 0) {
				print '<img src="index.php?rex_media_type=d2u_immo_list_tile&rex_media_file='.
						$property->pictures[0] .'" alt='. $property->name .' class="listpic">';
			}
			print '</div>';

			print '<div class="col-12 col-sm-8 col-lg-10">';
			print '<div class="row">';
			print '<div class="col-12"><strong>'. $property->name .'</strong></div>';
			print '<div class="col-12 col-sm-6 nolink"><b>'. $tag_open .'d2u_immo_form_city'. $tag_close .':</b> '. $property->city .'</div>';
			if($property->market_type = "KAUF") {
				print '<div class="col-12 col-sm-6 nolink"><b>'. $tag_open .'d2u_immo_purchase_price'. $tag_close .':</b> '. number_format($property->purchase_price, 0, ",", ".") .',- '. $property->currency_code .'</div>';
			}
			else if($property->market_type = "MIETE_PACHT" || $property->market_type = "ERBPACHT") {
				print '<div class="col-12 col-sm-6 nolink"><b>'. $tag_open .'d2u_immo_cold_rent'. $tag_close .':</b> '. number_format($property->cold_rent, 2, ",", ".") .' '. $property->currency_code .'</div>';
			}
			else if($property->market_type = "LEASING") {
				print '<div class="col-12 col-sm-6 nolink"><b>'. $tag_open .'d2u_immo_leasehold'. $tag_close .':</b> '. number_format($property->cold_rent, 2, ",", ".") .' '. $property->currency_code .'</div>';
			}
			if($property->living_area > 0) {
				print '<div class="col-12 col-sm-6 nolink"><b>'. $tag_open .'d2u_immo_living_area'. $tag_close .':</b> '. $property->living_area .' m²</div>';
			}
			else if($property->land_area > 0) {
				print '<div class="col-12 col-sm-6 nolink"><b>'. $tag_open .'d2u_immo_land_area'. $tag_close .':</b> '. $property->land_area .' m²</div>';
			}
			print '<div class="col-12 nolink">'. $property->teaser .'</div>';
			print '</div>';
			print '</div>';
			
			print '</div>';
			print '</div></a>';

			print '</div>';
			print '</div>';
		}
	}
}

// Get placeholder wildcard tags and other presets
$sprog = rex_addon::get("sprog");
$tag_open = $sprog->getConfig('wildcard_open_tag');
$tag_close = $sprog->getConfig('wildcard_close_tag');
$d2u_immo = rex_addon::get("d2u_immo");
$urlParamKey = "";
if(rex_addon::get("url")->isAvailable()) {
	$url_data = UrlGenerator::getData();
	$urlParamKey = isset($url_data->urlParamKey) ? $url_data->urlParamKey : "";
}

if(filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && $urlParamKey === "property_id")) {
	// Output property
	$property_id = filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT);
	if(rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
		$property_id = UrlGenerator::getId();
	}
	$property = new Property($property_id, rex_clang::getCurrentId());

	print '<div class="col-12 expose-navi">';
	print '<ul>';
	print '<li><a href="'. rex_getUrl($d2u_immo->getConfig('article_id')) .'"><span class="icon back"></span> '. $tag_open .'d2u_immo_back_to_list'. $tag_close .'</a></li>';
	print '<li><a href="'. rex_getUrl($d2u_immo->getConfig('article_id')) .'?print=short_expose"><span class="icon print"></span> '. $tag_open .'d2u_immo_print_short_expose'. $tag_close .'</a></li>';
	print '<li><a href="'. rex_getUrl($d2u_immo->getConfig('article_id')) .'?print=expose"><span class="icon print"></span> '. $tag_open .'d2u_immo_print_expose'. $tag_close .'</a></li>';
	if($property->market_type == "MIETE_PACHT" && $d2u_immo->hasConfig('even_informative_pdf') && $d2u_immo->getConfig('even_informative_pdf') != '') {
		print '<li><a href="'. rex_url::media('mieterselbstauskunft.pdf') .'"><span class="icon pdf"></span> '. $tag_open .'d2u_immo_tentant_information'. $tag_close .'</a></li>';
	}
	print '</ul>';
	print '</div>';
	
	// Tabs
	print '<div class="col-12">';
	print '<ul class="nav nav-pills">';
	print '<li class="nav-item"><a data-toggle="tab" href="#tab_overview" class="active">'. $tag_open .'d2u_immo_tab_overview'. $tag_close .'</a></li>';
	if(count($property->pictures) > 0) {
		print '<li class="nav-item"><a data-toggle="tab" href="#tab_pictures">'. $tag_open .'d2u_immo_tab_pictures'. $tag_close .'</a></li>';
	}
	print '<li class="nav-item"><a data-toggle="tab" href="#tab_map">'. $tag_open .'d2u_immo_tab_map'. $tag_close .'</a></li>';
	if($property->market_type == "KAUF") {
		print '<li class="nav-item"><a data-toggle="tab" href="#tab_calculator">'. $tag_open .'d2u_immo_tab_calculator'. $tag_close .'</a></li>';
	}
	print '<li class="nav-item"><a data-toggle="tab" href="#tab_request">'. $tag_open .'d2u_immo_tab_request'. $tag_close .'</a></li>';
	print '<li class="nav-item"><a data-toggle="tab" href="#tab_recommendation">'. $tag_open .'d2u_immo_tab_recommendation'. $tag_close .'</a></li>';
	print '</ul>';
	print '</div>';
	
	print '<div class="col-12">';
	print '<div class="tab-content">';
	print '<div id="tab_overview" class="tab-pane immo-tab fade in active show">';
	
	// Overview
	print '<div class="row">';

	print '<div class="col-12">';
	print '<h1>'. $property->name .'</h1>';
	print '</div>';
	if(count($property->pictures) > 0) {
		print '<div class="col-12 col-md-6">';
		print '<img src="index.php?rex_media_type=d2u_immo_overview&rex_media_file='.
				$property->pictures[0] .'" alt='. $property->name .' class="overviewpic">';
		print '</div>';

		print '<div class="col-12 col-md-6">';
	}
	else {
		print '<div class="col-12">';
	}
	print '<div class="row">';

	if(strtoupper($property->market_type) == "KAUF") {
		if($property->purchase_price > 0) {
			print '<div class="col-6">'. $tag_open .'d2u_immo_purchase_price'. $tag_close .':</div>';
			print '<div class="col-6"><b>'. number_format($property->purchase_price, 0, ",", ".") .',-&nbsp;'. $property->currency_code .'</b></div>';
		}
		if($property->purchase_price_m2 > 0) {
			print '<div class="col-6">'. $tag_open .'d2u_immo_purchase_price_m2'. $tag_close .':</div>';
			print '<div class="col-6">'. number_format($property->purchase_price_m2, 2, ",", ".") .'&nbsp;'. $property->currency_code .'</div>';
		}
	}
	else {
		if($property->cold_rent > 0 && $property->additional_costs > 0) {
			print '<div class="col-6">'. $tag_open .'d2u_immo_warm_rent'. $tag_close .':</div>';
			print '<div class="col-6"><b>'. number_format($property->cold_rent + $property->additional_costs, 2, ",", ".") .'&nbsp;'. $property->currency_code .'</b></div>';
		}
		if($property->cold_rent > 0) {
			print '<div class="col-6">'. $tag_open .'d2u_immo_cold_rent'. $tag_close .':</div>';
			print '<div class="col-6">'. number_format($property->cold_rent, 2, ",", ".") .'&nbsp;'. $property->currency_code .'</div>';
		}
		if($property->additional_costs > 0) {
			print '<div class="col-6">'. $tag_open .'d2u_immo_additional_costs'. $tag_close .':</div>';
			print '<div class="col-6">'. number_format($property->additional_costs, 2, ",", ".") .'&nbsp;'. $property->currency_code .'</div>';
		}
		if($property->deposit != "") {
			print '<div class="col-6">'. $tag_open .'d2u_immo_deposit'. $tag_close .':</div>';
			print '<div class="col-6">'. $property->deposit .'</div>';
		}
	}
	
	if(strtoupper($property->object_type) == "HAUS" || strtoupper($property->object_type) == "WOHNUNG" || strtoupper($property->object_type) == "BUERO_PRAXEN") {
		if($property->living_area > 0) {
			if(strtoupper($property->object_type) == "HAUS" || strtoupper($property->object_type) == "WOHNUNG") {
				print '<div class="col-6">'. $tag_open .'d2u_immo_living_area'. $tag_close .':</div>';
			}
			else if(strtoupper($property->object_type) == "BUERO_PRAXEN") {
				print '<div class="col-6">'. $tag_open .'d2u_immo_office_area'. $tag_close .':</div>';
			}
			print '<div class="col-6">'. number_format($property->living_area, 2, ",", ".") .'&nbsp;m²</div>';
		}

		if($property->rooms > 0) {
			print '<div class="col-6">'. $tag_open .'d2u_immo_rooms'. $tag_close .':</div>';
			print '<div class="col-6">'. $property->rooms .'</div>';
		}
		
		if($property->construction_year > 0) {
			print '<div class="col-6">'. $tag_open .'d2u_immo_construction_year'. $tag_close .':</div>';
			print '<div class="col-6">'. $property->construction_year .'</div>';
		}

		if($property->floor > 0) {
			print '<div class="col-6">'. $tag_open .'d2u_immo_floor'. $tag_close .':</div>';
			print '<div class="col-6">'. $property->floor .'</div>';
		}

		if($property->residential_community_possible) {
			print '<div class="col-6">'. $tag_open .'d2u_immo_residential_community'. $tag_close .':</div>';
			print '<div class="col-6">'. $tag_open .'d2u_immo_yes'. $tag_close .'</div>';
		}

		if($property->condition_type != "") {
			print '<div class="col-6">'. $tag_open .'d2u_immo_condition'. $tag_close .':</div>';
			print '<div class="col-6">'. $tag_open .'d2u_immo_condition_'. $property->condition_type . $tag_close .'</div>';
		}

		if($property->available_from != "") {
			print '<div class="col-6">'. $tag_open .'d2u_immo_available_from'. $tag_close .':</div>';
			print '<div class="col-6">'. date_format(date_create_from_format('Y-m-d', $property->available_from), "d.m.Y") .'</div>';
		}

		if($property->animals) {
			print '<div class="col-6">'. $tag_open .'d2u_immo_animals'. $tag_close .':</div>';
			print '<div class="col-6">'. $tag_open .'d2u_immo_yes'. $tag_close .'</div>';
		}

		if($property->rented) {
			print '<div class="col-6">'. $tag_open .'d2u_immo_rented'. $tag_close .':</div>';
			print '<div class="col-6">'. $tag_open .'d2u_immo_yes'. $tag_close .'</div>';
		}

		if($property->parking_space_duplex > 0) {
			print '<div class="col-6">'. $tag_open .'d2u_immo_parking_space_duplex'. $tag_close .':</div>';
			print '<div class="col-6">'. $property->parking_space_duplex .'</div>';
		}

		if($property->parking_space_simple > 0) {
			print '<div class="col-6">'. $tag_open .'d2u_immo_parking_space_simple'. $tag_close .':</div>';
			print '<div class="col-6">'. $property->parking_space_simple .'</div>';
		}

		if($property->parking_space_garage > 0) {
			print '<div class="col-6">'. $tag_open .'d2u_immo_parking_space_garage'. $tag_close .':</div>';
			print '<div class="col-6">'. $property->parking_space_garage .'</div>';
		}

		if($property->parking_space_undergroundcarpark > 0) {
			print '<div class="col-6">'. $tag_open .'d2u_immo_parking_space_undergroundcarpark'. $tag_close .':</div>';
			print '<div class="col-6">'. $property->parking_space_undergroundcarpark .'</div>';
		}
	}

	if($property->total_area > 0) {
		print '<div class="col-6">'. $tag_open .'d2u_immo_total_area'. $tag_close .':</div>';
		print '<div class="col-6">'. $property->total_area .'</div>';
	}

	if(count($property->documents) > 0 || ($property->market_type == "MIETE_PACHT" && $d2u_immo->hasConfig('even_informative_pdf') && $d2u_immo->getConfig('even_informative_pdf') != '')) {
		print '<div class="col-12">';
		foreach($property->documents as $document) {
			$media = rex_media::get($document);
			if($media instanceof rex_media) {
				print '<a href="'. rex_url::media($document) .'"><span class="icon pdf"></span> '. $media->getTitle() .'</a><br>';
			}
		}
		if($property->market_type == "MIETE_PACHT" && $d2u_immo->hasConfig('even_informative_pdf') && $d2u_immo->getConfig('even_informative_pdf') != '') {
			print '<li><a href="'. rex_url::media('mieterselbstauskunft.pdf') .'"><span class="icon pdf"></span> '. $tag_open .'d2u_immo_tentant_information'. $tag_close .'</a></li>';
		}
		print '</div>';
	}
	
	print '</div>';
	print '</div>';
	
	print '<div class="col-12">';
	print '<div class="row">';
	if(strlen($property->energy_pass) > 5) {
		print '<div class="col-6 col-md-4 col-lg-3"><b>'. $tag_open .'d2u_immo_energy_pass'. $tag_close .': </b></div>';
		print '<div class="col-6 col-md-8 col-lg-9"><b>'. $tag_open .'d2u_immo_energy_pass_'. $property->energy_pass . $tag_close .'</b></div>';

		print '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. $tag_open .'d2u_immo_energy_pass_valid_until'. $tag_close .':</li></ul></div>';
		print '<div class="col-6 col-md-8 col-lg-9">'. date_format(date_create_from_format('Y-m-d', $property->energy_pass_valid_until), "d.m.Y") .'</div>';

		print '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. $tag_open .'d2u_immo_energy_pass_value'. $tag_close .':</li></ul></div>';
		print '<div class="col-6 col-md-8 col-lg-9">'. $property->energy_consumption .'&nbsp;kWh/(m²*a)</div>';

		if($property->including_warm_water) {
			print '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. $tag_open .'d2u_immo_energy_pass_incl_warm_water'. $tag_close .':</li></ul></div>';
			print '<div class="col-6 col-md-8 col-lg-9">'. $tag_open .'d2u_immo_yes'. $tag_close .'</div>';
		}
		
		if($property->construction_year > 0) {
			print '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. $tag_open .'d2u_immo_construction_year'. $tag_close .':</li></ul></div>';
			print '<div class="col-6 col-md-8 col-lg-9">'. $property->construction_year .'</div>';
		}

		if($property->firing_type > 0) {
			print '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. $tag_open .'d2u_immo_firing_type'. $tag_close .':</li></ul></div>';
			print '<div class="col-6 col-md-8 col-lg-9">';
			$first_element = TRUE;
			foreach($property->firing_type as $firing_type) {
				print ($first_element ? "" : ", ") . $tag_open .'d2u_immo_firing_type_'. $firing_type . $tag_close;
				$first_element = FALSE;
			}
			print '</div>';
		}

		print '<div class="col-12">';
		print "<div style='position: relative; height: 25px; margin-top: 10px;'>";
		print "<div style='position: absolute;'>";
		print "<img src='". $d2u_immo->getAssetsUrl("energieskala.png") ."' class='energy_scale'>";
		print "</div>";
		print "<div style='position: absolute; margin-left: ". round($property->energy_consumption - 10,0) ."px !important;'>";
		print "<img src='". $d2u_immo->getAssetsUrl("zeiger.png") ."'>";
		print "</div>";
		print "</div>";
		print '</div>';
	}
	print '</div>';
	print '</div>';
	
	if(strtoupper($property->object_type) == "HAUS" || strtoupper($property->object_type) == "WOHNUNG" || strtoupper($property->object_type) == "BUERO_PRAXEN") {
		print '<div class="col-12"><strong>'. $tag_open .'d2u_immo_equipment'. $tag_close .'</strong></div>';
		
		if(count($property->bath) > 0) {
			print '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. $tag_open .'d2u_immo_bath'. $tag_close .':</li></ul></div>';
			print '<div class="col-6 col-md-8 col-lg-9">';
			$first_element = TRUE;
			foreach($property->bath as $bath) {
				print ($first_element ? "" : ", ") . $tag_open .'d2u_immo_bath_'. $bath . $tag_close;
				$first_element = FALSE;
			}
			print '</div>';
		}

		if(count($property->kitchen) > 0) {
			print '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. $tag_open .'d2u_immo_kitchen'. $tag_close .':</li></ul></div>';
			print '<div class="col-6 col-md-8 col-lg-9">';
			$first_element = TRUE;
			foreach($property->kitchen as $kitchen) {
				print ($first_element ? "" : ", ") . $tag_open .'d2u_immo_kitchen_'. $kitchen . $tag_close;
				$first_element = FALSE;
			}
			print '</div>';
		}
		
		if(count($property->floor_type) > 0) {
			print '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. $tag_open .'d2u_immo_floor_type'. $tag_close .':</li></ul></div>';
			print '<div class="col-6 col-md-8 col-lg-9">';
			$first_element = TRUE;
			foreach($property->floor_type as $floor_type) {
				print ($first_element ? "" : ", ") . $tag_open .'d2u_immo_floor_type_'. $floor_type . $tag_close;
				$first_element = FALSE;
			}
			print '</div>';
		}
		
		if(count($property->elevator) > 0) {
			print '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. $tag_open .'d2u_immo_elevator'. $tag_close .':</li></ul></div>';
			print '<div class="col-6 col-md-8 col-lg-9">';
			$first_element = TRUE;
			foreach($property->elevator as $elevator) {
				print ($first_element ? "" : ", ") . $tag_open .'d2u_immo_elevator_'. $elevator . $tag_close;
				$first_element = FALSE;
			}
			print '</div>';
		}
		
		if($property->cable_sat_tv) {
			print '<div class="col-12">'. $tag_open .'d2u_immo_cable_sat_tv'. $tag_close .'</div>';
		}

		if($property->broadband_internet != "") {
			print '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. $tag_open .'broadband_internet'. $tag_close .':</li></ul></div>';
			print '<div class="col-6 col-md-8 col-lg-9">'. $property->broadband_internet .'</div>';
		}
	
		if($property->description != "") {
			print '<div class="col-12"><b>'. $tag_open .'d2u_immo_description'. $tag_close .'</b></div>';
			print '<div class="col-12">'. $property->description .'</div>';
		}

		if($property->description_location != "") {
			print '<div class="col-12"><b>'. $tag_open .'d2u_immo_description_location'. $tag_close .'</b></div>';
			print '<div class="col-12">'. $property->description_location .'</div>';
		}

	}

	print '</div>';

/*
		if (strlen($lage) > 0) {
			print "<p><strong>###DETAIL_LAGE###</strong><br />";
			print $lage;
			print "</p>";	
		}
		if (strlen($ausstattung) > 0) {
			print "<p><strong>###DETAIL_AUSSTATTUNG###</strong><br />";
			print $ausstattung;
			print "</p>";	
		}
		if (strlen($beschreibung_sonstige) > 0) {
			print "<p><strong>###DETAIL_BESCHREIBUNG_SONSTIGE###</strong><br />";
			print $beschreibung_sonstige;
			print "</p>";	
		}
		print "<p><strong>Provision</strong><br />";
		if (strlen($aussen_courtage) == 0 || strtolower($aussen_courtage) == "keine") {
			print "Keine Provision";
		}
		else {
			print $aussen_courtage; 
			if ($aussen_courtage_mit_mwst == "1") { 
				print " inklusive gesetzliche Mehrwertsteuer";
			}
		}		
		print "</p>";

//		print "<h2>###DETAIL_KONTAKT###: </h2>";
//		print $c_anrede.' '.$c_vorname.' '.$c_nachname;;
//		print "<br />";
//		print "<strong>###DETAIL_PLZ###: </strong>";
//		print $c_plz; 
//		print "<br />";
//		print "<strong>###DETAIL_ORT###: </strong>";
//		print $c_ort; 
//		print "<br />";
//		print "<strong>###DETAIL_STRASSE###: </strong>";
//		print $c_strasse; 
//		print "<br />";
//		print "<strong>###DETAIL_HAUSNUMMER###: </strong>";
//		print $c_hausnummer; 
//		print "<br />";
//		print "<strong>###DETAIL_LAND###: </strong>";
//		print $c_land; 
//		print "<br />";						
*/













	print '</div>';
	if(count($property->pictures) > 0) {
		print '<div id="tab_pictures" class="tab-pane immo-tab fade">';
	//	propertyShowItem.inc_bilder.inc.php";
		print '</div>';
	}
	print '<div id="tab_map" class="tab-pane immo-tab fade">';
	if(count($property->publish_address) > 0) {
	//	include "propertyShowItem.inc_karte.inc.php";
	}
	print '</div>';
	if($property->market_type == "KAUF") {
		print '<div id="tab_calculator" class="tab-pane immo-tab fade">';
//		include "propertyShowItem.inc_finanzierungsrechner.inc.php";
		print '</div>';
	}
	print '<div id="tab_request" class="tab-pane immo-tab fade in active show">';
//	include "propertyShowItem.inc_anfrage.inc.php";
	print '</div>';
	print '<div id="tab_recommendation" class="tab-pane immo-tab fade in active show">';
//	include "propertyShowItem.inc_weiterempfehlen.inc.php";
	print '</div>';
	print '</div>';
	print '</div>';
}
else {
	// Output property list
	$properties_leasehold = Property::getAll(Rex_clang::getCurrentId(), "ERBPACHT", TRUE);
	$properties_leasing = Property::getAll(Rex_clang::getCurrentId(), "LEASING", TRUE);
	$properties_rent = Property::getAll(Rex_clang::getCurrentId(), "MIETE_PACHT", TRUE);
	$properties_sale = Property::getAll(Rex_clang::getCurrentId(), "KAUF", TRUE);

	// Tabs
	print '<div class="col-12">';
	print '<ul class="nav nav-pills">';
	$tab_active = TRUE;
	if(count($properties_sale) > 0) {
		print '<li class="nav-item"><a data-toggle="tab" href="#tab_sale"'. ($tab_active ? ' class="active"' : '') .'>'. $tag_open .'d2u_immo_tab_sale'. $tag_close .'</a></li>';
		$tab_active = FALSE;
	}
	if(count($properties_rent) > 0) {
		print '<li class="nav-item"><a data-toggle="tab" href="#tab_rent"'. ($tab_active ? ' class="active"' : '') .'>'. $tag_open .'d2u_immo_tab_rent'. $tag_close .'</a></li>';
		$tab_active = FALSE;
	}
	if(count($properties_leasing) > 0) {
		print '<li class="nav-item"><a data-toggle="tab" href="#tab_leasing"'. ($tab_active ? ' class="active"' : '') .'>'. $tag_open .'d2u_immo_tab_leasing'. $tag_close .'</a></li>';
		$tab_active = FALSE;
	}
	if(count($properties_leasehold) > 0) {
		print '<li class="nav-item"><a data-toggle="tab" href="#tab_leasehold"'. ($tab_active ? ' class="active"' : '') .'>'. $tag_open .'d2u_immo_tab_leasehold'. $tag_close .'</a></li>';
		$tab_active = FALSE;
	}
	print '</ul>';
	print '</div>';
	
	print '<div class="col-12">';
	print '<div class="tab-content">';
	$tab_active = TRUE;
	if(count($properties_sale) > 0) {
		print '<div id="tab_sale" class="tab-pane immo-tab fade'. ($tab_active ? ' in active show' : '') .'">';
		printPropertylist($properties_sale);
		print '</div>';
		$tab_active = FALSE;
	}
	if(count($properties_rent) > 0) {
		print '<div id="tab_rent" class="tab-pane immo-tab fade'. ($tab_active ? ' in active show' : '') .'">';
		printPropertylist($properties_rent);
		print '</div>';
		$tab_active = FALSE;
	}
	if(count($properties_leasing) > 0) {
		print '<div id="tab_leasing" class="tab-pane immo-tab fade'. ($tab_active ? ' in active show' : '') .'">';
		printPropertylist($properties_leasing);
		print '</div>';
		$tab_active = FALSE;
	}
	if(count($properties_leasehold) > 0) {
		print '<div id="tab_leasehold" class="tab-pane immo-tab fade'. ($tab_active ? ' in active show' : '') .'">';
		printPropertylist($properties_leasehold);
		print '</div>';
		$tab_active = FALSE;
	}
	print '</div>';
	print '</div>';
}