<?php

use TobiasKrais\D2UImmo\Contact;

$contact_id = (int) 'REX_VALUE[2]';
$contact_form_url = 'REX_LINK[id=1 output=url]';
$contact = new TobiasKrais\D2UImmo\Contact($contact_id);
$property = false;

$url_namespace = TobiasKrais\D2UHelper\FrontendHelper::getUrlNamespace();
$url_id = TobiasKrais\D2UHelper\FrontendHelper::getUrlId();

// If contact from object should be added
if (filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'property_id' === $url_namespace) {
    // Output property
    $property_id = (int) filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT);
    if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
        $property_id = $url_id;
    }
    $property = new TobiasKrais\D2UImmo\Property($property_id, rex_clang::getCurrentId());
    if ('REX_VALUE[1]' === 'Ja') { /** @phpstan-ignore-line */
        $contact = $property->contact;
    }
}

if ($contact->contact_id > 0) {
?>
<div class="col-12 col-sm-6 col-md-4 col-lg-12">
	<div class="infobox">
		<div class="infobox-header"><?= \Sprog\Wildcard::get('d2u_immo_contact_person') ?></div>
		<div class="infobox-content">
			<?php
                if ('' !== $contact->picture) {
                    echo "<img class='contactpic' src='index.php?rex_media_type=d2u_immo_contact&rex_media_file=". $contact->picture ."' alt='". $contact->firstname .' '. $contact->lastname ."' />";
                }
                echo '<br>';
                echo $contact->firstname .' '. $contact->lastname;
                echo "<span class='right'>";
                if (false !== $property) {
                    echo '<a href="#tab_request_pill" data-d2u-immo-request-tab="bs5">';
                } elseif ('' !== $contact_form_url) {
                    echo '<a href="'. $contact_form_url .'">';
                }
                echo 'E-Mail <span class="icon mail"></span>';
                echo '</a></span><br>';

                echo "Telefon: <span class='right'>". $contact->phone .'</span>';
            ?>
		</div>
	</div>
</div>
<?php
}
