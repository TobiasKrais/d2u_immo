<?php
if (!function_exists('printPropertylist')) {
    /**
     * Prints property list.
     * @param array<D2U_Immo\Property> $properties Array with properties
     */
    function printPropertylist($properties): void
    {
        $sprog = rex_addon::get('sprog');
        $tag_open = $sprog->getConfig('wildcard_open_tag');
        $tag_close = $sprog->getConfig('wildcard_close_tag');
        foreach ($properties as $property) {
            echo '<div class="row">';
            echo '<div class="col-12">';

            echo '<a href="'. $property->getUrl() .'"><div class="expose">';
            echo '<div class="row">';

            echo '<div class="col-12 col-sm-4 col-lg-3">';
            if (count($property->pictures) > 0) {
                if ($property->object_reserved) {
                    echo '<div class="reserved">';
                }
                echo '<img src="index.php?rex_media_type=d2u_helper_sm&rex_media_file='.
                        $property->pictures[0] .'" alt='. $property->name .' class="listpic">';
                if ($property->object_reserved) {
                    echo '<span>'. $tag_open .'d2u_immo_object_reserved'. $tag_close .'</span>';
                    echo '</div>';
                }
            }
            echo '</div>';

            echo '<div class="col-12 col-sm-8 col-lg-9">';
            echo '<div class="row">';
            echo '<div class="col-12"><strong>'. $property->name .'</strong></div>';
            echo '<div class="col-12 col-lg-6 nolink"><b>'. $tag_open .'d2u_immo_form_city'. $tag_close .':</b> '. $property->city .'</div>';
            if ('KAUF' === $property->market_type) {
                echo '<div class="col-12 col-lg-6 nolink"><b>'. $tag_open .'d2u_immo_purchase_price'. $tag_close .':</b> '. number_format($property->purchase_price, 0, ',', '.') .',- '. $property->currency_code .'</div>';
            } elseif ('MIETE_PACHT' === $property->market_type || 'ERBPACHT' === $property->market_type) {
                echo '<div class="col-12 col-lg-6 nolink"><b>'. $tag_open .'d2u_immo_cold_rent'. $tag_close .':</b> '. number_format($property->cold_rent, 2, ',', '.') .' '. $property->currency_code .'</div>';
            } elseif ('LEASING' === $property->market_type) {
                echo '<div class="col-12 col-lg-6 nolink"><b>'. $tag_open .'d2u_immo_leasehold'. $tag_close .':</b> '. number_format($property->cold_rent, 2, ',', '.') .' '. $property->currency_code .'</div>';
            }
            if ($property->living_area > 0) {
                echo '<div class="col-12 col-lg-6 nolink"><b>'. $tag_open .'d2u_immo_living_area'. $tag_close .':</b> '. round($property->living_area) .' m²</div>';
            } elseif ($property->land_area > 0) {
                echo '<div class="col-12 col-lg-6 nolink"><b>'. $tag_open .'d2u_immo_land_area'. $tag_close .':</b> '. round($property->land_area) .' m²</div>';
            }
            echo '<div class="col-12 nolink">'. $property->teaser .'</div>';
            echo '</div>';
            echo '</div>';

            echo '</div>';
            echo '</div></a>';

            echo '</div>';
            echo '</div>';
        }
    }
}

// Get placeholder wildcard tags
$sprog = rex_addon::get('sprog');
$tag_open = $sprog->getConfig('wildcard_open_tag');
$tag_close = $sprog->getConfig('wildcard_close_tag');

// Output property list
$category_id = (int) 'REX_VALUE[1]';
$properties_leasehold = [];
$properties_leasing = [];
$properties_rent = [];
$properties_sale = [];
if ($category_id > 0) { /** @phpstan-ignore-line */
    $category = new D2U_Immo\Category($category_id, rex_clang::getCurrentId());
    $properties_leasehold = $category->getProperties('ERBPACHT', true);
    $properties_leasing = $category->getProperties('LEASING', true);
    $properties_rent = $category->getProperties('MIETE_PACHT', true);
    $properties_sale = $category->getProperties('KAUF', true);
} else {
    $properties_leasehold = D2U_Immo\Property::getAll(rex_clang::getCurrentId(), 'ERBPACHT', true);
    $properties_leasing = D2U_Immo\Property::getAll(rex_clang::getCurrentId(), 'LEASING', true);
    $properties_rent = D2U_Immo\Property::getAll(rex_clang::getCurrentId(), 'MIETE_PACHT', true);
    $properties_sale = D2U_Immo\Property::getAll(rex_clang::getCurrentId(), 'KAUF', true);
}

// Tabs
echo '<div class="col-12">';
echo '<ul class="nav nav-pills d-print-none">';
$tab_active = true;
if (count($properties_sale) > 0) {
    echo '<li class="nav-item"><a data-toggle="tab" class="nav-link active" href="#tab_sale">'. $tag_open .'d2u_immo_tab_sale'. $tag_close .'</a></li>';
    $tab_active = false;
}
if (count($properties_rent) > 0) {
    echo '<li class="nav-item"><a data-toggle="tab" class="nav-link'. ($tab_active ? ' active' : '') .'" href="#tab_rent">'. $tag_open .'d2u_immo_tab_rent'. $tag_close .'</a></li>';
    $tab_active = false;
}
if (count($properties_leasing) > 0) {
    echo '<li class="nav-item"><a data-toggle="tab" class="nav-link'. ($tab_active ? ' active' : '') .'" href="#tab_leasing">'. $tag_open .'d2u_immo_tab_leasing'. $tag_close .'</a></li>';
    $tab_active = false;
}
if (count($properties_leasehold) > 0) {
    echo '<li class="nav-item"><a data-toggle="tab" class="nav-link'. ($tab_active ? ' active' : '') .'" href="#tab_leasehold">'. $tag_open .'d2u_immo_tab_leasehold'. $tag_close .'</a></li>';
    $tab_active = false;
}
echo '</ul>';
echo '</div>';

echo '<div class="col-12">';
echo '<div class="tab-content">';
$tab_active = true;
if (count($properties_sale) > 0) {
    echo '<div id="tab_sale" class="tab-pane immo-tab fade active show">';
    printPropertylist($properties_sale);
    echo '</div>';
    $tab_active = false;
}
if (count($properties_rent) > 0) {
    echo '<div id="tab_rent" class="tab-pane immo-tab fade'. ($tab_active ? ' active show' : '') .'">';
    printPropertylist($properties_rent);
    echo '</div>';
    $tab_active = false;
}
if (count($properties_leasing) > 0) {
    echo '<div id="tab_leasing" class="tab-pane immo-tab fade'. ($tab_active ? ' active show' : '') .'">';
    printPropertylist($properties_leasing);
    echo '</div>';
    $tab_active = false;
}
if (count($properties_leasehold) > 0) {
    echo '<div id="tab_leasehold" class="tab-pane immo-tab fade'. ($tab_active ? ' active show' : '') .'">';
    printPropertylist($properties_leasehold);
    echo '</div>';
    $tab_active = false;
}
echo '</div>';
echo '</div>';
?>
<script>
	// Allow activation of bootstrap tab via URL
	$(function() {
		var hash = window.location.hash;
		hash && $('ul.nav a[href="' + hash + '"]').tab('show');
	});
</script>