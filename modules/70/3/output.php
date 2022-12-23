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

// Get placeholder wildcard tags
$sprog = rex_addon::get("sprog");
$tag_open = $sprog->getConfig('wildcard_open_tag');
$tag_close = $sprog->getConfig('wildcard_close_tag');

// Output property list
$category_id = "REX_VALUE[1]";
$properties_leasehold = [];
$properties_leasing = [];
$properties_rent = [];
$properties_sale = [];
if($category_id > 0) {
	$category = new D2U_Immo\Category($category_id, true);
	$properties_leasehold = $category->getProperties("ERBPACHT", true);
	$properties_leasing = $category->getProperties("LEASING", true);
	$properties_rent = $category->getProperties("MIETE_PACHT", true);
	$properties_sale = $category->getProperties("KAUF", true);
}
else {
	$properties_leasehold = D2U_Immo\Property::getAll(Rex_clang::getCurrentId(), "ERBPACHT", true);
	$properties_leasing = D2U_Immo\Property::getAll(Rex_clang::getCurrentId(), "LEASING", true);
	$properties_rent = D2U_Immo\Property::getAll(Rex_clang::getCurrentId(), "MIETE_PACHT", true);
	$properties_sale = D2U_Immo\Property::getAll(Rex_clang::getCurrentId(), "KAUF", true);
}

// Tabs
print '<div class="col-12">';
print '<ul class="nav nav-pills d-print-none">';
$tab_active = true;
if(count($properties_sale) > 0) {
	print '<li class="nav-item"><a data-toggle="tab" class="nav-link'. ($tab_active ? ' active' : '') .'" href="#tab_sale">'. $tag_open .'d2u_immo_tab_sale'. $tag_close .'</a></li>';
	$tab_active = false;
}
if(count($properties_rent) > 0) {
	print '<li class="nav-item"><a data-toggle="tab" class="nav-link'. ($tab_active ? ' active' : '') .'" href="#tab_rent">'. $tag_open .'d2u_immo_tab_rent'. $tag_close .'</a></li>';
	$tab_active = false;
}
if(count($properties_leasing) > 0) {
	print '<li class="nav-item"><a data-toggle="tab" class="nav-link'. ($tab_active ? ' active' : '') .'" href="#tab_leasing">'. $tag_open .'d2u_immo_tab_leasing'. $tag_close .'</a></li>';
	$tab_active = false;
}
if(count($properties_leasehold) > 0) {
	print '<li class="nav-item"><a data-toggle="tab" class="nav-link'. ($tab_active ? ' active' : '') .'" href="#tab_leasehold">'. $tag_open .'d2u_immo_tab_leasehold'. $tag_close .'</a></li>';
	$tab_active = false;
}
print '</ul>';
print '</div>';

print '<div class="col-12">';
print '<div class="tab-content">';
$tab_active = true;
if(count($properties_sale) > 0) {
	print '<div id="tab_sale" class="tab-pane immo-tab fade'. ($tab_active ? ' active show' : '') .'">';
	printPropertylist($properties_sale);
	print '</div>';
	$tab_active = false;
}
if(count($properties_rent) > 0) {
	print '<div id="tab_rent" class="tab-pane immo-tab fade'. ($tab_active ? ' active show' : '') .'">';
	printPropertylist($properties_rent);
	print '</div>';
	$tab_active = false;
}
if(count($properties_leasing) > 0) {
	print '<div id="tab_leasing" class="tab-pane immo-tab fade'. ($tab_active ? ' active show' : '') .'">';
	printPropertylist($properties_leasing);
	print '</div>';
	$tab_active = false;
}
if(count($properties_leasehold) > 0) {
	print '<div id="tab_leasehold" class="tab-pane immo-tab fade'. ($tab_active ? ' active show' : '') .'">';
	printPropertylist($properties_leasehold);
	print '</div>';
	$tab_active = false;
}
print '</div>';
print '</div>';
?>
<script>
	// Allow activation of bootstrap tab via URL
	$(function() {
		var hash = window.location.hash;
		hash && $('ul.nav a[href="' + hash + '"]').tab('show');
	});
</script>