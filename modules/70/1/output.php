<?php
if (!function_exists('sendRecommendation')) {
    /**
     * Sends recommendation mail.
     * @param yform $yform YForm object with fields and values
     */
    function sendRecommendation($yform)
    {
        if (isset($yform->params['values'])) {
            $fields = [];
            foreach ($yform->params['values'] as $value) {
                if ('' != $value->name) {
                    $fields[$value->name] = $value->value;
                }
            }

            $mail = new rex_mailer();
            $mail->isHTML(false);
            $mail->CharSet = 'utf-8';
            $mail->From = $fields['immo_contact_mail'];
            $mail->FromName = $fields['immo_contact_name'];
            $mail->Sender = $fields['immo_contact_mail'];

            $mail->addAddress($fields['receipient_mail'], $fields['receipient_name']);
            $mail->addReplyTo($fields['sender_mail'], $fields['sender_name']);
            $mail->Subject = $fields['immo_name'];
            $mail_body = 'Guten Tag '. $fields['receipient_name'] .",\n\n";
            $mail_body .= $fields['sender_name'] .' empfiehlt Ihnen folgende Immobilie: "'. $fields['immo_name'] ."\"\n";
            $mail_body .= 'Link zum Objekt: '. $fields['immo_url'] ."\n\n";
            $mail_body .= $fields['sender_name'] ." hat Ihnen dazu folgende Nachricht hinterlassen:\n\n";
            $mail_body .= $fields['message'];
            $mail->Body = $mail_body;
            $mail->Send();
        }
    }
}

if (!function_exists('printPropertylist')) {
    /**
     * Prints property list.
     * @param Property $properties Array with properties
     */
    function printPropertylist($properties)
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
            if ('KAUF' == $property->market_type) {
                echo '<div class="col-12 col-lg-6 nolink"><b>'. $tag_open .'d2u_immo_purchase_price'. $tag_close .':</b> '. number_format($property->purchase_price, 0, ',', '.') .',- '. $property->currency_code .'</div>';
            } elseif ('MIETE_PACHT' == $property->market_type || 'ERBPACHT' == $property->market_type) {
                echo '<div class="col-12 col-lg-6 nolink"><b>'. $tag_open .'d2u_immo_cold_rent'. $tag_close .':</b> '. number_format($property->cold_rent, 2, ',', '.') .' '. $property->currency_code .'</div>';
            } elseif ('LEASING' == $property->market_type) {
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

if (!function_exists('printImages')) {
    /**
     * Prints images in Ekko Lightbox module format.
     * @param string[] $pics Array with images
     */
    function printImages($pics)
    {
        $type_thumb = 'd2u_helper_gallery_thumb';
        $type_detail = 'd2u_helper_gallery_detail';
        $lightbox_id = random_int(0, getrandmax());

        echo '<div class="col-12 print-border">';
        echo '<div class="row">';
        foreach ($pics as $pic) {
            $media = rex_media::get($pic);
            echo '<a href="index.php?rex_media_type='. $type_detail .'&rex_media_file='. $pic .'" data-toggle="lightbox'. $lightbox_id .'" data-gallery="example-gallery'. $lightbox_id .'" class="col-6 col-sm-4 col-lg-3"';
            if ($media instanceof rex_media) {
                echo ' data-title="'. $media->getValue('title') .'"';
            }
            echo '>';
            echo '<img src="index.php?rex_media_type='. $type_thumb .'&rex_media_file='. $pic .'" class="img-fluid gallery-pic-box"';
            if ($media instanceof rex_media) {
                echo ' alt="'. $media->getValue('title') .'" title="'. $media->getValue('title') .'"';
            }
            echo '>';
            echo '</a>';
        }
        echo '</div>';
        echo '</div>';
        echo '<script>';
        echo "$(document).on('click', '[data-toggle=\"lightbox". $lightbox_id ."\"]', function(event) {";
        echo 'event.preventDefault();';
        echo '$(this).ekkoLightbox({ alwaysShowClose: true	});';
        echo '});';
        echo '</script>';
    }
}

// Get placeholder wildcard tags and other presets
$print = filter_input(INPUT_GET, 'print', FILTER_SANITIZE_SPECIAL_CHARS);
$sprog = rex_addon::get('sprog');
$tag_open = $sprog->getConfig('wildcard_open_tag');
$tag_close = $sprog->getConfig('wildcard_close_tag');
$d2u_immo = rex_addon::get('d2u_immo');
$map_type = 'REX_VALUE[1]' == '' ? 'google' : 'REX_VALUE[1]'; // Backward compatibility
$map_id = 'd2u' . md5((string) time());

$url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
$url_id = d2u_addon_frontend_helper::getUrlId();
?>

<div id="d2u_immo_module_70_1" class="col-12">
    <div class="row">

<?php
if (filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'property_id' === $url_namespace) {
    // Output property
    $property_id = filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT);
    if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
        $property_id = $url_id;
    }
    $property = new D2U_Immo\Property($property_id, rex_clang::getCurrentId());
    // Redirect if object is not online
    if ('online' != $property->online_status) {
        rex_redirect(rex_article::getNotfoundArticleId(), rex_clang::getCurrentId());
    }

    if (null === $print) {
        echo '<div class="col-12 expose-navi d-print-none">';
        echo '<ul>';
        echo '<li><small><a href="'. rex_getUrl($d2u_immo->getConfig('article_id')) .'"><span class="icon back"></span> '. $tag_open .'d2u_immo_back_to_list'. $tag_close .'</a></small></li>';
        //	Following links see Chrome print bug: https://github.com/twbs/bootstrap/issues/22753
        echo '<li><small><a href="'. $property->getUrl(true).'?print=small" target="blank"><span class="icon print"></span> '. $tag_open .'d2u_immo_print_short_expose'. $tag_close .'</a></small></li>';
        echo '<li><small><a href="'. $property->getUrl(true).'?print=full" target="blank"><span class="icon print"></span> '. $tag_open .'d2u_immo_print_expose'. $tag_close .'</a></small></li>';
        if ('MIETE_PACHT' == $property->market_type && 'GEWERBE' != $property->type_of_use && '' != $d2u_immo->getConfig('even_informative_pdf', '')) {
            echo '<li><small><a href="'. rex_url::media('mieterselbstauskunft.pdf') .'"><span class="icon pdf"></span> '. $tag_open .'d2u_immo_tentant_information'. $tag_close .'</a></small></li>';
        }
        echo '</ul>';
        echo '</div>';

        echo '<div class="col-12 d-none d-print-inline">';
        echo '<p>'. $property->contact->firstname .' '. $property->contact->lastname .'<br>';
        echo $tag_open .'d2u_immo_form_phone'. $tag_close .': '. $property->contact->phone .'<br>';
        echo $tag_open .'d2u_immo_form_email'. $tag_close .': '. $property->contact->email .'<p>';
        echo '</div>';
    }

    // Tabs
    if (null === $print) {
        echo '<div class="col-12 d-print-none">';
        echo '<ul class="nav nav-pills" id="expose_tabs">';
        echo '<li class="nav-item"><a data-toggle="tab" href="#tab_overview" class="nav-link active"><span class="icon home d-md-none"></span><span class="d-none d-md-block">'. $tag_open .'d2u_immo_tab_overview'. $tag_close .'</span></a></li>';
        if (count($property->pictures) > 0 || count($property->pictures_360) > 0) {
            echo '<li class="nav-item"><a data-toggle="tab" class="nav-link" href="#tab_pictures"><span class="icon pic d-md-none"></span><span class="d-none d-md-block">'. $tag_open .'d2u_immo_tab_pictures'. $tag_close .'</span></a></li>';
        }
        if ($property->publish_address) {
            echo '<li class="nav-item"><a data-toggle="tab" class="nav-link" href="#tab_map"><span class="icon map d-md-none"></span><span class="d-none d-md-block">'. $tag_open .'d2u_immo_tab_map'. $tag_close .'</span></a></li>';
        }
        if ('KAUF' === $property->market_type) {
            echo '<li class="nav-item"><a data-toggle="tab" class="nav-link" href="#tab_calculator"><span class="icon money d-md-none"></span><span class="d-none d-md-block">'. $tag_open .'d2u_immo_tab_calculator'. $tag_close .'</span></a></li>';
        }
        echo '<li class="nav-item"><a data-toggle="tab" class="nav-link" href="#tab_request" id="tab_request_pill"><span class="icon request d-md-none"></span><span class="d-none d-md-block">'. $tag_open .'d2u_immo_tab_request'. $tag_close .'</span></a></li>';
        echo '<li class="nav-item"><a data-toggle="tab" class="nav-link" href="#tab_recommendation"><span class="icon forward d-md-none"></span><span class="d-none d-md-block">'. $tag_open .'d2u_immo_tab_recommendation'. $tag_close .'<span></a></li>';
        echo '</ul>';
        echo '</div>';
    }

    if (null === $print) {
        echo '<div class="col-12">'; // START div containing tab content
        echo '<div class="tab-content">'; // START tab content
        echo '<div id="tab_overview" class="tab-pane fade active show immo-tab">'; // START tab overview
    }

    // Overview
    echo '<div class="row page-break-avoid">'; // START row overview

    echo '<div class="col-12 print-border-h">';
    echo '<h1>'. $property->name .'</h1>';
    echo '</div>';
    if ($property->publish_address) {
        echo '<div class="col-12 print-border d-none d-print-inline">';
        echo '<p>'. $property->street .' '. $property->house_number .', '. $property->zip_code .' '. $property->city .'</p>';
        echo '</div>';
    }
    echo '<div class="col-12 print-border">'; // START overview picture and short info
    echo '<div class="row">';

    if (count($property->pictures) > 0) {
        echo '<div class="col-12 col-md-6">'; // START overview picture
        if ($property->object_reserved || $property->object_sold) {
            echo '<div class="reserved">';
        }
        echo '<img src="index.php?rex_media_type=d2u_helper_sm&rex_media_file='.
                $property->pictures[0] .'" alt="'. $property->name .'" class="overviewpic">';
        if ($property->object_reserved) {
            echo '<span class="d-print-none">'. $tag_open .'d2u_immo_object_reserved'. $tag_close .'</span>';
        } elseif ($property->object_sold) {
            echo '<span class="d-print-none">'. $tag_open .'d2u_immo_object_sold'. $tag_close .'</span>';
        }
        if ($property->object_reserved || $property->object_sold) {
            echo '</div>'; // <div class="reserved">
        }
        echo '</div>'; // END overview picture
        echo '<div class="col-12 col-md-6">'; // START short info
    } else {
        echo '<div class="col-12">'; // START short info
    }
    echo '<div class="row page-break-avoid">';

    if ('KAUF' === strtoupper($property->market_type)) {
        if ($property->purchase_price > 0) {
            echo '<div class="col-6">'. $tag_open .'d2u_immo_purchase_price'. $tag_close .':</div>';
            echo '<div class="col-6"><b>'. number_format($property->purchase_price, 0, ',', '.') .',-&nbsp;'. $property->currency_code .'</b></div>';
        }
        if ($property->purchase_price_m2 > 0) {
            echo '<div class="col-6">'. $tag_open .'d2u_immo_purchase_price_m2'. $tag_close .':</div>';
            echo '<div class="col-6">'. number_format($property->purchase_price_m2, 2, ',', '.') .'&nbsp;'. $property->currency_code .'</div>';
        }
        if ($property->price_plus_vat) {
            echo '<div class="col-12">'. $tag_open .'d2u_immo_prices_plus_vat'. $tag_close .'</div>';
            echo '<div class="col-12">&nbsp;</div>';
        }
    } else {
        if ($property->cold_rent > 0 && $property->additional_costs > 0) {
            echo '<div class="col-6">'. $tag_open .'d2u_immo_warm_rent'. $tag_close .':</div>';
            echo '<div class="col-6"><b>'. number_format($property->cold_rent + $property->additional_costs, 2, ',', '.') .'&nbsp;'. $property->currency_code .'</b></div>';
        }
        if ($property->cold_rent > 0) {
            echo '<div class="col-6">'. $tag_open .'d2u_immo_cold_rent'. $tag_close .':</div>';
            echo '<div class="col-6">'. number_format($property->cold_rent, 2, ',', '.') .'&nbsp;'. $property->currency_code .'</div>';
        }
        if ($property->additional_costs > 0) {
            echo '<div class="col-6">'. $tag_open .'d2u_immo_additional_costs'. $tag_close .':</div>';
            echo '<div class="col-6">'. number_format($property->additional_costs, 2, ',', '.') .'&nbsp;'. $property->currency_code .'</div>';
        }
        if ($property->price_plus_vat) {
            echo '<div class="col-12">'. $tag_open .'d2u_immo_prices_plus_vat'. $tag_close .'</div>';
            echo '<div class="col-12">&nbsp;</div>';
        }
        if ('' != $property->deposit) {
            echo '<div class="col-6">'. $tag_open .'d2u_immo_deposit'. $tag_close .':</div>';
            echo '<div class="col-6">'. number_format($property->deposit, 2, ',', '.') .'&nbsp;'. $property->currency_code .'</div>';
        }
    }

    if ('HAUS' === strtoupper($property->object_type) || 'WOHNUNG' === strtoupper($property->object_type) || 'BUERO_PRAXEN' === strtoupper($property->object_type)) {
        if ($property->living_area > 0) {
            if ('HAUS' === strtoupper($property->object_type) || 'WOHNUNG' === strtoupper($property->object_type)) {
                echo '<div class="col-6">'. $tag_open .'d2u_immo_living_area'. $tag_close .':</div>';
            } elseif ('BUERO_PRAXEN' === strtoupper($property->object_type)) {
                echo '<div class="col-6">'. $tag_open .'d2u_immo_office_area'. $tag_close .':</div>';
            }
            echo '<div class="col-6">'. number_format($property->living_area, 2, ',', '.') .'&nbsp;m²</div>';
        }

        if ($property->rooms > 0) {
            echo '<div class="col-6">'. $tag_open .'d2u_immo_rooms'. $tag_close .':</div>';
            echo '<div class="col-6">'. $property->rooms .'</div>';
        }

        if ($property->construction_year > 0) {
            echo '<div class="col-6">'. $tag_open .'d2u_immo_construction_year'. $tag_close .':</div>';
            echo '<div class="col-6">'. $property->construction_year .'</div>';
        }

        if ($property->floor > 0) {
            echo '<div class="col-6">'. $tag_open .'d2u_immo_floor'. $tag_close .':</div>';
            echo '<div class="col-6">'. $property->floor .'</div>';
        }

        if ($property->flat_sharing_possible) {
            echo '<div class="col-6">'. $tag_open .'d2u_immo_flat_sharing'. $tag_close .':</div>';
            echo '<div class="col-6">'. $tag_open .'d2u_immo_yes'. $tag_close .'</div>';
        }

        if ('' != $property->condition_type) {
            echo '<div class="col-6">'. $tag_open .'d2u_immo_condition'. $tag_close .':</div>';
            echo '<div class="col-6">'. $tag_open .'d2u_immo_condition_'. $property->condition_type . $tag_close .'</div>';
        }

        if ('' != $property->available_from) {
            echo '<div class="col-6">'. $tag_open .'d2u_immo_available_from'. $tag_close .':</div>';
            echo '<div class="col-6">'. date_format(date_create_from_format('Y-m-d', $property->available_from), 'd.m.Y') .'</div>';
        }

        if ($property->animals) {
            echo '<div class="col-6">'. $tag_open .'d2u_immo_animals'. $tag_close .':</div>';
            echo '<div class="col-6">'. $tag_open .'d2u_immo_yes'. $tag_close .'</div>';
        }

        if ($property->rented) {
            echo '<div class="col-6">'. $tag_open .'d2u_immo_rented'. $tag_close .':</div>';
            echo '<div class="col-6">'. $tag_open .'d2u_immo_yes'. $tag_close .'</div>';
        }

        if ($property->parking_space_duplex > 0) {
            echo '<div class="col-6">'. $tag_open .'d2u_immo_parking_space_duplex'. $tag_close .':</div>';
            echo '<div class="col-6">'. $property->parking_space_duplex .'</div>';
        }

        if ($property->parking_space_simple > 0) {
            echo '<div class="col-6">'. $tag_open .'d2u_immo_parking_space_simple'. $tag_close .':</div>';
            echo '<div class="col-6">'. $property->parking_space_simple .'</div>';
        }

        if ($property->parking_space_garage > 0) {
            echo '<div class="col-6">'. $tag_open .'d2u_immo_parking_space_garage'. $tag_close .':</div>';
            echo '<div class="col-6">'. $property->parking_space_garage .'</div>';
        }

        if ($property->parking_space_undergroundcarpark > 0) {
            echo '<div class="col-6">'. $tag_open .'d2u_immo_parking_space_undergroundcarpark'. $tag_close .':</div>';
            echo '<div class="col-6">'. $property->parking_space_undergroundcarpark .'</div>';
        }
    }

    if ($property->total_area > 0) {
        echo '<div class="col-6">'. $tag_open .'d2u_immo_total_area'. $tag_close .':</div>';
        echo '<div class="col-6">'. $property->total_area .'&nbsp;m²</div>';
    }
    if ($property->land_area > 0) {
        echo '<div class="col-6">'. $tag_open .'d2u_immo_land_area'. $tag_close .':</div>';
        echo '<div class="col-6">'. round($property->land_area) .'&nbsp;m²</div>';
    }

    if (count($property->documents) > 0 || ('MIETE_PACHT' == $property->market_type && 'GEWERBE' != $property->type_of_use && '' != $d2u_immo->getConfig('even_informative_pdf', ''))) {
        echo '<div class="col-12"><ul>';
        foreach ($property->documents as $document) {
            $media = rex_media::get($document);
            if ($media instanceof rex_media) {
                // Check permissions
                $has_permission = true;
                if (rex_plugin::get('ycom', 'media_auth')->isAvailable()) {
                    $has_permission = rex_ycom_media_auth::checkPerm(rex_media_manager::create('', $document));
                }
                if ($has_permission) {
                    echo '<li><span class="icon pdf"></span> <a href="'. rex_url::media($document) .'">'. ('' != $media->getTitle() ? $media->getTitle() : $document) .'</a></li>';
                }
            }
        }
        if ('MIETE_PACHT' == $property->market_type && 'GEWERBE' != $property->type_of_use && '' != $d2u_immo->getConfig('even_informative_pdf', '')) {
            echo '<li class="d-print-none"><span class="icon pdf"></span> <a href="'. rex_url::media('mieterselbstauskunft.pdf') .'">'. $tag_open .'d2u_immo_tentant_information'. $tag_close .'</a></li>';
        }
        echo '</ul></div>';
    }

    echo '</div>';
    echo '</div>'; // END short info
    echo '</div>';
    echo '</div>'; // END overview picture and short info
    if (null !== $print) { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
        echo '</div>';
    }

    echo '<div class="col-12">&nbsp;</div>';

    if ('grundstueck' != strtolower($property->object_type)
            && 'parken' != strtolower($property->object_type)
            && 'projektiert' != strtolower($property->condition_type)
            && strlen($property->energy_pass) > 5) {
        if (null !== $print) { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
            echo '<div class="row page-break-avoid">';
        }
        echo '<div class="col-12 print-border-h"><h2>'. $tag_open .'d2u_immo_energy_pass'. $tag_close .'</h2></div>';

        echo '<div class="col-12 print-border">'; // START energy pass
        echo '<div class="row">';

        echo '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. $tag_open .'d2u_immo_energy_pass_type'. $tag_close .':</li></ul></div>';
        echo '<div class="col-6 col-md-8 col-lg-9">'. $tag_open .'d2u_immo_energy_pass_'. $property->energy_pass . $tag_close .'</div>';

        if ('' != $property->energy_pass_valid_until) {
            echo '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. $tag_open .'d2u_immo_energy_pass_valid_until'. $tag_close .':</li></ul></div>';
            echo '<div class="col-6 col-md-8 col-lg-9">'. date_format(date_create_from_format('Y-m-d', $property->energy_pass_valid_until), 'd.m.Y') .'</div>';
        }

        echo '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. $tag_open .'d2u_immo_energy_pass_value'. $tag_close .':</li></ul></div>';
        echo '<div class="col-6 col-md-8 col-lg-9">'. $property->energy_consumption .'&nbsp;kWh/(m²*a)</div>';

        if ($property->including_warm_water) {
            echo '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. $tag_open .'d2u_immo_energy_pass_incl_warm_water'. $tag_close .':</li></ul></div>';
            echo '<div class="col-6 col-md-8 col-lg-9">'. $tag_open .'d2u_immo_yes'. $tag_close .'</div>';
        }

        if ($property->construction_year > 0) {
            echo '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. $tag_open .'d2u_immo_construction_year'. $tag_close .':</li></ul></div>';
            echo '<div class="col-6 col-md-8 col-lg-9">'. $property->construction_year .'</div>';
        }

        if ($property->firing_type > 0) {
            echo '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. $tag_open .'d2u_immo_firing_type'. $tag_close .':</li></ul></div>';
            echo '<div class="col-6 col-md-8 col-lg-9">';
            $first_element = true;
            foreach ($property->firing_type as $firing_type) {
                echo($first_element ? '' : ', ') . $tag_open .'d2u_immo_firing_type_'. $firing_type . $tag_close;
                $first_element = false;
            }
            echo '</div>';
        }

        echo '<div class="col-12">';
        echo "<div class='energy-scale-container'>";
        echo "<div style='position: absolute;'>";
        echo "<img src='". $d2u_immo->getAssetsUrl('energieskala.png') ."' class='energy_scale'>";
        echo '</div>';
        echo "<div style='position: absolute; margin-left: ". round($property->energy_consumption - 10, 0) ."px !important;'>";
        echo "<img src='". $d2u_immo->getAssetsUrl('zeiger.png') ."'>";
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';  // END energy pass
        if (null !== $print) { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
            echo '</div>';
        }
        echo '<div class="col-12">&nbsp;</div>';
    }

    if (('HAUS' === strtoupper($property->object_type) || 'WOHNUNG' === strtoupper($property->object_type) || 'BUERO_PRAXEN' === strtoupper($property->object_type))
        && (count($property->bath) > 0 || count($property->kitchen) > 0 || count($property->floor_type) > 0 || count($property->elevator) > 0 || $property->cable_sat_tv || count($property->broadband_internet) > 0)) {
        if (null !== $print) { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
            echo '<div class="row page-break-avoid">';
        }
        echo '<div class="col-12 print-border-h"><h2>'. $tag_open .'d2u_immo_equipment'. $tag_close .'</h2></div>';

        echo '<div class="col-12 print-border">'; // START detail facts
        echo '<div class="row page-break-avoid">';
        if (count($property->bath) > 0) {
            echo '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. $tag_open .'d2u_immo_bath'. $tag_close .':</li></ul></div>';
            echo '<div class="col-6 col-md-8 col-lg-9">';
            $first_element = true;
            foreach ($property->bath as $bath) {
                echo($first_element ? '' : ', ') . $tag_open .'d2u_immo_bath_'. $bath . $tag_close;
                $first_element = false;
            }
            echo '</div>';
        }

        if (count($property->kitchen) > 0) {
            echo '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. $tag_open .'d2u_immo_kitchen'. $tag_close .':</li></ul></div>';
            echo '<div class="col-6 col-md-8 col-lg-9">';
            $first_element = true;
            foreach ($property->kitchen as $kitchen) {
                echo($first_element ? '' : ', ') . $tag_open .'d2u_immo_kitchen_'. $kitchen . $tag_close;
                $first_element = false;
            }
            echo '</div>';
        }

        if (count($property->floor_type) > 0) {
            echo '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. $tag_open .'d2u_immo_floor_type'. $tag_close .':</li></ul></div>';
            echo '<div class="col-6 col-md-8 col-lg-9">';
            $first_element = true;
            foreach ($property->floor_type as $floor_type) {
                echo($first_element ? '' : ', ') . $tag_open .'d2u_immo_floor_type_'. $floor_type . $tag_close;
                $first_element = false;
            }
            echo '</div>';
        }

        if (count($property->elevator) > 0) {
            echo '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. $tag_open .'d2u_immo_elevator'. $tag_close .':</li></ul></div>';
            echo '<div class="col-6 col-md-8 col-lg-9">';
            $first_element = true;
            foreach ($property->elevator as $elevator) {
                echo($first_element ? '' : ', ') . $tag_open .'d2u_immo_elevator_'. $elevator . $tag_close;
                $first_element = false;
            }
            echo '</div>';
        }

        if ($property->cable_sat_tv) {
            echo '<div class="col-12">'. $tag_open .'d2u_immo_cable_sat_tv'. $tag_close .'</div>';
        }

        if (count($property->broadband_internet) > 0) {
            echo '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. $tag_open .'d2u_immo_broadband_internet'. $tag_close .':</li></ul></div>';
            echo '<div class="col-6 col-md-8 col-lg-9">'. implode(', ', $property->broadband_internet) .'</div>';
        }
        echo '</div>';
        echo '</div>';  // END detail facts

        echo '<div class="col-12">&nbsp;</div>';
        if (null !== $print) { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
            echo '</div>';
        }
    }

    if ('' != $property->description) {
        if (null !== $print) { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
            echo '<div class="row page-break-avoid">';
        }
        echo '<div class="col-12 print-border-h"><h2>'. $tag_open .'d2u_immo_description'. $tag_close .'</h2></div>';
        echo '<div class="col-12 print-border">'. d2u_addon_frontend_helper::prepareEditorField($property->description) .'</div>';
        echo '<div class="col-12">&nbsp;</div>';
        if (null !== $print) { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
            echo '</div>';
        }
    }

    if ('' != $property->description_location) {
        if (null !== $print) { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
            echo '<div class="row page-break-avoid">';
        }
        echo '<div class="col-12 print-border-h"><h2>'. $tag_open .'d2u_immo_description_location'. $tag_close .'</h2></div>';
        echo '<div class="col-12 print-border">'. d2u_addon_frontend_helper::prepareEditorField($property->description_location) .'</div>';
        echo '<div class="col-12">&nbsp;</div>';
        if (null !== $print) { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
            echo '</div>';
        }
    }

    if ('' != $property->description_equipment) {
        if (null !== $print) { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
            echo '<div class="row page-break-avoid">';
        }
        echo '<div class="col-12 print-border-h"><h2>'. $tag_open .'d2u_immo_description_equipment'. $tag_close .'</h2></div>';
        echo '<div class="col-12 print-border">'. d2u_addon_frontend_helper::prepareEditorField($property->description_equipment) .'</div>';
        echo '<div class="col-12">&nbsp;</div>';
        if (null !== $print) { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
            echo '</div>';
        }
    }

    if ('' != $property->description_others) {
        if (null !== $print) { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
            echo '<div class="row page-break-avoid">';
        }
        echo '<div class="col-12 print-border-h"><h2>'. $tag_open .'d2u_immo_description_others'. $tag_close .'</h2></div>';
        echo '<div class="col-12 print-border">'. d2u_addon_frontend_helper::prepareEditorField($property->description_others) .'</div>';
        echo '<div class="col-12">&nbsp;</div>';
        if (null !== $print) { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
            echo '</div>';
        }
    }

    if (null !== $print) { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
        echo '<div class="row page-break-avoid">';
    }
    echo '<div class="col-12 print-border-h"><h2>'. $tag_open .'d2u_immo_courtage'. $tag_close .'</h2></div>';
    if ('' == $property->courtage) {
        echo '<div class="col-12 print-border">'. $tag_open .'d2u_immo_courtage_no'. $tag_close .'</div>';
    } else {
        echo '<div class="col-12 print-border">'. $property->courtage .' '. $tag_open .'d2u_immo_courtage_incl_vat'. $tag_close .'</div>';
    }
    echo '<div class="col-12 d-none d-print-inline">&nbsp;</div>';

    echo '</div>'; // END row overview
    if (null === $print) {
        echo '</div>'; // END tab overview
    }
    // End Overview
    // Pictures
    if (count($property->pictures) > 0 && 'small' != $print) {
        if ('full' != $print) {
            echo '<div id="tab_pictures" class="tab-pane immo-tab fade">'; // START tab picures
        }
        echo '<div class="row">'; // START pictures
        echo '<div class="col-12 d-none d-print-inline print-border-h">';
        echo '<h2>'. $tag_open .'d2u_immo_tab_pictures'. $tag_close .'</h2>';
        echo '</div>';
        echo '<div class="col-12 d-none d-print-none">';
        echo '<h2>'. $property->name .'</h2>';
        echo '</div>';
        echo printImages($property->pictures);

        // 360° pictures
        $viewer_id = 0;
        if (count($property->pictures_360) > 0) {
            if (!function_exists('includePhotoSphereViewerJS')) {
                /**
                 * Echo Photo Sphere Viewer JS files.
                 */
                function includePhotoSphereViewerJS(): void
                {
                    $three_js = 'modules/03-3/three.min.js';
                    echo '<script src="'. rex_url::addonAssets('d2u_helper', $three_js) .'?buster='. filemtime(rex_path::addonAssets('d2u_helper', $three_js)) .'"></script>' . PHP_EOL;
                    $photosphereviewer_js = 'modules/03-3/photosphereviewer.min.js';
                    echo '<script src="'. rex_url::addonAssets('d2u_helper', $photosphereviewer_js) .'?buster='. filemtime(rex_path::addonAssets('d2u_helper', $photosphereviewer_js)) .'"></script>' . PHP_EOL;

                    $photosphereviewer_css = 'modules/03/3/style.css';
                    if (file_exists(rex_path::addon('d2u_helper', $photosphereviewer_css))) {
                        echo '<style>'. file_get_contents(rex_path::addon('d2u_helper', $photosphereviewer_css)) .'</style>';
                    }
                }

                includePhotoSphereViewerJS();
            }
            foreach ($property->pictures_360 as $picture_360) {
                echo '<div class="col-12 col-md-6 mb-4 d-print-none">';
                echo '<div id="viewer_'. $viewer_id .'" style="height: 400px;"></div>';
                echo '<script>'. PHP_EOL;
                echo 'const viewer_'. $viewer_id .' = new PhotoSphereViewer.Viewer({'. PHP_EOL;
                echo 'container: document.querySelector("#viewer_'. $viewer_id .'"),'. PHP_EOL;
                echo 'panorama: "'. rex_url::media($picture_360) .'",'. PHP_EOL;
                echo 'navbar: ["zoom", "move", "fullscreen"]'. PHP_EOL;
                echo '});'. PHP_EOL;
                echo '</script>';
                echo '</div>';
                $viewer_id++;
            }
        }
        echo '</div>'; // END pictures

        if (count($property->ground_plans) > 0) {
            echo '<div class="row">';
            echo '<div class="col-12 print-border-h">';
            echo '<h2>'. $tag_open .'d2u_immo_ground_plans'. $tag_close .'</h2>';
            echo '</div>';
            echo printImages($property->ground_plans);
            echo '<div class="col-12 d-none d-print-inline">&nbsp;</div>';
            echo '</div>';
        }

        if (count($property->location_plans) > 0) {
            echo '<div class="row">';
            echo '<div class="col-12 print-border-h">';
            echo '<h2>'. $tag_open .'d2u_immo_location_plans'. $tag_close .'</h2>';
            echo '</div>';
            echo printImages($property->location_plans);
            echo '<div class="col-12 d-none d-print-inline">&nbsp;</div>';
            echo '</div>';
        }
        if ('full' != $print) {
            echo '</div>'; // END tab picures
        }
    }
    // End Pictures
    // Map
    if ($property->publish_address && 'small' != $print) {
        $d2u_helper = rex_addon::get('d2u_helper');
        $api_key = '';
        if ($d2u_helper->hasConfig('maps_key')) {
            $api_key = $d2u_helper->getConfig('maps_key');
        }
        if ('full' != $print) {
            echo '<div id="tab_map" class="tab-pane immo-tab fade page-break-avoid">'; // START tab map
        }
        echo '<div class="row page-break-avoid">';
        echo '<div class="col-12 d-none d-print-inline print-border-h">';
        echo '<h2>'. $tag_open .'d2u_immo_tab_map'. $tag_close .'</h2>';
        echo '</div>';
        echo '<div class="col-12 print-border">';
        echo '<h2 class="d-print-none">'. $property->name .'</h2>';
        echo '<p class="d-print-none">'. $property->street .' '. $property->house_number .'<br /> '. $property->zip_code .' '. $property->city .'</p>';

        if ('google' == $map_type) {
?>
		<script src="https://maps.googleapis.com/maps/api/js?key=<?= $api_key ?>"></script>
		<div id="map_canvas" style="display: block; <?= '' != $print ? 'width: 900px' : 'width: 100%' ?>; height: 500px"></div>
		<script>
			var map;
			var myLatlng;
			<?php
                // if longitude and latitude are available
                if (0 != $property->longitude && 0 != $property->latitude) {
            ?>
				var myLatlng = new google.maps.LatLng(<?= $property->latitude .', '. $property->longitude ?>);
				var myOptions = {
					zoom: 15,
					center: myLatlng,
					mapTypeId: google.maps.MapTypeId.<?= 'full' == $print ? 'ROADMAP' : 'HYBRID' ?>
				};
				map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

				var marker = new google.maps.Marker({
					position: myLatlng,
					map: map
				});

				google.maps.event.addListener(marker, 'click', function() {
					infowindow.open(map,marker);
				});
			<?php
            } else {
            ?>
				var geocoder = new google.maps.Geocoder();
				var address = "<?= $property->street .' '. $property->house_number .', '. $property->zip_code .' '. $property->city ?>";
				if (geocoder) {
					geocoder.geocode( { 'address': address}, function(results, status) {
						if (status === google.maps.GeocoderStatus.OK) {
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
					mapTypeId: google.maps.MapTypeId.<?= 'full' == $print ? 'ROADMAP' : 'HYBRID' ?>
				};
				map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
			<?php
                }
            ?>
		</script>
		<?php
        } elseif ('osm' == $map_type && rex_addon::get('osmproxy')->isAvailable()) {
            $map_id = random_int(0, getrandmax());

            $leaflet_js_file = 'modules/04-2/leaflet.js';
            echo '<script src="'. rex_url::addonAssets('d2u_helper', $leaflet_js_file) .'?buster='. filemtime(rex_path::addonAssets('d2u_helper', $leaflet_js_file)) .'"></script>' . PHP_EOL;
        ?>
		<div id="map-<?= $map_id ?>" style="width:100%; height: 500px"></div>
		<script type="text/javascript" async="async">
			<?= "var map = L.map('map-". $map_id ."').setView([". $property->latitude .', '. $property->longitude .'], 15);';
            ?>
			L.tileLayer('/?osmtype=german&z={z}&x={x}&y={y}', {
				attribution: 'Map data &copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors'
			}).addTo(map);
			map.scrollWheelZoom.disable();
			var myIcon = L.icon({
				iconUrl: '<?= rex_url::addonAssets('d2u_helper', 'modules/04-2/marker-icon.png') ?>',
				shadowUrl: '<?= rex_url::addonAssets('d2u_helper', 'modules/04-2/marker-shadow.png') ?>',

				iconSize:     [25, 41], // size of the icon
				shadowSize:   [41, 41], // size of the shadow
				iconAnchor:   [12, 40], // point of the icon which will correspond to marker's location
				shadowAnchor: [13, 40], // the same for the shadow
				popupAnchor:  [0, -41]  // point from which the popup should open relative to the iconAnchor
			});
			var marker = L.marker([<?= $property->latitude ?>, <?= $property->longitude ?>], {
				draggable: false,
				icon: myIcon
			}).addTo(map);
		</script>
		<?php
        } elseif (rex_addon::get('geolocation')->isAvailable()) {
            try {
                if (rex::isFrontend()) {
                    \Geolocation\tools::echoAssetTags();
                }
?>
<script>
	Geolocation.default.positionColor = '<?= rex_config::get('d2u_helper', 'article_color_h') ?>';

	// adjust zoom level
	Geolocation.Tools.Center = class extends Geolocation.Tools.Template{
		constructor ( ...args){
			super(args);
			this.zoom = this.zoomDefault = Geolocation.default.zoom;
			this.center = this.centerDefault = L.latLngBounds( Geolocation.default.bounds ).getCenter();
			return this;
		}
		setValue( data ){
			super.setValue( data );
			this.center = L.latLng( data[0] ) || this.centerDefault;
			this.zoom = data[1] || this.zoomDefault;
			this.radius = data[2];
			this.circle = null;
			if( data[2] ) {
				let options = Geolocation.default.styleCenter;
				options.color = data[3] || options.color;
				options.radius = this.radius;
				this.circle = L.circle( this.center, options );
			}
			if( this.map ) this.show( this.map );
			return this;
		}
		show( map ){
			super.show( map );
			map.setView( this.center, this.zoom );
			if( this.circle instanceof L.Circle ) this.circle.addTo( map );
			return this;
		}
		remove(){
			if( this.circle instanceof L.Circle ) this.circle.remove();
			super.remove();
			return this;
		}
		getCurrentBounds(){
			if( this.circle instanceof L.Circle ) {
				return this.radius ? this.circle.getBounds() : this.circle.getLatLng();
			}
			return this.center;
		}
	};
	Geolocation.tools.center = function(...args) { return new Geolocation.Tools.Center(args); };
</script>
<?php
            } catch (Exception $e) {
            }

            echo \Geolocation\mapset::take((int) $map_type)
                ->attributes('id', $map_id)
                ->dataset('position', [$property->latitude, $property->longitude])
                ->dataset('center', [[$property->latitude, $property->longitude], 15])
                ->parse();
        }

        echo '</div>';
        echo '</div>';
        if ('full' != $print) {
            echo '</div>';  // END tab map
        } else {
            echo '<div class="col-12 d-none d-print-inline">&nbsp;</div>';
        }
    }
    // End Map
    // Calculator
    if ('KAUF' === $property->market_type && null === $print) {
        echo '<div id="tab_calculator" class="tab-pane immo-tab fade">'; // START tab calculator
        echo '<div class="row">';
        echo '<div class="col-12">';
        $real_estate_tax = $d2u_immo->getConfig('finance_calculator_real_estate_tax');
        $notary_costs = $d2u_immo->getConfig('finance_calculator_notary_costs');
        $courtage = is_numeric($property->courtage) ? (strtr(strtr($property->courtage, ',', '.') * 1, ',', '.') / 100) : 0;
        $interest_rate = $d2u_immo->getConfig('finance_calculator_interest_rate');
        $repayment = $d2u_immo->getConfig('finance_calculator_repayment');

        echo '<h2>'. $property->name .'</h2>';
?>
		<script>
			// Removes thousand separator, substutiutes decimal separator
			function substractNumber(number_string) {
				number_string = number_string.trim();
				number_string = number_string.replace(".", ""); // Thousand separator
				number_string = number_string.replace(".", ""); // Million separator
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
				zahl += (idx === -1 ? '.' : '' ) + f.toString().substring(1);

				var sign = zahl < 0;
				if(sign) zahl = zahl.substring(1);
				idx = zahl.indexOf('.');

				// decimal place
				if( idx === -1) {
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
				var grundsteuer = <?= $real_estate_tax ?>;
				var notarkosten = <?= $notary_costs ?>;

				// Neue Werte berechnen
				var gesamtkosten = (kaufpreis * (provision + notarkosten + grundsteuer + 1));

				gesamtkosten += (sonstiges * 1);

				var darlehen = gesamtkosten - eigenkapital;

				if(darlehen < 0)
					darlehen = 0;
				if(isNaN(darlehen)) {
					alert("Bitte geben Sie nur Zahlen, Punkt oder Komma ein.");
				}
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
				<legend><?= $tag_open .'d2u_immo_finance_calc_investement'. $tag_close ?></legend>
				<table style="width: 100%;">
					<tr>
						<td style="width: 45%; height: 30px; text-align: left">
							<strong><label for="kaufpreis"><?= $tag_open .'d2u_immo_purchase_price'. $tag_close ?></label></strong>
						</td>
						<td style="width: 20%"></td>
						<td style="width: 5%"></td>
						<td style="width: 25%; text-align: right">
							<input name="kaufpreis" id="kaufpreis" size="15" maxlength="15"
								value="<?= number_format($property->purchase_price, 2, ',', '.') ?>"
								type="text" style="text-align: right;"
								onchange="javascript:recalc();"></td>
						<td style="width: 5%; text-align: right">&euro;</td>
					</tr>
					<tr>
						<td style="height: 30px; text-align: left">
							<label>+ <?= $tag_open .'d2u_immo_finance_calc_real_estate_tax'. $tag_close ?></label></td>
						<td style="text-align: right"><div id="grunderwerbsteuer">
							<input type="hidden" name="grunderwerbsteuer" value="<?= number_format($real_estate_tax * 100, 2, ',', '.') ?>">
							<?= number_format($real_estate_tax * 100, 2, ',', '.') ?></div></td>
						<td style="text-align: right">%</td>
						<td style="text-align: right"><div id="preis_grunderwerbsteuer">
								<?= number_format($property->purchase_price * $real_estate_tax, 2, ',', '.') ?></div></td>
						<td style="text-align: right">&euro;</td>
					</tr>
					<tr>
						<td style="height: 30px; text-align: left;">
							<label>+ <?= $tag_open .'d2u_immo_finance_calc_notary_costs'. $tag_close ?></label></td>
						<td style="text-align: right"><div id="notar"><input type="hidden" name="notarkosten" value="<?= number_format($notary_costs * 100, 2, ',', '.') ?>">ca.
							<?= number_format($notary_costs * 100, 2, ',', '.') ?></div></td>
						<td style="text-align: right">%</td>
						<td style="text-align: right"><div id="preis_notar">
							<?= number_format($property->purchase_price * $notary_costs, 2, ',', '.') ?></div></td>
						<td style="text-align: right">&euro;</td>
					</tr>
					<tr>
						<td style="height: 30px; text-align: left;">
							<label for="maklerprovision">+ <?= $tag_open .'d2u_immo_courtage'. $tag_close ?></label></td>
						<td style="text-align: right"><input name="maklerprovision" id="maklerprovision"
								value="<?= number_format($courtage * 100, 2, ',', '.') ?>"
								size="5" maxlength="5" type="text" style="text-align: right;"
								onchange="javascript:recalc();"></td>
						<td style="text-align: right">%</td>
						<td style="text-align: right"><div id="preis_maklerprovision">
								<?= number_format($property->purchase_price * $courtage, 2, ',', '.') ?></div></td>
						<td style="text-align: right">&euro;</td>
					</tr>
					<tr>
						<td style="height: 30px; text-align: left;">
							<label for="sonstiges">+ <?= $tag_open .'d2u_immo_finance_calc_other_costs'. $tag_close ?></label></td>
						<td></td>
						<td></td>
						<td style="text-align: right"><input name="sonstiges" id="sonstiges"
								value="0,00" size="15" maxlength="15" type="text"
								style="text-align: right;" onchange="javascript:recalc();"></td>
						<td style="text-align: right">&euro;</td>
					</tr>
					<tr>
						<td style="border-top: 1px solid #333; height: 30px; text-align: left;">
							<label><strong><?= $tag_open .'d2u_immo_finance_calc_total_costs'. $tag_close ?></strong></label></td>
						<td style="border-top: 1px solid #333;"></td>
						<td style="border-top: 1px solid #333;"></td>
						<td style="border-top: 1px solid #333; text-align: right">
							<div id="gesamtkosten">
								<?php
                                    $gesamtkosten = $property->purchase_price + ($property->purchase_price * $courtage) + ($property->purchase_price * $real_estate_tax) + ($property->purchase_price * $notary_costs);
                                    echo number_format($gesamtkosten, 2, ',', '.');
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
							<label for="eigenkapital"><?= $tag_open .'d2u_immo_finance_calc_equity'. $tag_close ?></label>
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
							<label><?= $tag_open .'d2u_immo_finance_calc_required_loan'. $tag_close ?></label>
						</td>
						<td></td>
						<td></td>
						<td style="text-align: right"><div id="darlehen"><?= number_format($gesamtkosten, 2, ',', '.') ?></div></td>
						<td style="text-align: right">&euro;</td>
					</tr>
					<tr>
						<td style="height: 30px; text-align: left;">
							<label for="zinssatz"><?= $tag_open .'d2u_immo_finance_calc_interest_rate'. $tag_close ?></label></td>
						<td style="text-align: right"><input name="zinssatz" id="zinssatz"
								value="<?= number_format($interest_rate * 100, 2, ',', '.') ?>"
								size="5" maxlength="5" type="text" style="text-align: right;"
								onchange="javascript:recalc();"></td>
						<td style="text-align: right">%</td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td style="height: 30px; text-align: left;">
							<label for="tilgung"><?= $tag_open .'d2u_immo_finance_calc_repayment'. $tag_close ?></label></td>
						<td style="text-align: right"><input name="tilgung" id="tilgung"
								value="<?= number_format($repayment * 100, 2, ',', '.') ?>"
								size="5" maxlength="5" type="text" style="text-align: right;"
								onchange="javascript:recalc();"></td>
						<td style="text-align: right">%</td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td style="border-top: 1px solid #333; height: 30px; text-align: left;">
							<label><strong><?= $tag_open .'d2u_immo_finance_calc_monthly_rate'. $tag_close ?></strong></label></td>
						<td style="border-top: 1px solid #333;"></td>
						<td style="border-top: 1px solid #333;"></td>
						<td style="border-top: 1px solid #333; text-align: right;">
							<div id="rate"><?= number_format(round((($gesamtkosten * $interest_rate) + ($gesamtkosten * $repayment)) / 12, 2), 2, ',', '.') ?></div></td>
						<td style="border-top: 1px solid #333; text-align: right">&euro;</td>
					</tr>
				</table>
			</fieldset>
			<br />
			<input name="berechnen" id="berechnen" value="<?= $tag_open .'d2u_immo_finance_calc_calculate'. $tag_close ?>" type="submit" onClick="javascript:recalc(); return false;" class="mb-2 btn btn-primary d-print-none">
			<input name="drucken" id="drucken" value="<?= $tag_open .'d2u_immo_print'. $tag_close ?>" onClick="javascript:window.print(); return false;" type="submit" class="mb-2 btn btn-primary d-print-none">
		</form>

<?php
        echo '</div>';
        echo '</div>';
        echo '</div>';  // END tab calculator
    }
    if (null === $print) {
        echo '<div id="tab_request" class="tab-pane immo-tab fade">'; // START tab request
        echo '<div class="col-12">';
        echo '<fieldset><legend>'. $tag_open .'d2u_immo_form_title'. $tag_close .'</legend>';
        $form_data = 'hidden|immo_name|'. $property->name .'|REQUEST

				text|name|'. $tag_open .'d2u_immo_form_name'. $tag_close .' *
				text|address|'. $tag_open .'d2u_immo_form_address'. $tag_close .'
				text|zip|'. $tag_open .'d2u_immo_form_zip'. $tag_close .'
				text|city|'. $tag_open .'d2u_immo_form_city'. $tag_close .'
				text|phone|'. $tag_open .'d2u_immo_form_phone'. $tag_close .' *
				text|email|'. $tag_open .'d2u_immo_form_email'. $tag_close .' *
				textarea|message|'. $tag_open .'d2u_immo_form_message'. $tag_close .'
				checkbox|privacy_policy_accepted|'. $tag_open .'d2u_immo_form_privacy_policy'. $tag_close .' *|0,1|0
				checkbox|phone_calls|'. $tag_open .'d2u_immo_form_phone_calls'. $tag_close .'|0,1|0
				php|validate_timer|Spamprotection|<input name="validate_timer" type="hidden" value="'. microtime(true) .'" />|

				html||<br>* '. $tag_open .'d2u_immo_form_required'. $tag_close .'<br><br>

				submit|submit|'. $tag_open .'d2u_immo_form_send'. $tag_close .'|no_db

				validate|empty|name|'. $tag_open .'d2u_immo_form_validate_name'. $tag_close .'
				validate|empty|phone|'. $tag_open .'d2u_immo_form_validate_phone'. $tag_close .'
				validate|empty|email|'. $tag_open .'d2u_immo_form_validate_email'. $tag_close .'
				validate|type|email|email|'. $tag_open .'d2u_immo_form_validate_email_false'. $tag_close .'
				validate|empty|privacy_policy_accepted|'. $tag_open .'d2u_immo_form_validate_privacy_policy'. $tag_close .'
				validate|customfunction|validate_timer|d2u_addon_frontend_helper::yform_validate_timer|3|'. $tag_open .'d2u_immo_form_validate_spambots'. $tag_close .'|

				action|tpl2email|d2u_immo_request|'. $property->contact->email;

        $yform = new rex_yform();
        $yform->setFormData(trim($form_data));
        $yform->setObjectparams('form_action', $property->getUrl());
        $yform->setObjectparams('form_anchor', 'tab_request');
        $yform->setObjectparams('Error-occured', $tag_open .'d2u_immo_form_validate_title'. $tag_close);
        $yform->setObjectparams('real_field_names', true);
        $yform->setObjectparams('form_name', 'd2u_immo_module_70_1_'. random_int(1, 100));

        // action - showtext
        $yform->setActionField('showtext', [$tag_open .'d2u_immo_form_thanks'. $tag_close]);

        echo $yform->getForm();
        echo '</fieldset>';
        echo '</div>';
        echo '</div>'; // END tab request
        // End request form

        // Recommendation form
        echo '<div id="tab_recommendation" class="tab-pane immo-tab fade">'; // START tab recommendation
        echo '<div class="col-12">';
        echo '<fieldset><legend>'. $tag_open .'d2u_immo_recommendation_title'. $tag_close .'</legend>';
        $form_data = 'hidden|immo_name|'. $property->name .'|REQUEST
				hidden|immo_url|'. $property->getUrl(true) .'|REQUEST
				hidden|immo_contact_mail|'. $property->contact->email .'|REQUEST
				hidden|immo_contact_name|'. $property->contact->firstname .' '. $property->contact->lastname .'|REQUEST

				text|sender_name|'. $tag_open .'d2u_immo_recommendation_sender_name'. $tag_close .' *
				text|sender_mail|'. $tag_open .'d2u_immo_recommendation_sender_mail'. $tag_close .' *
				text|receipient_name|'. $tag_open .'d2u_immo_recommendation_receipient_name'. $tag_close .' *
				text|receipient_mail|'. $tag_open .'d2u_immo_recommendation_receipient_mail'. $tag_close .' *
				textarea|message|'. $tag_open .'d2u_immo_recommendation_message'. $tag_close .'
				php|immo_contact_validate_timer|Spamprotection|<input name="immo_contact_validate_timer" type="hidden" value="'. microtime(true) .'" />|

				html||<br>* '. $tag_open .'d2u_immo_form_required'. $tag_close .'<br><br>
				html||<br>'. $tag_open .'d2u_immo_recommendation_privacy_policy'. $tag_close .'<br><br>

				submit|submit|'. $tag_open .'d2u_immo_form_send'. $tag_close .'|no_db

				validate|empty|sender_name|'. $tag_open .'d2u_immo_recommendation_validate_sender_name'. $tag_close .'
				validate|empty|sender_mail|'. $tag_open .'d2u_immo_recommendation_validate_sender_mail'. $tag_close .'
				validate|type|sender_mail|email|'. $tag_open .'d2u_immo_recommendation_validate_sender_mail'. $tag_close .'
				validate|empty|receipient_name|'. $tag_open .'d2u_immo_recommendation_validate_receipient_name'. $tag_close .'
				validate|empty|receipient_mail|'. $tag_open .'d2u_immo_recommendation_validate_receipient_mail'. $tag_close .'
				validate|type|receipient_mail|email|'. $tag_open .'d2u_immo_recommendation_validate_receipient_mail'. $tag_close .'
				validate|empty|message|'. $tag_open .'d2u_immo_recommendation_validate_message'. $tag_close .'
				validate|customfunction|immo_contact_validate_timer|d2u_addon_frontend_helper::yform_validate_timer|3|'. $tag_open .'d2u_immo_form_validate_spambots'. $tag_close .'|

				action|callback|sendRecommendation';

        $yform_recommend = new rex_yform();
        $yform_recommend->setFormData(trim($form_data));
        $yform_recommend->setObjectparams('form_action', $property->getUrl());
        $yform_recommend->setObjectparams('form_anchor', 'tab_recommendation');
        $yform_recommend->setObjectparams('Error-occured', $tag_open .'d2u_immo_form_validate_title'. $tag_close);
        $yform_recommend->setObjectparams('real_field_names', true);

        // action - showtext
        $yform_recommend->setActionField('showtext', [$tag_open .'d2u_immo_recommendation_thanks'. $tag_close]);

        echo $yform_recommend->getForm();
        echo '</fieldset>';
        echo '</div>';
        echo '</div>'; // END tab recommendation
        // End recommendation form
    }
    if (null === $print) {
        echo '</div>'; // END tab content
        echo '</div>'; // END div containing tab content
    }

    echo '<div class="col-12 d-none d-print-inline">';
    echo '<p>'. $tag_open .'d2u_immo_print_foot'. $tag_close .'</p>';
    echo '<p>'. $tag_open .'d2u_immo_print_foot_greetings'. $tag_close .'</p>';
    echo '<p>'. $property->contact->firstname .' '. $property->contact->lastname;
    if ('' != $property->contact->phone) {
        echo '<br>'. $property->contact->phone;
    }
    if ('' != $property->contact->email) {
        echo '<br>'. $property->contact->email;
    }
    echo '</p>';
    echo '</div>';

} else {
    // Output property list
    $properties_leasehold = D2U_Immo\Property::getAll(Rex_clang::getCurrentId(), 'ERBPACHT', true);
    $properties_leasing = D2U_Immo\Property::getAll(Rex_clang::getCurrentId(), 'LEASING', true);
    $properties_rent = D2U_Immo\Property::getAll(Rex_clang::getCurrentId(), 'MIETE_PACHT', true);
    $properties_sale = D2U_Immo\Property::getAll(Rex_clang::getCurrentId(), 'KAUF', true);

    // Tabs
    echo '<div class="col-12">';
    echo '<ul class="nav nav-pills d-print-none">';
    $tab_active = true;
    if (count($properties_sale) > 0) {
        echo '<li class="nav-item"><a data-toggle="tab" class="nav-link'. ($tab_active ? ' active' : '') .'" href="#tab_sale">'. $tag_open .'d2u_immo_tab_sale'. $tag_close .'</a></li>';
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
        echo '<div id="tab_sale" class="tab-pane immo-tab fade'. ($tab_active ? ' active show' : '') .'">';
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
}
?>
    </div>
</div>
<script>
	// Allow activation of bootstrap tab via URL
	$(function() {
		var hash = window.location.hash;
		hash && $('ul.nav a[href="' + hash + '"]').tab('show');
	});

	// Activate map on hidden tab
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		var target_url = e.target.toString();
		var target_anchor = target_url.substr(target_url.indexOf("#")).toString();
		if(target_anchor === "#tab_map") {
			<?php
                if ('google' == $map_type) {
                    echo "google.maps.event.trigger(map, 'resize');";
                    echo 'map.setCenter(myLatlng);';
                } elseif ('osm' == $map_type && rex_addon::get('osmproxy')->isAvailable()) {
                    echo 'L.Util.requestAnimFrame(map.invalidateSize,map,!1,map._container);';
                } elseif (rex_addon::get('geolocation')->isAvailable()) {
                    echo 'let container = document.getElementById(\''.$map_id.'\');'. PHP_EOL;
                    echo 'if( container ) container.__rmMap.map.invalidateSize();'. PHP_EOL;
                }
            ?>
		}
	});
	<?php
        if (null !== $print) {
            echo 'window.print();';
        }
    ?>
</script>
