<?php
if(!function_exists('printPropertylist')) {
	/**
	 * Sends recommendation mail.
	 * @param yform $yform YForm object with fields and values
	 */
	function sendRecommendation($yform) {
		if(isset($yform->params['values'])) {
			$fields = [];
			foreach($yform->params['values'] as $value) {
				if($value->name != "") {
					$fields[$value->name] = $value->value;
				}
			}
			
			$mail = new rex_mailer();
			$mail->IsHTML(FALSE);
			$mail->CharSet = "utf-8";
			$mail->From = $fields['immo_contact_mail'];
			$mail->FromName = $fields['immo_contact_name'];
			$mail->Sender = $fields['immo_contact_mail'];

			$mail->AddAddress($fields['receipient_mail'], $fields['receipient_name']);
			$mail->addReplyTo($fields['sender_mail'], $fields['sender_name']);
			$mail->Subject = $fields['immo_name'];
			$mail_body = "Guten Tag ". $fields['receipient_name'] .",\n\n";
			$mail_body .= $fields['sender_name'] ." empfiehlt Ihnen folgende Immobilie: \"". $fields['immo_name'] ."\"\n";
			$mail_body .= "Link zum Objekt: ". $fields['immo_url'] ."\n\n";
			$mail_body .= $fields['sender_name'] ." hat Ihnen dazu folgende Nachricht hinterlassen:\n\n";
			$mail_body .= $fields['message'];
			$mail->Body = $mail_body;
			$mail->Send();
		}
	}
}

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
			
			print '<div class="col-12 col-sm-4 col-lg-3">';
			if(count($property->pictures) > 0) {
				if($property->object_reserved) {
					print '<div class="reserved">';
				}
				print '<img src="index.php?rex_media_type=d2u_helper_sm&rex_media_file='.
						$property->pictures[0] .'" alt='. $property->name .' class="listpic">';
				if($property->object_reserved) {
					print '<span>'. $tag_open .'d2u_immo_object_reserved'. $tag_close .'</span>';
					print '</div>';
				}
			}
			print '</div>';

			print '<div class="col-12 col-sm-8 col-lg-9">';
			print '<div class="row">';
			print '<div class="col-12"><strong>'. $property->name .'</strong></div>';
			print '<div class="col-12 col-lg-6 nolink"><b>'. $tag_open .'d2u_immo_form_city'. $tag_close .':</b> '. $property->city .'</div>';
			if($property->market_type = "KAUF") {
				print '<div class="col-12 col-lg-6 nolink"><b>'. $tag_open .'d2u_immo_purchase_price'. $tag_close .':</b> '. number_format($property->purchase_price, 0, ",", ".") .',- '. $property->currency_code .'</div>';
			}
			else if($property->market_type = "MIETE_PACHT" || $property->market_type = "ERBPACHT") {
				print '<div class="col-12 col-lg-6 nolink"><b>'. $tag_open .'d2u_immo_cold_rent'. $tag_close .':</b> '. number_format($property->cold_rent, 2, ",", ".") .' '. $property->currency_code .'</div>';
			}
			else if($property->market_type = "LEASING") {
				print '<div class="col-12 col-lg-6 nolink"><b>'. $tag_open .'d2u_immo_leasehold'. $tag_close .':</b> '. number_format($property->cold_rent, 2, ",", ".") .' '. $property->currency_code .'</div>';
			}
			if($property->living_area > 0) {
				print '<div class="col-12 col-lg-6 nolink"><b>'. $tag_open .'d2u_immo_living_area'. $tag_close .':</b> '. round($property->living_area) .' m²</div>';
			}
			else if($property->land_area > 0) {
				print '<div class="col-12 col-lg-6 nolink"><b>'. $tag_open .'d2u_immo_land_area'. $tag_close .':</b> '. round($property->land_area) .' m²</div>';
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

if(!function_exists('printImages')) {
	/**
	 * Prints images in Ekko Lightbox module format
	 * @param string[] $pics Array with images
	 */
	function printImages($pics) {
		$type_thumb = "d2u_helper_gallery_thumb";
		$type_detail = "d2u_helper_gallery_detail";
		$lightbox_id = rand();
		
		print '<div class="col-12 print-border">';
		print '<div class="row">';
		foreach($pics as $pic) {
			$media = rex_media::get($pic);
			print '<a href="index.php?rex_media_type='. $type_detail .'&rex_media_file='. $pic .'" data-toggle="lightbox'. $lightbox_id .'" data-gallery="example-gallery'. $lightbox_id .'" class="col-6 col-sm-4 col-lg-3"';
			if($media instanceof rex_media) {
				print ' data-title="'. $media->getValue('title') .'"';
			}
			print '>';
			print '<img src="index.php?rex_media_type='. $type_thumb .'&rex_media_file='. $pic .'" class="img-fluid gallery-pic-box"';
			if($media instanceof rex_media) {
				print ' alt="'. $media->getValue('title') .'" title="'. $media->getValue('title') .'"';
			}
			print '>';
			print '</a>';
		}
		print '</div>';
		print '</div>';
		print "<script>";
		print "$(document).on('click', '[data-toggle=\"lightbox". $lightbox_id ."\"]', function(event) {";
		print "event.preventDefault();";
		print "$(this).ekkoLightbox({ alwaysShowClose: true	});";
		print "});";
		print "</script>";
	}
}

// Get placeholder wildcard tags and other presets
$print = filter_input(INPUT_GET, 'print', FILTER_SANITIZE_SPECIAL_CHARS);
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

	if($print == "") {
		print '<div class="col-12 expose-navi hidden-print">';
		print '<ul>';
		print '<li><small><a href="'. rex_getUrl($d2u_immo->getConfig('article_id')) .'"><span class="icon back"></span> '. $tag_open .'d2u_immo_back_to_list'. $tag_close .'</a></small></li>';
		//	Following links see Chrome print bug: https://github.com/twbs/bootstrap/issues/22753
		print '<li><small><a href="javascript:onclick=window.open(\''. $property->getURL(TRUE).'?print=small\', \'_blank\',\'width=500, height=500\');" target="blank"><span class="icon print"></span> '. $tag_open .'d2u_immo_print_short_expose'. $tag_close .'</a></small></li>';
		print '<li><small><a href="javascript:onclick=window.open(\''. $property->getURL(TRUE).'?print=full\', \'_blank\',\'width=500, height=500\');" target="blank"><span class="icon print"></span> '. $tag_open .'d2u_immo_print_expose'. $tag_close .'</a></small></li>';
		if($property->market_type == "MIETE_PACHT" && $d2u_immo->hasConfig('even_informative_pdf') && $d2u_immo->getConfig('even_informative_pdf') != '') {
			print '<li><small><a href="'. rex_url::media('mieterselbstauskunft.pdf') .'"><span class="icon pdf"></span> '. $tag_open .'d2u_immo_tentant_information'. $tag_close .'</a></small></li>';
		}
		print '</ul>';
		print '</div>';

		print '<div class="col-12 visible-print-inline">';
		print '<p>'. $property->contact->firstname .' '. $property->contact->lastname .'<br>';
		print $tag_open .'d2u_immo_form_phone'. $tag_close .': '. $property->contact->phone .'<br>';
		print $tag_open .'d2u_immo_form_email'. $tag_close .': '. $property->contact->email .'<p>';
		print '</div>';
	}
	
	// Tabs
	if($print == "") {
		print '<div class="col-12 hidden-print">';
		print '<ul class="nav nav-pills" id="expose_tabs">';
		print '<li class="nav-item"><a data-toggle="tab" href="#tab_overview" class="active"><span class="icon home hidden-md-up"></span><span class="hidden-sm-down">'. $tag_open .'d2u_immo_tab_overview'. $tag_close .'</span></a></li>';
		if(count($property->pictures) > 0) {
			print '<li class="nav-item"><a data-toggle="tab" href="#tab_pictures"><span class="icon pic hidden-md-up"></span><span class="hidden-sm-down">'. $tag_open .'d2u_immo_tab_pictures'. $tag_close .'</span></a></li>';
		}
		print '<li class="nav-item"><a data-toggle="tab" href="#tab_map"><span class="icon map hidden-md-up"></span><span class="hidden-sm-down">'. $tag_open .'d2u_immo_tab_map'. $tag_close .'</span></a></li>';
		if($property->market_type == "KAUF") {
			print '<li class="nav-item"><a data-toggle="tab" href="#tab_calculator"><span class="icon money hidden-md-up"></span><span class="hidden-sm-down">'. $tag_open .'d2u_immo_tab_calculator'. $tag_close .'</span></a></li>';
		}
		print '<li class="nav-item"><a data-toggle="tab" href="#tab_request"><span class="icon request hidden-md-up"></span><span class="hidden-sm-down">'. $tag_open .'d2u_immo_tab_request'. $tag_close .'</span></a></li>';
		print '<li class="nav-item"><a data-toggle="tab" href="#tab_recommendation"><span class="icon forward hidden-md-up"></span><span class="hidden-sm-down">'. $tag_open .'d2u_immo_tab_recommendation'. $tag_close .'<span></a></li>';
		print '</ul>';
		print '</div>';
	}
	
	if($print == "") {
		print '<div class="col-12">'; // START div containing tab content
		print '<div class="tab-content">'; // START tab content
		print '<div id="tab_overview" class="tab-pane immo-tab fade in active show">'; // START tab overview
	}
	
	// Overview
	print '<div class="row page-break-avoid">'; // START row overview

	print '<div class="col-12 print-border-h">';
	print '<h1>'. $property->name .'</h1>';
	print '</div>';
	print '<div class="col-12 print-border visible-print-inline">';
	print '<p>'. $property->street .' '. $property->house_number .', '. $property->zip_code .' '. $property->city .'</p>';
	print '</div>';
	print '<div class="col-12 print-border">'; // START overview picture and short info
	print '<div class="row">';
	
	if(count($property->pictures) > 0) {
		print '<div class="col-12 col-md-6">'; // START overview picture
		if($property->object_reserved || $property->object_sold) {
			print '<div class="reserved">';
		}
		print '<img src="index.php?rex_media_type=d2u_helper_sm&rex_media_file='.
				$property->pictures[0] .'" alt="'. $property->name .'" class="overviewpic">';
		if($property->object_reserved) {
			print '<span class="hidden-print">'. $tag_open .'d2u_immo_object_reserved'. $tag_close .'</span>';
		}
		else if($property->object_sold) {
			print '<span class="hidden-print">'. $tag_open .'d2u_immo_object_sold'. $tag_close .'</span>';										
		}
		if($property->object_reserved || $property->object_sold) {
			print '</div>'; // <div class="reserved">
		}
		print '</div>'; // END overview picture
		print '<div class="col-12 col-md-6">'; // START short info
	}
	else {
		print '<div class="col-12">'; // START short info
	}
	print '<div class="row page-break-avoid">';

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

		if($property->flat_sharing_possible) {
			print '<div class="col-6">'. $tag_open .'d2u_immo_flat_sharing'. $tag_close .':</div>';
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
		print '<div class="col-6">'. $property->total_area .'&nbsp;m²</div>';
	}
	if($property->land_area > 0) {
		print '<div class="col-6">'. $tag_open .'d2u_immo_land_area'. $tag_close .':</div>';
		print '<div class="col-6">'. round($property->land_area) .'&nbsp;m²</div>';
	}

	if(count($property->documents) > 0 || ($property->market_type == "MIETE_PACHT" && $d2u_immo->hasConfig('even_informative_pdf') && $d2u_immo->getConfig('even_informative_pdf') != '')) {
		print '<div class="col-12"><ul>';
		foreach($property->documents as $document) {
			$media = rex_media::get($document);
			if($media instanceof rex_media) {
				print '<li><span class="icon pdf"></span> <a href="'. rex_url::media($document) .'">'. $media->getTitle() .'</a></li>';
			}
		}
		if($property->market_type == "MIETE_PACHT" && $d2u_immo->hasConfig('even_informative_pdf') && $d2u_immo->getConfig('even_informative_pdf') != '') {
			print '<li><span class="icon pdf"></span> <a href="'. rex_url::media('mieterselbstauskunft.pdf') .'">'. $tag_open .'d2u_immo_tentant_information'. $tag_close .'</a></li>';
		}
		print '</ul></div>';
	}
	
	print '</div>';
	print '</div>'; // END short info
	print '</div>';
	print '</div>'; // END overview picture and short info
	if($print != "") { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
		print '</div>';
	}

	print '<div class="col-12">&nbsp;</div>';

	if(strlen($property->energy_pass) > 5) {
		if($print != "") { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
			print '<div class="row page-break-avoid">';
		}
		print '<div class="col-12 print-border-h"><h2>'. $tag_open .'d2u_immo_energy_pass'. $tag_close .'</h2></div>';

		print '<div class="col-12 print-border">'; // START energy pass
		print '<div class="row">';
	
		print '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. $tag_open .'d2u_immo_energy_pass_type'. $tag_close .':</li></ul></div>';
		print '<div class="col-6 col-md-8 col-lg-9">'. $tag_open .'d2u_immo_energy_pass_'. $property->energy_pass . $tag_close .'</div>';

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
		print "<div class='energy-scale-container'>";
		print "<div style='position: absolute;'>";
		print "<img src='". $d2u_immo->getAssetsUrl("energieskala.png") ."' class='energy_scale'>";
		print "</div>";
		print "<div style='position: absolute; margin-left: ". round($property->energy_consumption - 10,0) ."px !important;'>";
		print "<img src='". $d2u_immo->getAssetsUrl("zeiger.png") ."'>";
		print "</div>";
		print "</div>";
		print '</div>';
		print '</div>';
		print '</div>';  // END energy pass
		if($print != "") { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
			print '</div>';
		}
		print '<div class="col-12">&nbsp;</div>';
	}
	
	if((strtoupper($property->object_type) == "HAUS" || strtoupper($property->object_type) == "WOHNUNG" || strtoupper($property->object_type) == "BUERO_PRAXEN")
		&& (count($property->bath) > 0 || count($property->kitchen) > 0 || count($property->floor_type) > 0 || count($property->elevator) > 0 || $property->cable_sat_tv || count($property->broadband_internet) > 0)) {
		if($print != "") { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
			print '<div class="row page-break-avoid">';
		}
		print '<div class="col-12 print-border-h"><h2>'. $tag_open .'d2u_immo_equipment'. $tag_close .'</h2></div>';
		
		print '<div class="col-12 print-border">'; // START detail facts
		print '<div class="row page-break-avoid">';
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

		if(count($property->broadband_internet) > 0) {
			print '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. $tag_open .'d2u_immo_broadband_internet'. $tag_close .':</li></ul></div>';
			print '<div class="col-6 col-md-8 col-lg-9">'. implode(", ", $property->broadband_internet) .'</div>';
		}
		print '</div>';
		print '</div>';  // END detail facts
		
		print '<div class="col-12">&nbsp;</div>';
		if($print != "") { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
			print '</div>';
		}
	}

	if($property->description != "") {
		if($print != "") { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
			print '<div class="row page-break-avoid">';
		}
		print '<div class="col-12 print-border-h"><h2>'. $tag_open .'d2u_immo_description'. $tag_close .'</h2></div>';
		print '<div class="col-12 print-border">'. $property->description .'</div>';
		print '<div class="col-12">&nbsp;</div>';
		if($print != "") { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
			print '</div>';
		}
	}

	if($property->description_location != "") {
		if($print != "") { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
			print '<div class="row page-break-avoid">';
		}
		print '<div class="col-12 print-border-h"><h2>'. $tag_open .'d2u_immo_description_location'. $tag_close .'</h2></div>';
		print '<div class="col-12 print-border">'. $property->description_location .'</div>';
		print '<div class="col-12">&nbsp;</div>';
		if($print != "") { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
			print '</div>';
		}
	}

	if($property->description_equipment != "") {
		if($print != "") { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
			print '<div class="row page-break-avoid">';
		}
		print '<div class="col-12 print-border-h"><h2>'. $tag_open .'d2u_immo_description_equipment'. $tag_close .'</h2></div>';
		print '<div class="col-12 print-border">'. $property->description_equipment .'</div>';
		print '<div class="col-12">&nbsp;</div>';
		if($print != "") { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
			print '</div>';
		}
	}

	if($property->description_others != "") {
		if($print != "") { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
			print '<div class="row page-break-avoid">';
		}
		print '<div class="col-12 print-border-h"><h2>'. $tag_open .'d2u_immo_description_others'. $tag_close .'</h2></div>';
		print '<div class="col-12 print-border">'. $property->description_others .'</div>';
		print '<div class="col-12">&nbsp;</div>';
		if($print != "") { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
			print '</div>';
		}
	}

	if($print != "") { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
		print '<div class="row page-break-avoid">';
	}
	print '<div class="col-12 print-border-h"><h2>'. $tag_open .'d2u_immo_courtage'. $tag_close .'</h2></div>';
	if($property->courtage == "") {
		print '<div class="col-12 print-border">'. $tag_open .'d2u_immo_courtage_no'. $tag_close .'</div>';
	}
	else {
		print '<div class="col-12 print-border">'. $property->courtage .' '. $tag_open .'d2u_immo_courtage_incl_vat'. $tag_close .'</div>';
	}
	print '<div class="col-12 visible-print-inline">&nbsp;</div>';
	if($print != "") { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
		print '</div>';
	}
	print '</div>'; // END row overview
	if($print == "") {
		print '</div>'; // END tab overview
	}
	// End Overview
	// Pictures
	if(count($property->pictures) > 0 && $print != "small") {
		if($print != "full") {
			print '<div id="tab_pictures" class="tab-pane immo-tab fade">'; // START tab picures
		}
		print '<div class="row">'; // START pictures
		print '<div class="col-12 visible-print-inline print-border-h">';
		print "<h2>". $tag_open .'d2u_immo_tab_pictures'. $tag_close ."</h2>";
		print '</div>';
		print '<div class="col-12 hidden-print">';
		print "<h2>". $property->name ."</h2>";
		print '</div>';
		echo printImages($property->pictures);
		print '</div>'; // END pictures

		if(count($property->ground_plans) > 0) {
			print '<div class="row">';
			print '<div class="col-12 print-border-h">';
			print "<h2>". $tag_open .'d2u_immo_ground_plans'. $tag_close ."</h2>";
			print '</div>';
			echo printImages($property->ground_plans);
			print '<div class="col-12 visible-print-inline">&nbsp;</div>';
			print '</div>';			
		}

		if (count($property->location_plans) > 0) {
			print '<div class="row">';
			print '<div class="col-12 print-border-h">';
			print "<h2>". $tag_open .'d2u_immo_location_plans'. $tag_close ."</h2>";
			print '</div>';
			echo printImages($property->location_plans);	
			print '<div class="col-12 visible-print-inline">&nbsp;</div>';
			print '</div>';
		}
		if($print != "full") {
			print '</div>'; // END tab picures
		}
	}
	// End Pictures
	// Map
	if(count($property->publish_address) > 0 && $print != "small") {
		$d2u_helper = rex_addon::get("d2u_helper");
		$api_key = "";
		if($d2u_helper->hasConfig("maps_key")) {
			$api_key = $d2u_helper->getConfig("maps_key");
		}
		if($print != "full") {
			print '<div id="tab_map" class="tab-pane immo-tab fade page-break-avoid">'; // START tab map
		}
		print '<div class="row page-break-avoid">';
		print '<div class="col-12 visible-print-inline print-border-h">';
		print "<h2>". $tag_open .'d2u_immo_tab_map'. $tag_close ."</h2>";
		print '</div>';
		print '<div class="col-12 print-border">';
		print '<h2 class="hidden-print">'. $property->name .'</h2>';
		print '<p class="hidden-print">'. $property->street ." ". $property->house_number ."<br /> ". $property->zip_code ." ". $property->city ."</p>";
?>
		<script type="text/javascript" src="https://maps.google.com/maps/api/js?key=<?php echo $api_key; ?>"></script> 
		<div id="map_canvas" style="display: block; <?php print ($print != '' ? 'width: 900px' : 'width: 100%'); ?>; height: 500px"></div> 
		<script type="text/javascript">
			var map;
			var myLatlng;
			<?php
				// if longitude and latitude are available
				if($property->longitude != 0 && $property->latitude != 0) {
			?>
				var myLatlng = new google.maps.LatLng(<?php echo $property->latitude .", ". $property->longitude; ?>);
				var myOptions = {
					zoom: 15,
					center: myLatlng,
					mapTypeId: google.maps.MapTypeId.<?php print ($print == "full" ? "ROADMAP" : "HYBRID"); ?>
				}
				map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

				var marker = new google.maps.Marker({
					position: myLatlng, 
					map: map
				});

				google.maps.event.addListener(marker, 'click', function() {
					infowindow.open(map,marker);
				});
			<?php
			}
			else {
			?>
				var geocoder = new google.maps.Geocoder();
				var address = "<?php print $property->street ." ". $property->house_number .", ". $property->zip_code ." ". $property->city; ?>";
				if (geocoder) {
					geocoder.geocode( { 'address': address}, function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							myLatlng = results[0].geometry.location;
							map.setCenter(myLatlng);
							var marker = new google.maps.Marker({
								map: map,
								position: myLatlng
							});
						} else {
							alert("Geocode was not successful for the following reason: " + status);
						}
					});
				}

				var myOptions = {
					zoom: 15,
					mapTypeId: google.maps.MapTypeId.<?php print ($print == "full" ? "ROADMAP" : "HYBRID"); ?>
				}
				map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
			<?php
				}
			?>
		</script>
<?php
		print '</div>';
		print '</div>';
		if($print != "full") {
			print '</div>';  // END tab map
		}
		else {
			print '<div class="col-12 visible-print-inline">&nbsp;</div>';
		}
	}
	// End Map
	// Calculator
	if($property->market_type == "KAUF" && $print == "") {
		print '<div id="tab_calculator" class="tab-pane immo-tab fade">'; // START tab calculator
		print '<div class="row">';
		print '<div class="col-12">';
		$real_estate_tax = $d2u_immo->getConfig('finance_calculator_real_estate_tax');
		$notary_costs = $d2u_immo->getConfig('finance_calculator_notary_costs');
		$courtage = strtr(strtr($property->courtage, ",", ".") * 1, ",", ".") / 100;
		$interest_rate = $d2u_immo->getConfig('finance_calculator_interest_rate');
		$repayment = $d2u_immo->getConfig('finance_calculator_repayment');
		
		print '<h2>'. $property->name .'</h2>';
?>
		<script type="text/javascript">
			// Removes thousand separator, substutiutes decimal separator
			function substractNumber(number_string) {
				number_string = number_string.trim();
				number_string = number_string.replace(".", "");
				number_string = number_string.replace(",", ".");
				number_string = number_string.replace(/[^\d\.,]/g, "");
				return number_string;
			}

			// Format numbers for output
			function formatZahl(zahl) {
				var k = 2;
				var neu = '';
				var dec_point = ',';
				var thousands_sep = '.';

				// Round
				var f = Math.pow(10, k);
				zahl = '' + parseInt(zahl * f + (.5 * (zahl > 0 ? 1 : -1)) ) / f ;

				// where is comma
				var idx = zahl.indexOf('.');

				// fill missing zero
				zahl += (idx == -1 ? '.' : '' ) + f.toString().substring(1);

				var sign = zahl < 0;
				if(sign) zahl = zahl.substring(1);
				idx = zahl.indexOf('.');

				// decimal place
				if( idx == -1) {
					idx = zahl.length;
				}
				else {
					neu = dec_point + zahl.substr(idx + 1, k);
				}
				while(idx > 0)    {
					if(idx - 3 > 0) {
						neu = thousands_sep + zahl.substring( idx - 3, idx) + neu;
					}
					else {
						neu = zahl.substring(0, idx) + neu;
					}
					idx -= 3;
				}
				return (sign ? '-' : '') + neu;
			}

			// Recalculate values
			function recalc() {
				// Input Felder auslesen
				var kaufpreis = substractNumber(document.getElementById("kaufpreis").value);
				var provision = substractNumber(document.getElementById("maklerprovision").value) / 100;
				var sonstiges = substractNumber(document.getElementById("sonstiges").value);
				var eigenkapital = substractNumber(document.getElementById("eigenkapital").value);
				var zins = substractNumber(document.getElementById("zinssatz").value) / 100;
				var tilgung = substractNumber(document.getElementById("tilgung").value) / 100;
				var grundsteuer = <?php print $real_estate_tax; ?>;
				var notarkosten = <?php print $notary_costs; ?>;

				// Neue Werte berechnen
				var gesamtkosten = (kaufpreis * (provision + notarkosten + grundsteuer + 1));
				gesamtkosten += (sonstiges * 1);

				var darlehen = gesamtkosten - eigenkapital;
				if(darlehen < 0)
					darlehen = 0;

				if(isNaN(darlehen))
					alert("Bitte geben Sie nur Zahlen, Punkt oder Komma ein.");
				document.getElementById("kaufpreis").value = formatZahl(kaufpreis);
				document.getElementById("preis_grunderwerbsteuer").firstChild.nodeValue = 
						formatZahl(kaufpreis * grundsteuer);
				document.getElementById("preis_notar").firstChild.nodeValue = 
						formatZahl(kaufpreis * notarkosten);
				document.getElementById("maklerprovision").value = formatZahl(provision * 100);
				document.getElementById("preis_maklerprovision").firstChild.nodeValue = 
						formatZahl(kaufpreis * provision);
				document.getElementById("sonstiges").value = formatZahl(sonstiges);
				document.getElementById("gesamtkosten").firstChild.nodeValue =
						formatZahl(gesamtkosten);

				document.getElementById("eigenkapital").value = formatZahl(eigenkapital);
				document.getElementById("darlehen").firstChild.nodeValue =
						formatZahl(darlehen);
				document.getElementById("zinssatz").value = formatZahl(zins * 100);
				document.getElementById("tilgung").value = formatZahl(tilgung * 100);			
				document.getElementById("rate").firstChild.nodeValue = 
						formatZahl(darlehen * (zins + tilgung) / 12);			

				return false;
			}
		</script>
		<form id="finanzierungsrechner" method="post" target="blank">
			<input name="option" value="finanzierungsrechner" type="hidden">
			<fieldset>
				<legend><?php print $tag_open .'d2u_immo_finance_calc_investement'. $tag_close; ?></legend>
				<table style="width: 100%;">
					<tr>
						<td style="width: 45%; height: 30px; text-align: left">
							<strong><label for="kaufpreis"><?php print $tag_open .'d2u_immo_purchase_price'. $tag_close; ?></label></strong>
						</td>
						<td style="width: 20%"></td>
						<td style="width: 5%"></td>
						<td style="width: 25%; text-align: right">
							<input name="kaufpreis" id="kaufpreis" size="15" maxlength="15" 
								value="<?php print number_format($property->purchase_price, 2, ',', '.'); ?>"
								type="text" style="text-align: right;"
								onchange="javascript:recalc();"></td>
						<td style="width: 5%; text-align: right">&euro;</td>
					</tr>
					<tr>
						<td style="height: 30px; text-align: left">
							<label>+ <?php print $tag_open .'d2u_immo_finance_calc_real_estate_tax'. $tag_close; ?></label></td>
						<td style="text-align: right"><div id="grunderwerbsteuer">
							<input type="hidden" name="grunderwerbsteuer" value="<?php print number_format($real_estate_tax * 100, 2, ',', '.'); ?>">
							<?php print number_format($real_estate_tax * 100, 2, ',', '.'); ?></div></td>
						<td style="text-align: right">%</td>
						<td style="text-align: right"><div id="preis_grunderwerbsteuer">
								<?php print number_format($property->purchase_price * $real_estate_tax, 2, ',', '.'); ?></div></td>
						<td style="text-align: right">&euro;</td>
					</tr>
					<tr>
						<td style="height: 30px; text-align: left;">
							<label>+ <?php print $tag_open .'d2u_immo_finance_calc_notary_costs'. $tag_close; ?></label></td>
						<td style="text-align: right"><div id="notar"><input type="hidden" name="notarkosten" value="<?php print number_format($notary_costs * 100, 2, ',', '.'); ?>">ca.
							<?php print number_format($notary_costs * 100, 2, ',', '.'); ?></div></td>
						<td style="text-align: right">%</td>
						<td style="text-align: right"><div id="preis_notar">
							<?php print number_format($property->purchase_price * $notary_costs, 2, ',', '.'); ?></div></td>
						<td style="text-align: right">&euro;</td>
					</tr>
					<tr>
						<td style="height: 30px; text-align: left;">
							<label for="maklerprovision">+ <?php print $tag_open .'d2u_immo_courtage'. $tag_close; ?></label></td>
						<td style="text-align: right"><input name="maklerprovision" id="maklerprovision"
								value="<?php print number_format($courtage * 100, 2, ',', '.'); ?>"
								size="5" maxlength="5" type="text" style="text-align: right;"
								onchange="javascript:recalc();"></td>
						<td style="text-align: right">%</td>
						<td style="text-align: right"><div id="preis_maklerprovision">
								<?php print number_format($property->purchase_price * $courtage, 2, ',', '.'); ?></div></td>
						<td style="text-align: right">&euro;</td>
					</tr>
					<tr>
						<td style="height: 30px; text-align: left;">
							<label for="sonstiges">+ <?php print $tag_open .'d2u_immo_finance_calc_other_costs'. $tag_close; ?></label></td>
						<td></td>
						<td></td>
						<td style="text-align: right"><input name="sonstiges" id="sonstiges"
								value="0,00" size="15" maxlength="15" type="text"
								style="text-align: right;" onchange="javascript:recalc();"></td>
						<td style="text-align: right">&euro;</td>
					</tr>
					<tr>
						<td style="border-top: 1px solid #333; height: 30px; text-align: left;">
							<label><strong><?php print $tag_open .'d2u_immo_finance_calc_total_costs'. $tag_close; ?></strong></label></td>
						<td style="border-top: 1px solid #333;"></td>
						<td style="border-top: 1px solid #333;"></td>
						<td style="border-top: 1px solid #333; text-align: right">
							<div id="gesamtkosten">
								<?php
									$gesamtkosten = $property->purchase_price + ($property->purchase_price * $courtage) + ($property->purchase_price * $real_estate_tax) + ($property->purchase_price * $notary_costs);
									print number_format($gesamtkosten, 2, ',', '.');
								 ?></div></td> 
						<td style="border-top: 1px solid #333; text-align: right">&euro;</td>
					</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend>Finanzierung</legend>
				<table style="width: 100%">
					<tr>
						<td style="width: 45%; text-align: left; height: 30px">
							<label for="eigenkapital"><?php print $tag_open .'d2u_immo_finance_calc_equity'. $tag_close; ?></label>
						</td>
						<td style="width: 20%"></td>
						<td style="width: 5%"></td>
						<td style="width: 25%; text-align: right">
							<input name="eigenkapital" id="eigenkapital" value="0,00"
									size="15" maxlength="15" type="text" style="text-align: right;"
									onchange="javascript:recalc();"></td>
						<td style="width: 5%; text-align: right">&euro;</td>
					</tr>
					<tr>
						<td style="height: 30px; text-align: left;">
							<label><?php print $tag_open .'d2u_immo_finance_calc_required_loan'. $tag_close; ?></label>
						</td>
						<td></td>
						<td></td>
						<td style="text-align: right"><div id="darlehen"><?php print number_format($gesamtkosten, 2, ',', '.'); ?></div></td>
						<td style="text-align: right">&euro;</td>
					</tr>
					<tr>
						<td style="height: 30px; text-align: left;">
							<label for="zinssatz"><?php print $tag_open .'d2u_immo_finance_calc_interest_rate'. $tag_close; ?></label></td>
						<td style="text-align: right"><input name="zinssatz" id="zinssatz"
								value="<?php print number_format($interest_rate * 100, 2, ',', '.'); ?>"
								size="5" maxlength="5" type="text" style="text-align: right;"
								onchange="javascript:recalc();"></td>
						<td style="text-align: right">%</td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td style="height: 30px; text-align: left;">
							<label for="tilgung"><?php print $tag_open .'d2u_immo_finance_calc_repayment'. $tag_close; ?></label></td>
						<td style="text-align: right"><input name="tilgung" id="tilgung"
								value="<?php print number_format($repayment * 100, 2, ',', '.'); ?>"
								size="5" maxlength="5" type="text" style="text-align: right;"
								onchange="javascript:recalc();"></td>
						<td style="text-align: right">%</td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td style="border-top: 1px solid #333; height: 30px; text-align: left;">
							<label><strong><?php print $tag_open .'d2u_immo_finance_calc_monthly_rate'. $tag_close; ?></strong></label></td>
						<td style="border-top: 1px solid #333;"></td>
						<td style="border-top: 1px solid #333;"></td>
						<td style="border-top: 1px solid #333; text-align: right;">
							<div id="rate"><?php print number_format(round((($gesamtkosten * $interest_rate) + ($gesamtkosten * $repayment)) / 12, 2), 2, ',', '.'); ?></div></td>
						<td style="border-top: 1px solid #333; text-align: right">&euro;</td>
					</tr>
				</table>
			</fieldset>
			<br />
			<input name="berechnen" id="berechnen" value="<?php print $tag_open .'d2u_immo_finance_calc_calculate'. $tag_close; ?>" type="submit" onClick="javascript:recalc(); return false;" class="hidden-print">
			<input name="drucken" id="drucken" value="<?php print $tag_open .'d2u_immo_print'. $tag_close; ?>" onClick="javascript:window.print(); return false;" type="submit" class="hidden-print">
		</form>

<?php
		print '</div>';
		print '</div>';
		print '</div>';  // END tab calculator
	}
	if($print == "") {
		print '<div id="tab_request" class="tab-pane immo-tab fade">'; // START tab request
		print '<div class="col-12">';
		print '<fieldset><legend>'. $tag_open .'d2u_immo_form_title'. $tag_close .'</legend>';
		$form_data = 'hidden|immo_name|'. $property->name .'|REQUEST

				text|name|'. $tag_open .'d2u_immo_form_name'. $tag_close .' *
				text|address|'. $tag_open .'d2u_immo_form_address'. $tag_close .'
				text|zip|'. $tag_open .'d2u_immo_form_zip'. $tag_close .'
				text|city|'. $tag_open .'d2u_immo_form_city'. $tag_close .'
				text|phone|'. $tag_open .'d2u_immo_form_phone'. $tag_close .' *
				text|email|'. $tag_open .'d2u_immo_form_email'. $tag_close .' *
				textarea|message|'. $tag_open .'d2u_immo_form_message'. $tag_close .'

				html||<br>* '. $tag_open .'d2u_immo_form_required'. $tag_close .'<br><br>
				captcha|'. $tag_open .'d2u_immo_form_captcha'. $tag_close .'|'. $tag_open .'d2u_immo_form_validate_captcha'. $tag_close .'|'. rex_getUrl('', '', ['property_id' => $property->property_id]) .'

				submit|submit|'. $tag_open .'d2u_immo_form_send'. $tag_close .'|no_db

				validate|empty|name|'. $tag_open .'d2u_immo_form_validate_name'. $tag_close .'
				validate|empty|phone|'. $tag_open .'d2u_immo_form_validate_phone'. $tag_close .'
				validate|empty|email|'. $tag_open .'d2u_immo_form_validate_email'. $tag_close .'
				validate|email|email|'. $tag_open .'d2u_immo_form_validate_email_false'. $tag_close .'

				action|tpl2email|d2u_immo_request|emaillabel|'. $property->contact->email;

		$yform = new rex_yform;
		$yform->setFormData(trim($form_data));
		$yform->setObjectparams("form_action", $property->getUrl());
		$yform->setObjectparams("form_anchor", "tab_request");
		$yform->setObjectparams("Error-occured", $tag_open .'d2u_immo_form_validate_title'. $tag_close);

		// action - showtext
		$yform->setActionField("showtext", array($tag_open .'d2u_immo_form_thanks'. $tag_close));

		echo $yform->getForm();
		print '</fieldset>';
		print '</div>';
		print '</div>'; // END tab request
		// End request form

		// Recommendation form
		print '<div id="tab_recommendation" class="tab-pane immo-tab fade">'; // START tab recommendation
		print '<div class="col-12">';
		print '<fieldset><legend>'. $tag_open .'d2u_immo_recommendation_title'. $tag_close .'</legend>';
		$form_data = 'hidden|immo_name|'. $property->name .'|REQUEST
				hidden|immo_url|'. $property->getURL(TRUE) .'|REQUEST
				hidden|immo_contact_mail|'. $property->contact->email .'|REQUEST
				hidden|immo_contact_name|'. $property->contact->firstname .' '. $property->contact->lastname .'|REQUEST

				text|sender_name|'. $tag_open .'d2u_immo_recommendation_sender_name'. $tag_close .' *
				text|sender_mail|'. $tag_open .'d2u_immo_recommendation_sender_mail'. $tag_close .' *
				text|receipient_name|'. $tag_open .'d2u_immo_recommendation_receipient_name'. $tag_close .' *
				text|receipient_mail|'. $tag_open .'d2u_immo_recommendation_receipient_mail'. $tag_close .' *
				textarea|message|'. $tag_open .'d2u_immo_recommendation_message'. $tag_close .' *

				html||<br>* '. $tag_open .'d2u_immo_form_required'. $tag_close .'<br><br>
				captcha|'. $tag_open .'d2u_immo_form_captcha'. $tag_close .'|'. $tag_open .'d2u_immo_form_validate_captcha'. $tag_close .'|'. rex_getUrl('', '', ['property_id' => $property->property_id]) .'
				html||<br>* '. $tag_open .'d2u_immo_recommendation_privacy_policy'. $tag_close .'<br><br>

				submit|submit|'. $tag_open .'d2u_immo_form_send'. $tag_close .'|no_db

				validate|empty|sender_name|'. $tag_open .'d2u_immo_recommendation_validate_sender_name'. $tag_close .'
				validate|empty|sender_mail|'. $tag_open .'d2u_immo_recommendation_validate_sender_mail'. $tag_close .'
				validate|email|sender_mail|'. $tag_open .'d2u_immo_recommendation_validate_sender_mail'. $tag_close .'
				validate|empty|receipient_name|'. $tag_open .'d2u_immo_recommendation_validate_receipient_name'. $tag_close .'
				validate|empty|receipient_mail|'. $tag_open .'d2u_immo_recommendation_validate_receipient_mail'. $tag_close .'
				validate|email|receipient_mail|'. $tag_open .'d2u_immo_recommendation_validate_receipient_mail'. $tag_close .'
				validate|empty|message|'. $tag_open .'d2u_immo_recommendation_validate_message'. $tag_close .'

				action|callback|sendRecommendation';

		$yform_recommend = new rex_yform;
		$yform_recommend->setFormData(trim($form_data));
		$yform_recommend->setObjectparams("form_action", $property->getUrl());
		$yform_recommend->setObjectparams("form_anchor", "tab_recommendation");
		$yform_recommend->setObjectparams("Error-occured", $tag_open .'d2u_immo_form_validate_title'. $tag_close);

		// action - showtext
		$yform_recommend->setActionField("showtext", array($tag_open .'d2u_immo_recommendation_thanks'. $tag_close));

		echo $yform_recommend->getForm();
		print '</fieldset>';
		print '</div>';
		print '</div>'; // END tab recommendation
		// End recommendation form
	}
	if($print == "") {
		print '</div>'; // END tab content
		print '</div>'; // END div containing tab content
	}

	print '<div class="col-12 visible-print-inline">';
	print '<p>'. $tag_open .'d2u_immo_print_foot'. $tag_close .'</p>';
	print '<p>'. $tag_open .'d2u_immo_print_foot_greetings'. $tag_close .'</p>';
	print '<p>'. $property->contact->firstname .' '. $property->contact->lastname .'</p>';
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
	print '<ul class="nav nav-pills hidden-print">';
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
?>
<script>
	// Allow activation of bootstrap tab via URL
	$(function() {
		var hash = window.location.hash;
		hash && $('ul.nav a[href="' + hash + '"]').tab('show');
	});
	
	// Activate Google map on hidden tab
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		var target_url = e.target.toString();
		var target_anchor = target_url.substr(target_url.indexOf("#")).toString();
		if(target_anchor == "#tab_map") {
			google.maps.event.trigger(map, 'resize');
			map.setCenter(myLatlng);
		}
	});

	<?php
		if($print != "") {
			print "window.print();";
		}
	?>
</script>