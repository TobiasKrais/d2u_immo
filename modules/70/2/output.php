<?php
$contact_id = (int) 'REX_VALUE[2]';
$contact_form_url = 'REX_LINK[id=1 output=url]';
$contact = new D2U_Immo\Contact($contact_id);
$property = false;

// Get placeholder wildcard tags and other presets
$sprog = rex_addon::get('sprog');
$tag_open = $sprog->getConfig('wildcard_open_tag');
$tag_close = $sprog->getConfig('wildcard_close_tag');

$url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
$url_id = d2u_addon_frontend_helper::getUrlId();

// If contact from object should be added
if (filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'property_id' === $url_namespace) {
    // Output property
    $property_id = filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT);
    if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
        $property_id = $url_id;
    }
    $property = new D2U_Immo\Property($property_id, rex_clang::getCurrentId());
    if ('REX_VALUE[1]' == 'Ja') {
        $contact = $property->contact;
    }
}

if ($contact && $contact->contact_id > 0) {
?>
<div class="col-12 col-sm-6 col-md-4 col-lg-12">
	<div class="infobox">
		<div class="infobox-header"><?= $tag_open .'d2u_immo_contact_person'. $tag_close ?></div>
		<div class="infobox-content">
			<?php
                if ('' != $contact->picture) {
                    echo "<img class='contactpic' src='index.php?rex_media_type=d2u_immo_contact&rex_media_file=". $contact->picture ."' alt='". $contact->firstname .' '. $contact->lastname ."' />";
                }
                echo '<br>';
                echo $contact->firstname .' '. $contact->lastname;
                echo "<span class='right'>";
                if (false !== $property) {
                    echo '<a href="javascript:show_request_form()">';
                } elseif ('' != $contact_form_url) {
                    echo '<a href="'. $contact_form_url .'">';
                }
                echo 'E-Mail <span class="icon mail"></span>';
                echo '</a></span><br>';

                echo "Telefon: <span class='right'>". $contact->phone .'</span>';
            ?>
		<script>
			function show_request_form() {
				$('#tab_request_pill').tab('show');
			}
		</script>
		</div>
	</div>
</div>
<?php
}
