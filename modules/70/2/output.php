<?php
$contact_id = "REX_VALUE[2]";
$contact_form_url = "REX_LINK[id=1 output=url]";
$contact = new Contact($contact_id);
$property = FALSE;

// Get placeholder wildcard tags and other presets
$sprog = rex_addon::get("sprog");
$tag_open = $sprog->getConfig('wildcard_open_tag');
$tag_close = $sprog->getConfig('wildcard_close_tag');

$urlParamKey = "";
if(rex_addon::get("url")->isAvailable()) {
	$url_data = UrlGenerator::getData();
	$urlParamKey = isset($url_data->urlParamKey) ? $url_data->urlParamKey : "";
}

// If contact from object should be added
if(filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || (rex_addon::get("url")->isAvailable() && $urlParamKey === "property_id")) {
	// Output property
	$property_id = filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT);
	if(rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
		$property_id = UrlGenerator::getId();
	}
	$property = new Property($property_id, rex_clang::getCurrentId());
	if("REX_VALUE[1]" == "Ja") {
		$contact = $property->contact;
	}
}
?>
<div class="col-12 col-sm-6 col-md-4 col-lg-12">
	<div class="infobox">
		<div class="infobox-header"><?php print $tag_open .'d2u_immo_contact_person'. $tag_close ?></div>
		<div class="infobox-content">
			<?php
				if($contact->picture != "") {
					print "<img class='contactpic' src='index.php?rex_media_type=d2u_immo_contact&rex_media_file=". $contact->picture ."' alt='". $contact->firstname ." ". $contact->lastname ."' />";
				}
				print "<br>";
				print $contact->firstname ." ". $contact->lastname;
				print "<span class='right'>";
				if($property !== FALSE) {
					print '<a href="'. $property->getURL(TRUE) .'#request">';
				}
				else if($contact_form_url != "") {
					print '<a href="'. $contact_form_url .'">';
				}
				print 'E-Mail <span class="icon mail"></span>';
				print "</a></span><br>";
				
				print "Telefon: <span class='right'>". $contact->phone ."</span>";
			?>
		</div>
	</div>
</div>
<br />