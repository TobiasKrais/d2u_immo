<?php

use TobiasKrais\D2UImmo\Contact;

if (!function_exists('sendRecommendation')) {
    /**
     * Sends recommendation mail.
     * @param \rex_yform_action_callback $yform YForm object with fields and values
     */
    function sendRecommendation($yform):void
    {
        if (isset($yform->params['values'])) {
            $fields = [];
            foreach ($yform->params['values'] as $value) {
                if ('' !== $value->name) {
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
            $mail->send();
        }
    }
}

if (!function_exists('printPropertylist')) {
    /**
     * Prints property list.
     * @param array<TobiasKrais\D2UImmo\Property> $properties Array with properties
     */
    function printPropertylist($properties):void
    {
        foreach ($properties as $property) {
            $energy_efficiency_class = $property->getEnergyEfficiencyClass();
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
                    echo '<span>'. \Sprog\Wildcard::get('d2u_immo_object_reserved') .'</span>';
                    echo '</div>';
                }
            }
            echo '</div>';

            echo '<div class="col-12 col-sm-8 col-lg-9">';
            echo '<div class="row">';
            echo '<div class="col-12"><strong>'. $property->name .'</strong></div>';
            echo '<div class="col-12 col-lg-6 nolink"><b>'. \Sprog\Wildcard::get('d2u_immo_form_city') .':</b> '. $property->city .'</div>';
            if ('KAUF' === $property->market_type) {
                echo '<div class="col-12 col-lg-6 nolink"><b>'. \Sprog\Wildcard::get('d2u_immo_purchase_price') .':</b> '
                . ($property->purchase_price_on_request || $property->purchase_price === 0 ? Sprog\Wildcard::get('d2u_immo_purchase_price_on_request') : number_format($property->purchase_price, 0, ',', '.') .',- '. $property->currency_code) .'</div>';
            } elseif ('MIETE_PACHT' === $property->market_type || 'ERBPACHT' === $property->market_type) {
                echo '<div class="col-12 col-lg-6 nolink"><b>'. \Sprog\Wildcard::get('d2u_immo_cold_rent') .':</b> '. number_format($property->cold_rent, 2, ',', '.') .' '. $property->currency_code .'</div>';
            } elseif ('LEASING' === $property->market_type) {
                echo '<div class="col-12 col-lg-6 nolink"><b>'. \Sprog\Wildcard::get('d2u_immo_leasehold') .':</b> '. number_format($property->cold_rent, 2, ',', '.') .' '. $property->currency_code .'</div>';
            }
            if ($property->living_area > 0) {
                echo '<div class="col-12 col-lg-6 nolink"><b>'. \Sprog\Wildcard::get('d2u_immo_living_area') .':</b> '. round($property->living_area) .' m²</div>';
            } elseif ($property->land_area > 0) {
                echo '<div class="col-12 col-lg-6 nolink"><b>'. \Sprog\Wildcard::get('d2u_immo_land_area') .':</b> '. round($property->land_area) .' m²</div>';
            }
            if ('' !== $energy_efficiency_class) {
                echo '<div class="col-12 col-lg-6 nolink"><b>'. \Sprog\Wildcard::get('d2u_immo_energy_efficiency_class') .':</b> '. $energy_efficiency_class .'</div>';
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
     * @param array<string> $pics Array with images
     */
    function printImages($pics):void
    {
        $type_thumb = 'd2u_helper_gallery_thumb';
        $type_detail = 'd2u_helper_gallery_detail';
        $lightbox_id = random_int(0, getrandmax());

        echo '<div class="col-12 print-border">';
        echo '<div class="row">';
        foreach ($pics as $pic) {
            $media = rex_media::get($pic);
            echo '<a href="index.php?rex_media_type='. $type_detail .'&rex_media_file='. $pic .'" data-d2u-immo-ekko-lightbox="lightbox'. $lightbox_id .'" data-gallery="example-gallery'. $lightbox_id .'" class="col-6 col-sm-4 col-lg-3"';
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
    }
}

if (!function_exists('printRevocationNoticeLinks')) {
    /**
     * Prints print links with optional revocation notice modal trigger.
     */
    function printRevocationNoticeLinks(string $property_url, string $notice_file, string $modal_id): void
    {
        if ('' === $notice_file) {
            echo '<li><small><a href="'. $property_url .'?print=small" target="blank"><span class="icon print"></span> '. \Sprog\Wildcard::get('d2u_immo_print_short_expose') .'</a></small></li>';
            echo '<li><small><a href="'. $property_url .'?print=full" target="blank"><span class="icon print"></span> '. \Sprog\Wildcard::get('d2u_immo_print_expose') .'</a></small></li>';
            return;
        }

        echo '<li><small><a href="#'. $modal_id .'" data-toggle="modal" data-target="#'. $modal_id .'" data-d2u-print-modal="'. $modal_id .'" data-d2u-print-target="'. $property_url .'?print=small"><span class="icon print"></span> '. \Sprog\Wildcard::get('d2u_immo_print_short_expose') .'</a></small></li>';
        echo '<li><small><a href="#'. $modal_id .'" data-toggle="modal" data-target="#'. $modal_id .'" data-d2u-print-modal="'. $modal_id .'" data-d2u-print-target="'. $property_url .'?print=full"><span class="icon print"></span> '. \Sprog\Wildcard::get('d2u_immo_print_expose') .'</a></small></li>';
    }
}

if (!function_exists('printRevocationNoticeModal')) {
    /**
     * Prints optional revocation notice modal.
     */
    function printRevocationNoticeModal(string $property_url, string $notice_file, string $modal_id): void
    {
        if ('' === $notice_file) {
            return;
        }

        $notice_url = rex_url::media($notice_file);
        $continue_id = $modal_id .'-continue';
        echo '<div class="modal fade" id="'. $modal_id .'" tabindex="-1" role="dialog" aria-labelledby="'. $modal_id .'-label" aria-hidden="true">';
        echo '<div class="modal-dialog modal-dialog-centered" role="document">';
        echo '<div class="modal-content">';
        echo '<div class="modal-header">';
        echo '<h5 class="modal-title" id="'. $modal_id .'-label">'. \Sprog\Wildcard::get('d2u_immo_revocation_notice_title') .'</h5>';
        echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
        echo '</div>';
        echo '<div class="modal-body">';
        echo '<p>'. \Sprog\Wildcard::get('d2u_immo_revocation_notice_text') .'</p>';
        echo '<p><a href="'. $notice_url .'" target="blank" class="btn btn-outline-secondary"><span class="icon pdf"></span> '. \Sprog\Wildcard::get('d2u_immo_revocation_notice_open_pdf') .'</a></p>';
        echo '</div>';
        echo '<div class="modal-footer">';
        echo '<button type="button" class="btn btn-secondary" data-dismiss="modal">'. \Sprog\Wildcard::get('d2u_immo_revocation_notice_cancel') .'</button>';
        echo '<a href="'. $property_url .'?print=full" target="blank" class="btn btn-primary" id="'. $continue_id .'" data-dismiss="modal" data-d2u-print-continue="1">'. \Sprog\Wildcard::get('d2u_immo_revocation_notice_continue') .'</a>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}

$print = filter_input(INPUT_GET, 'print', FILTER_SANITIZE_SPECIAL_CHARS);
$d2u_immo = rex_addon::get('d2u_immo');
$map_type = 'REX_VALUE[1]' === '' ? 'google' : 'REX_VALUE[1]'; // Backward compatibility /** @phpstan-ignore-line */
$map_id = 'd2u' . md5((string) time());

$url_namespace = TobiasKrais\D2UHelper\FrontendHelper::getUrlNamespace();
$url_id = TobiasKrais\D2UHelper\FrontendHelper::getUrlId();
?>

<div id="d2u_immo_module_70_1" class="col-12 d2u-immo-detail-bs4<?= null !== $print ? ' d2u-immo-auto-print' : '' ?>">
    <div class="row">

<?php
if (filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'property_id' === $url_namespace) {
    // Output property
    $property_id = (int) filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT);
    if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
        $property_id = $url_id;
    }
    $property = new TobiasKrais\D2UImmo\Property($property_id, rex_clang::getCurrentId());
    $widerrufsbelehrung = (string) $d2u_immo->getConfig('widerrufsbelehrung', '');
    // Redirect if object is not online
    if ('online' !== $property->online_status) {
        rex_redirect(rex_article::getNotfoundArticleId(), rex_clang::getCurrentId());
    }

    if (null === $print) {
        $print_modal_id = 'd2u-immo-print-modal-bs4-'. $property_id;
        echo '<div class="col-12 expose-navi d-print-none">';
        echo '<ul>';
        echo '<li><small><a href="'. rex_getUrl((int) $d2u_immo->getConfig('article_id')) .'"><span class="icon back"></span> '. \Sprog\Wildcard::get('d2u_immo_back_to_list') .'</a></small></li>';
        //	Following links see Chrome print bug: https://github.com/twbs/bootstrap/issues/22753
        printRevocationNoticeLinks($property->getUrl(true), $widerrufsbelehrung, $print_modal_id);
        if ('MIETE_PACHT' === $property->market_type && 'GEWERBE' !== $property->type_of_use && '' !== $d2u_immo->getConfig('even_informative_pdf', '')) {
            echo '<li><small><a href="'. rex_url::media('mieterselbstauskunft.pdf') .'"><span class="icon pdf"></span> '. \Sprog\Wildcard::get('d2u_immo_tentant_information') .'</a></small></li>';
        }
        echo '</ul>';
        printRevocationNoticeModal($property->getUrl(true), $widerrufsbelehrung, $print_modal_id);
        echo '</div>';

        if ($property->contact instanceof Contact) {
            echo '<div class="col-12 d-none d-print-inline">';
            echo '<p>'. $property->contact->firstname .' '. $property->contact->lastname .'<br>';
            echo \Sprog\Wildcard::get('d2u_immo_form_phone') .': '. $property->contact->phone .'<br>';
            echo \Sprog\Wildcard::get('d2u_immo_form_email') .': '. $property->contact->email .'<p>';
            echo '</div>';
        }
    }

    // Tabs
    if (null === $print) {
        echo '<div class="col-12 d-print-none">';
        echo '<ul class="nav nav-pills" id="expose_tabs">';
        echo '<li class="nav-item"><a data-toggle="tab" href="#tab_overview" class="nav-link active"><span class="icon home d-md-none"></span><span class="d-none d-md-block">'. \Sprog\Wildcard::get('d2u_immo_tab_overview') .'</span></a></li>';
        if (count($property->pictures) > 0 || count($property->pictures_360) > 0) {
            echo '<li class="nav-item"><a data-toggle="tab" class="nav-link" href="#tab_pictures"><span class="icon pic d-md-none"></span><span class="d-none d-md-block">'. \Sprog\Wildcard::get('d2u_immo_tab_pictures') .'</span></a></li>';
        }
        if ($property->publish_address) {
            echo '<li class="nav-item"><a data-toggle="tab" class="nav-link" href="#tab_map"><span class="icon map d-md-none"></span><span class="d-none d-md-block">'. \Sprog\Wildcard::get('d2u_immo_tab_map') .'</span></a></li>';
        }
        if ('KAUF' === $property->market_type) {
            echo '<li class="nav-item"><a data-toggle="tab" class="nav-link" href="#tab_calculator"><span class="icon money d-md-none"></span><span class="d-none d-md-block">'. \Sprog\Wildcard::get('d2u_immo_tab_calculator') .'</span></a></li>';
        }
        echo '<li class="nav-item"><a data-toggle="tab" class="nav-link" href="#tab_request" id="tab_request_pill"><span class="icon request d-md-none"></span><span class="d-none d-md-block">'. \Sprog\Wildcard::get('d2u_immo_tab_request') .'</span></a></li>';
        echo '<li class="nav-item"><a data-toggle="tab" class="nav-link" href="#tab_recommendation"><span class="icon forward d-md-none"></span><span class="d-none d-md-block">'. \Sprog\Wildcard::get('d2u_immo_tab_recommendation') .'<span></a></li>';
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
            echo '<span class="d-print-none">'. \Sprog\Wildcard::get('d2u_immo_object_reserved') .'</span>';
        } elseif ($property->object_sold) {
            echo '<span class="d-print-none">'. \Sprog\Wildcard::get('d2u_immo_object_sold') .'</span>';
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
        echo '<div class="col-6">'. \Sprog\Wildcard::get('d2u_immo_purchase_price') .':</div>';
        echo '<div class="col-6"><b>'. ($property->purchase_price_on_request || $property->purchase_price === 0 ? Sprog\Wildcard::get('d2u_immo_purchase_price_on_request') : number_format($property->purchase_price, 0, ',', '.') .',- '. $property->currency_code) .'</b></div>';
        if ($property->purchase_price_m2 > 0 && false === $property->purchase_price_on_request) {
            echo '<div class="col-6">'. \Sprog\Wildcard::get('d2u_immo_purchase_price_m2') .':</div>';
            echo '<div class="col-6">'. number_format($property->purchase_price_m2, 2, ',', '.') .'&nbsp;'. $property->currency_code .'</div>';
        }
        if ($property->price_plus_vat && false === $property->purchase_price_on_request) {
            echo '<div class="col-12">'. \Sprog\Wildcard::get('d2u_immo_prices_plus_vat') .'</div>';
        }
        echo '<div class="col-12">&nbsp;</div>';
    } else {
        if ($property->cold_rent > 0 && $property->additional_costs > 0) {
            echo '<div class="col-6">'. \Sprog\Wildcard::get('d2u_immo_warm_rent') .':</div>';
            echo '<div class="col-6"><b>'. number_format($property->cold_rent + $property->additional_costs, 2, ',', '.') .'&nbsp;'. $property->currency_code .'</b></div>';
        }
        if ($property->cold_rent > 0) {
            echo '<div class="col-6">'. \Sprog\Wildcard::get('d2u_immo_cold_rent') .':</div>';
            echo '<div class="col-6">'. number_format($property->cold_rent, 2, ',', '.') .'&nbsp;'. $property->currency_code .'</div>';
        }
        if ($property->additional_costs > 0) {
            echo '<div class="col-6">'. \Sprog\Wildcard::get('d2u_immo_additional_costs') .':</div>';
            echo '<div class="col-6">'. number_format($property->additional_costs, 2, ',', '.') .'&nbsp;'. $property->currency_code .'</div>';
        }
        if ($property->price_plus_vat) {
            echo '<div class="col-12">'. \Sprog\Wildcard::get('d2u_immo_prices_plus_vat') .'</div>';
            echo '<div class="col-12">&nbsp;</div>';
        }
        if ($property->deposit > 0) {
            echo '<div class="col-6">'. \Sprog\Wildcard::get('d2u_immo_deposit') .':</div>';
            echo '<div class="col-6">'. number_format($property->deposit, 2, ',', '.') .'&nbsp;'. $property->currency_code .'</div>';
        }
    }

    if ('HAUS' === strtoupper($property->object_type) || 'WOHNUNG' === strtoupper($property->object_type) || 'BUERO_PRAXEN' === strtoupper($property->object_type)) {
        if ($property->living_area > 0) {
            if ('HAUS' === strtoupper($property->object_type) || 'WOHNUNG' === strtoupper($property->object_type)) {
                echo '<div class="col-6">'. \Sprog\Wildcard::get('d2u_immo_living_area') .':</div>';
            } elseif ('BUERO_PRAXEN' === strtoupper($property->object_type)) {
                echo '<div class="col-6">'. \Sprog\Wildcard::get('d2u_immo_office_area') .':</div>';
            }
            echo '<div class="col-6">'. number_format($property->living_area, 2, ',', '.') .'&nbsp;m²</div>';
        }

        if ($property->rooms > 0) {
            echo '<div class="col-6">'. \Sprog\Wildcard::get('d2u_immo_rooms') .':</div>';
            echo '<div class="col-6">'. $property->rooms .'</div>';
        }

        if ($property->construction_year > 0) {
            echo '<div class="col-6">'. \Sprog\Wildcard::get('d2u_immo_construction_year') .':</div>';
            echo '<div class="col-6">'. $property->construction_year .'</div>';
        }

        if ($property->floor > 0) {
            echo '<div class="col-6">'. \Sprog\Wildcard::get('d2u_immo_floor') .':</div>';
            echo '<div class="col-6">'. $property->floor .'</div>';
        }

        if ($property->flat_sharing_possible) {
            echo '<div class="col-6">'. \Sprog\Wildcard::get('d2u_immo_flat_sharing') .':</div>';
            echo '<div class="col-6">'. \Sprog\Wildcard::get('d2u_immo_yes') .'</div>';
        }

        if ('' !== $property->condition_type) {
            echo '<div class="col-6">'. \Sprog\Wildcard::get('d2u_immo_condition') .':</div>';
            echo '<div class="col-6">'. \Sprog\Wildcard::get('d2u_immo_condition_'. $property->condition_type) .'</div>';
        }
        if ($property->listed_monument) {
            echo '<div class="col-12">'. \Sprog\Wildcard::get('d2u_immo_listed_monument') .'</div>';
        }

        if ('' !== $property->available_from) {
            $date = date_create_from_format('Y-m-d', $property->available_from);
            if (false !== $date) {
                echo '<div class="col-6">'. \Sprog\Wildcard::get('d2u_immo_available_from') .':</div>';
                echo '<div class="col-6">'. date_format($date, 'd.m.Y') .'</div>';
            }
        }

        if ($property->animals) {
            echo '<div class="col-6">'. \Sprog\Wildcard::get('d2u_immo_animals') .':</div>';
            echo '<div class="col-6">'. \Sprog\Wildcard::get('d2u_immo_yes') .'</div>';
        }

        if ($property->rented) {
            echo '<div class="col-6">'. \Sprog\Wildcard::get('d2u_immo_rented') .':</div>';
            echo '<div class="col-6">'. \Sprog\Wildcard::get('d2u_immo_yes') .'</div>';
        }

        if ($property->parking_space_duplex > 0) {
            echo '<div class="col-6">'. \Sprog\Wildcard::get('d2u_immo_parking_space_duplex') .':</div>';
            echo '<div class="col-6">'. $property->parking_space_duplex .'</div>';
        }

        if ($property->parking_space_simple > 0) {
            echo '<div class="col-6">'. \Sprog\Wildcard::get('d2u_immo_parking_space_simple') .':</div>';
            echo '<div class="col-6">'. $property->parking_space_simple .'</div>';
        }

        if ($property->parking_space_garage > 0) {
            echo '<div class="col-6">'. \Sprog\Wildcard::get('d2u_immo_parking_space_garage') .':</div>';
            echo '<div class="col-6">'. $property->parking_space_garage .'</div>';
        }

        if ($property->parking_space_undergroundcarpark > 0) {
            echo '<div class="col-6">'. \Sprog\Wildcard::get('d2u_immo_parking_space_undergroundcarpark') .':</div>';
            echo '<div class="col-6">'. $property->parking_space_undergroundcarpark .'</div>';
        }
    }

    if ($property->total_area > 0) {
        echo '<div class="col-6">'. \Sprog\Wildcard::get('d2u_immo_total_area') .':</div>';
        echo '<div class="col-6">'. $property->total_area .'&nbsp;m²</div>';
    }
    if ($property->land_area > 0) {
        echo '<div class="col-6">'. \Sprog\Wildcard::get('d2u_immo_land_area') .':</div>';
        echo '<div class="col-6">'. round($property->land_area) .'&nbsp;m²</div>';
    }

    if (count($property->documents) > 0 || ('MIETE_PACHT' === $property->market_type && 'GEWERBE' !== $property->type_of_use && '' !== $d2u_immo->getConfig('even_informative_pdf', ''))) {
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
                    echo '<li><span class="icon pdf"></span> <a href="'. rex_url::media($document) .'">'. ('' !== $media->getTitle() ? $media->getTitle() : $document) .'</a></li>';
                }
            }
        }
        if ('MIETE_PACHT' === $property->market_type && 'GEWERBE' !== $property->type_of_use && '' !== $d2u_immo->getConfig('even_informative_pdf', '')) {
            echo '<li class="d-print-none"><span class="icon pdf"></span> <a href="'. rex_url::media('mieterselbstauskunft.pdf') .'">'. \Sprog\Wildcard::get('d2u_immo_tentant_information') .'</a></li>';
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

    if ('grundstueck' !== strtolower($property->object_type)
            && 'parken' !== strtolower($property->object_type)
            && 'projektiert' !== strtolower($property->condition_type)
            && strlen($property->energy_pass) > 5) {
        if (null !== $print) { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
            echo '<div class="row page-break-avoid">';
        }
        echo '<div class="col-12 print-border-h"><h2>'. \Sprog\Wildcard::get('d2u_immo_energy_pass') .'</h2></div>';

        echo '<div class="col-12 print-border">'; // START energy pass
        echo '<div class="row">';

        if($property->energy_pass_year === 'bei_besichtigung') {
            echo '<div class="col-12">'. \Sprog\Wildcard::get('d2u_immo_energy_pass_year_on_visit') .'</div>';
        }
        else if($property->energy_pass_year === 'nicht_noetig') {
            echo '<div class="col-12">'. \Sprog\Wildcard::get('d2u_immo_energy_pass_year_not_necessary') .'</div>';
        }
        else if($property->energy_pass_year === 'ohne') {
            echo '<div class="col-12">'. \Sprog\Wildcard::get('d2u_immo_energy_pass_year_without') .'</div>';
        }
        else {
            echo '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. \Sprog\Wildcard::get('d2u_immo_energy_pass_type') .':</li></ul></div>';
            echo '<div class="col-6 col-md-8 col-lg-9">'. \Sprog\Wildcard::get('d2u_immo_energy_pass_'. $property->energy_pass) .'</div>';

            if ('' !== $property->energy_pass_valid_until) {
                $energy_pass_date = date_create_from_format('Y-m-d', $property->energy_pass_valid_until);
                if (false !== $energy_pass_date) {
                    echo '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. \Sprog\Wildcard::get('d2u_immo_energy_pass_valid_until') .':</li></ul></div>';
                    echo '<div class="col-6 col-md-8 col-lg-9">'. date_format($energy_pass_date, 'd.m.Y') .'</div>';
                }
            }

            echo '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. \Sprog\Wildcard::get('d2u_immo_energy_pass_value') .':</li></ul></div>';
            echo '<div class="col-6 col-md-8 col-lg-9">'. $property->energy_consumption .'&nbsp;kWh/(m²*a)</div>';

            if ('' !== $property->getEnergyEfficiencyClass()) {
                echo '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. \Sprog\Wildcard::get('d2u_immo_energy_efficiency_class') .':</li></ul></div>';
                echo '<div class="col-6 col-md-8 col-lg-9">'. $property->getEnergyEfficiencyClass() .'</div>';
            }

            if ($property->including_warm_water) {
                echo '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. \Sprog\Wildcard::get('d2u_immo_energy_pass_incl_warm_water') .':</li></ul></div>';
                echo '<div class="col-6 col-md-8 col-lg-9">'. \Sprog\Wildcard::get('d2u_immo_yes') .'</div>';
            }

            if ($property->construction_year > 0) {
                echo '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. \Sprog\Wildcard::get('d2u_immo_construction_year') .':</li></ul></div>';
                echo '<div class="col-6 col-md-8 col-lg-9">'. $property->construction_year .'</div>';
            }

            if (count($property->firing_type) > 0) {
                echo '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. \Sprog\Wildcard::get('d2u_immo_firing_type') .':</li></ul></div>';
                echo '<div class="col-6 col-md-8 col-lg-9">';
                $first_element = true;
                foreach ($property->firing_type as $firing_type) {
                    echo($first_element ? '' : ', ') . \Sprog\Wildcard::get('d2u_immo_firing_type_'. $firing_type);
                    $first_element = false;
                }
                echo '</div>';
            }

            echo '<div class="col-12">';
            echo "<div class='energy-scale-container'>";
            echo "<div style='position: absolute;'>";
            echo "<img src='". $d2u_immo->getAssetsUrl('energieskala.png') ."' class='energy_scale'>";
            echo '</div>';
            echo "<div style='position: absolute; margin-left: ". round((int) $property->energy_consumption - 10, 0) ."px !important;'>";
            echo "<img src='". $d2u_immo->getAssetsUrl('zeiger.png') ."'>";
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
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
        echo '<div class="col-12 print-border-h"><h2>'. \Sprog\Wildcard::get('d2u_immo_equipment') .'</h2></div>';

        echo '<div class="col-12 print-border">'; // START detail facts
        echo '<div class="row page-break-avoid">';
        if (count($property->bath) > 0) {
            echo '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. \Sprog\Wildcard::get('d2u_immo_bath') .':</li></ul></div>';
            echo '<div class="col-6 col-md-8 col-lg-9">';
            $first_element = true;
            foreach ($property->bath as $bath) {
                echo($first_element ? '' : ', ') . \Sprog\Wildcard::get('d2u_immo_bath_'. $bath);
                $first_element = false;
            }
            echo '</div>';
        }

        if (count($property->kitchen) > 0) {
            echo '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. \Sprog\Wildcard::get('d2u_immo_kitchen') .':</li></ul></div>';
            echo '<div class="col-6 col-md-8 col-lg-9">';
            $first_element = true;
            foreach ($property->kitchen as $kitchen) {
                echo($first_element ? '' : ', ') . \Sprog\Wildcard::get('d2u_immo_kitchen_'. $kitchen);
                $first_element = false;
            }
            echo '</div>';
        }

        if (count($property->floor_type) > 0) {
            echo '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. \Sprog\Wildcard::get('d2u_immo_floor_type') .':</li></ul></div>';
            echo '<div class="col-6 col-md-8 col-lg-9">';
            $first_element = true;
            foreach ($property->floor_type as $floor_type) {
                echo($first_element ? '' : ', ') . \Sprog\Wildcard::get('d2u_immo_floor_type_'. $floor_type);
                $first_element = false;
            }
            echo '</div>';
        }

        if (count($property->elevator) > 0) {
            echo '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. \Sprog\Wildcard::get('d2u_immo_elevator') .':</li></ul></div>';
            echo '<div class="col-6 col-md-8 col-lg-9">';
            $first_element = true;
            foreach ($property->elevator as $elevator) {
                echo($first_element ? '' : ', ') . \Sprog\Wildcard::get('d2u_immo_elevator_'. $elevator);
                $first_element = false;
            }
            echo '</div>';
        }

        if ($property->cable_sat_tv) {
            echo '<div class="col-12">'. \Sprog\Wildcard::get('d2u_immo_cable_sat_tv') .'</div>';
        }

        if (count($property->broadband_internet) > 0) {
            echo '<div class="col-6 col-md-4 col-lg-3"><ul><li>'. \Sprog\Wildcard::get('d2u_immo_broadband_internet') .':</li></ul></div>';
            echo '<div class="col-6 col-md-8 col-lg-9">'. implode(', ', $property->broadband_internet) .'</div>';
        }
        echo '</div>';
        echo '</div>';  // END detail facts

        echo '<div class="col-12">&nbsp;</div>';
        if (null !== $print) { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
            echo '</div>';
        }
    }

    if ('' !== $property->description) {
        if (null !== $print) { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
            echo '<div class="row page-break-avoid">';
        }
        echo '<div class="col-12 print-border-h"><h2>'. \Sprog\Wildcard::get('d2u_immo_description') .'</h2></div>';
        echo '<div class="col-12 print-border">'. TobiasKrais\D2UHelper\FrontendHelper::prepareEditorField($property->description) .'</div>';
        echo '<div class="col-12">&nbsp;</div>';
        if (null !== $print) { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
            echo '</div>';
        }
    }

    if ('' !== $property->description_location) {
        if (null !== $print) { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
            echo '<div class="row page-break-avoid">';
        }
        echo '<div class="col-12 print-border-h"><h2>'. \Sprog\Wildcard::get('d2u_immo_description_location') .'</h2></div>';
        echo '<div class="col-12 print-border">'. TobiasKrais\D2UHelper\FrontendHelper::prepareEditorField($property->description_location) .'</div>';
        echo '<div class="col-12">&nbsp;</div>';
        if (null !== $print) { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
            echo '</div>';
        }
    }

    if ('' !== $property->description_equipment) {
        if (null !== $print) { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
            echo '<div class="row page-break-avoid">';
        }
        echo '<div class="col-12 print-border-h"><h2>'. \Sprog\Wildcard::get('d2u_immo_description_equipment') .'</h2></div>';
        echo '<div class="col-12 print-border">'. TobiasKrais\D2UHelper\FrontendHelper::prepareEditorField($property->description_equipment) .'</div>';
        echo '<div class="col-12">&nbsp;</div>';
        if (null !== $print) { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
            echo '</div>';
        }
    }

    if ('' !== $property->description_others) {
        if (null !== $print) { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
            echo '<div class="row page-break-avoid">';
        }
        echo '<div class="col-12 print-border-h"><h2>'. \Sprog\Wildcard::get('d2u_immo_description_others') .'</h2></div>';
        echo '<div class="col-12 print-border">'. TobiasKrais\D2UHelper\FrontendHelper::prepareEditorField($property->description_others) .'</div>';
        echo '<div class="col-12">&nbsp;</div>';
        if (null !== $print) { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
            echo '</div>';
        }
    }

    if (null !== $print) { // Remove when https://github.com/twbs/bootstrap/issues/22753 is solved
        echo '<div class="row page-break-avoid">';
    }
    echo '<div class="col-12 print-border-h"><h2>'. \Sprog\Wildcard::get('d2u_immo_courtage') .'</h2></div>';
    if ('' === $property->courtage) {
        echo '<div class="col-12 print-border">'. \Sprog\Wildcard::get('d2u_immo_courtage_no') .'</div>';
    } else {
        echo '<div class="col-12 print-border">'. $property->courtage .' '. \Sprog\Wildcard::get('d2u_immo_courtage_incl_vat') .'</div>';
    }
    echo '<div class="col-12 d-none d-print-inline">&nbsp;</div>';

    echo '</div>'; // END row overview
    if (null === $print) {
        echo '</div>'; // END tab overview
    }
    // End Overview
    // Pictures
    if (count($property->pictures) > 0 && 'small' !== $print) {
        if ('full' !== $print) {
            echo '<div id="tab_pictures" class="tab-pane immo-tab fade">'; // START tab picures
        }
        echo '<div class="row">'; // START pictures
        echo '<div class="col-12 d-none d-print-inline print-border-h">';
        echo '<h2>'. \Sprog\Wildcard::get('d2u_immo_tab_pictures') .'</h2>';
        echo '</div>';
        echo '<div class="col-12 d-none d-print-none">';
        echo '<h2>'. $property->name .'</h2>';
        echo '</div>';
        printImages($property->pictures);

        // 360° pictures
        $viewer_id = 0;
        if (count($property->pictures_360) > 0) {
            echo '<div class="col-12 d-print-none">&nbsp;</div>';
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
            echo '<h2>'. \Sprog\Wildcard::get('d2u_immo_ground_plans') .'</h2>';
            echo '</div>';
            printImages($property->ground_plans);
            echo '<div class="col-12 d-none d-print-inline">&nbsp;</div>';
            echo '</div>';
        }

        if (count($property->location_plans) > 0) {
            echo '<div class="row">';
            echo '<div class="col-12 print-border-h">';
            echo '<h2>'. \Sprog\Wildcard::get('d2u_immo_location_plans') .'</h2>';
            echo '</div>';
            printImages($property->location_plans);
            echo '<div class="col-12 d-none d-print-inline">&nbsp;</div>';
            echo '</div>';
        }
        if ('full' !== $print) {
            echo '</div>'; // END tab picures
        }
    }
    // End Pictures
    // Map
    if ($property->publish_address && 'small' !== $print) {
        $d2u_helper = rex_addon::get('d2u_helper');
        $api_key = '';
        if ($d2u_helper->hasConfig('maps_key')) {
            $api_key = (string) $d2u_helper->getConfig('maps_key');
        }
        if ('full' !== $print) {
            echo '<div id="tab_map" class="tab-pane immo-tab fade page-break-avoid">'; // START tab map
        }
        echo '<div class="row page-break-avoid">';
        echo '<div class="col-12 d-none d-print-inline print-border-h">';
        echo '<h2>'. \Sprog\Wildcard::get('d2u_immo_tab_map') .'</h2>';
        echo '</div>';
        echo '<div class="col-12 print-border">';
        echo '<h2 class="d-print-none">'. $property->name .'</h2>';
        echo '<p class="d-print-none">'. $property->street .' '. $property->house_number .'<br /> '. $property->zip_code .' '. $property->city .'</p>';

        if ('google' === $map_type) { /** @phpstan-ignore-line */
?>
		<script src="https://maps.googleapis.com/maps/api/js?key=<?= $api_key ?>"></script>
		<div id="map_canvas" style="display: block; <?= '' !== $print ? 'width: 900px' : 'width: 100%' ?>; height: 500px"></div>
		<script>
			var map;
			var myLatlng;
			<?php
                // if longitude and latitude are available
                if (0.0 !== $property->longitude && 0.0 !== $property->latitude) {
            ?>
				var myLatlng = new google.maps.LatLng(<?= $property->latitude .', '. $property->longitude ?>);
				var myOptions = {
					zoom: 15,
					center: myLatlng,
					mapTypeId: google.maps.MapTypeId.<?= 'full' === $print ? 'ROADMAP' : 'HYBRID' ?>,
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
					mapTypeId: google.maps.MapTypeId.<?= 'full' === $print ? 'ROADMAP' : 'HYBRID' ?>,
				};
				map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
			<?php
                }
            ?>
		</script>
		<?php
        } elseif ('osm' === $map_type && rex_addon::get('osmproxy')->isAvailable()) { /** @phpstan-ignore-line */
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
                    if(rex_version::compare('2.0.0', rex_addon::get('geolocation')->getVersion(), '<=')) {
                        // Geolocation 2.x
                        \FriendsOfRedaxo\Geolocation\Tools::echoAssetTags();
                    }
                    else {
                        // Geolocation 1.x
                        // @deprecated remove in Version 2
                        \Geolocation\tools::echoAssetTags(); /** @phpstan-ignore-line */
                    }
                }
?>
<script>
	Geolocation.default.positionColor = '<?= (string) rex_config::get('d2u_helper', 'article_color_h') ?>';

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

            if(rex_version::compare('2.0.0', rex_addon::get('geolocation')->getVersion(), '<=')) {
                // Geolocation 2.x
                echo \FriendsOfRedaxo\Geolocation\Mapset::take((int) $map_type)
                    ->attributes('id', $map_id)
                    ->dataset('position', [$property->latitude, $property->longitude])
                    ->dataset('center', [[$property->latitude, $property->longitude], 15])
                    ->parse();
            }
            else {
                // Geolocation 1.x
                // @deprecated remove in Version 2
                echo \Geolocation\mapset::take((int) $map_type) /** @phpstan-ignore-line */
                    ->attributes('id', $map_id)
                    ->dataset('position', [$property->latitude, $property->longitude])
                    ->dataset('center', [[$property->latitude, $property->longitude], 15])
                    ->parse();
            }
        }

        echo '</div>';
        echo '</div>';
        if ('full' !== $print) {
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
        $courtage = is_numeric($property->courtage) ? ((float) strtr((string) ((float) strtr($property->courtage, ',', '.') * 1), ',', '.') / 100) : 0;
        $interest_rate = $d2u_immo->getConfig('finance_calculator_interest_rate');
        $repayment = $d2u_immo->getConfig('finance_calculator_repayment');

        echo '<h2>'. $property->name .'</h2>';
?>
		<form id="finanzierungsrechner" method="post" target="blank">
			<input name="option" value="finanzierungsrechner" type="hidden">
			<fieldset>
				<legend><?= \Sprog\Wildcard::get('d2u_immo_finance_calc_investement') ?></legend>
				<table style="width: 100%;">
					<tr>
						<td style="width: 45%; height: 30px; text-align: left">
							<strong><label for="kaufpreis"><?= \Sprog\Wildcard::get('d2u_immo_purchase_price') ?></label></strong>
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
							<label>+ <?= \Sprog\Wildcard::get('d2u_immo_finance_calc_real_estate_tax') ?></label></td>
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
							<label>+ <?= \Sprog\Wildcard::get('d2u_immo_finance_calc_notary_costs') ?></label></td>
						<td style="text-align: right"><div id="notar"><input type="hidden" name="notarkosten" value="<?= number_format($notary_costs * 100, 2, ',', '.') ?>">ca.
							<?= number_format($notary_costs * 100, 2, ',', '.') ?></div></td>
						<td style="text-align: right">%</td>
						<td style="text-align: right"><div id="preis_notar">
							<?= number_format($property->purchase_price * $notary_costs, 2, ',', '.') ?></div></td>
						<td style="text-align: right">&euro;</td>
					</tr>
					<tr>
						<td style="height: 30px; text-align: left;">
							<label for="maklerprovision">+ <?= \Sprog\Wildcard::get('d2u_immo_courtage') ?></label></td>
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
							<label for="sonstiges">+ <?= \Sprog\Wildcard::get('d2u_immo_finance_calc_other_costs') ?></label></td>
						<td></td>
						<td></td>
						<td style="text-align: right"><input name="sonstiges" id="sonstiges"
								value="0,00" size="15" maxlength="15" type="text"
								style="text-align: right;" onchange="javascript:recalc();"></td>
						<td style="text-align: right">&euro;</td>
					</tr>
					<tr>
						<td style="border-top: 1px solid #333; height: 30px; text-align: left;">
							<label><strong><?= \Sprog\Wildcard::get('d2u_immo_finance_calc_total_costs') ?></strong></label></td>
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
							<label for="eigenkapital"><?= \Sprog\Wildcard::get('d2u_immo_finance_calc_equity') ?></label>
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
							<label><?= \Sprog\Wildcard::get('d2u_immo_finance_calc_required_loan') ?></label>
						</td>
						<td></td>
						<td></td>
						<td style="text-align: right"><div id="darlehen"><?= number_format($gesamtkosten, 2, ',', '.') ?></div></td>
						<td style="text-align: right">&euro;</td>
					</tr>
					<tr>
						<td style="height: 30px; text-align: left;">
							<label for="zinssatz"><?= \Sprog\Wildcard::get('d2u_immo_finance_calc_interest_rate') ?></label></td>
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
							<label for="tilgung"><?= \Sprog\Wildcard::get('d2u_immo_finance_calc_repayment') ?></label></td>
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
							<label><strong><?= \Sprog\Wildcard::get('d2u_immo_finance_calc_monthly_rate') ?></strong></label></td>
						<td style="border-top: 1px solid #333;"></td>
						<td style="border-top: 1px solid #333;"></td>
						<td style="border-top: 1px solid #333; text-align: right;">
							<div id="rate"><?= number_format(round((($gesamtkosten * $interest_rate) + ($gesamtkosten * $repayment)) / 12, 2), 2, ',', '.') ?></div></td>
						<td style="border-top: 1px solid #333; text-align: right">&euro;</td>
					</tr>
				</table>
			</fieldset>
			<br />
			<input name="berechnen" id="berechnen" value="<?= \Sprog\Wildcard::get('d2u_immo_finance_calc_calculate') ?>" type="submit" onClick="javascript:recalc(); return false;" class="mb-2 btn btn-primary d-print-none">
			<input name="drucken" id="drucken" value="<?= \Sprog\Wildcard::get('d2u_immo_print') ?>" onClick="javascript:window.print(); return false;" type="submit" class="mb-2 btn btn-primary d-print-none">
		</form>

<?php
        echo '</div>';
        echo '</div>';
        echo '</div>';  // END tab calculator
    }
    if (null === $print) {
        echo '<div id="tab_request" class="tab-pane immo-tab fade">'; // START tab request
        echo '<div class="row">';
        echo '<div class="col-12">';
        echo '<fieldset><legend>'. \Sprog\Wildcard::get('d2u_immo_form_title') .'</legend>';
        $form_data = 'hidden|immo_name|'. $property->name .'|REQUEST

				text|name|'. \Sprog\Wildcard::get('d2u_immo_form_name') .' *
				text|address|'. \Sprog\Wildcard::get('d2u_immo_form_address') .'
				text|zip|'. \Sprog\Wildcard::get('d2u_immo_form_zip') .'
				text|city|'. \Sprog\Wildcard::get('d2u_immo_form_city') .'
				text|phone|'. \Sprog\Wildcard::get('d2u_immo_form_phone') .' *
				text|email|'. \Sprog\Wildcard::get('d2u_immo_form_email') .' *
				textarea|message|'. \Sprog\Wildcard::get('d2u_immo_form_message') .'
				checkbox|privacy_policy_accepted|'. \Sprog\Wildcard::get('d2u_immo_form_privacy_policy') .' *|0,1|0
				checkbox|phone_calls|'. \Sprog\Wildcard::get('d2u_immo_form_phone_calls') .'|0,1|0
				php|validate_timer|Spamprotection|<input name="validate_timer" type="hidden" value="'. microtime(true) .'" />|

				html||<br>* '. \Sprog\Wildcard::get('d2u_immo_form_required') .'<br><br>

				submit|submit|'. \Sprog\Wildcard::get('d2u_immo_form_send') .'|no_db

				validate|empty|name|'. \Sprog\Wildcard::get('d2u_immo_form_validate_name') .'
				validate|empty|phone|'. \Sprog\Wildcard::get('d2u_immo_form_validate_phone') .'
				validate|empty|email|'. \Sprog\Wildcard::get('d2u_immo_form_validate_email') .'
				validate|type|email|email|'. \Sprog\Wildcard::get('d2u_immo_form_validate_email_false') .'
				validate|empty|privacy_policy_accepted|'. \Sprog\Wildcard::get('d2u_immo_form_validate_privacy_policy') .'
				validate|customfunction|validate_timer|TobiasKrais\D2UHelper\FrontendHelper::yform_validate_timer|3|'. \Sprog\Wildcard::get('d2u_immo_form_validate_spambots') .'|

				action|tpl2email|d2u_immo_request|'. ($property->contact instanceof Contact ? $property->contact->email : rex::getErrorEmail());

        $yform = new rex_yform();
        $yform->setFormData(trim($form_data));
        $yform->setObjectparams('form_action', $property->getUrl());
        $yform->setObjectparams('form_anchor', 'tab_request');
        $yform->setObjectparams('Error-occured', \Sprog\Wildcard::get('d2u_immo_form_validate_title'));
        $yform->setObjectparams('real_field_names', true);
        $yform->setObjectparams('form_name', 'd2u_immo_module_70_1_request_'. $this->getCurrentSlice()->getId()); /** @phpstan-ignore-line */

        // action - showtext
        $yform->setActionField('showtext', [\Sprog\Wildcard::get('d2u_immo_form_thanks')]);

        echo $yform->getForm();
        echo '</fieldset>';
        echo '</div>';
        echo '</div>';
        echo '</div>'; // END tab request
        // End request form

        // Recommendation form
        echo '<div id="tab_recommendation" class="tab-pane immo-tab fade">'; // START tab recommendation
        echo '<div class="row">';
        echo '<div class="col-12">';
        echo '<fieldset><legend>'. \Sprog\Wildcard::get('d2u_immo_recommendation_title') .'</legend>';
        $form_data = 'hidden|immo_name|'. $property->name .'|REQUEST
				hidden|immo_url|'. $property->getUrl(true) .'|REQUEST
				hidden|immo_contact_mail|'. ($property->contact instanceof Contact ? $property->contact->email : '') .'|REQUEST
				hidden|immo_contact_name|'. ($property->contact instanceof Contact ? $property->contact->firstname .' '. $property->contact->lastname : '') .'|REQUEST

				text|sender_name|'. \Sprog\Wildcard::get('d2u_immo_recommendation_sender_name') .' *
				text|sender_mail|'. \Sprog\Wildcard::get('d2u_immo_recommendation_sender_mail') .' *
				text|receipient_name|'. \Sprog\Wildcard::get('d2u_immo_recommendation_receipient_name') .' *
				text|receipient_mail|'. \Sprog\Wildcard::get('d2u_immo_recommendation_receipient_mail') .' *
				textarea|message|'. \Sprog\Wildcard::get('d2u_immo_recommendation_message') .'
				php|immo_contact_validate_timer|Spamprotection|<input name="immo_contact_validate_timer" type="hidden" value="'. microtime(true) .'" />|

				html||<br>* '. \Sprog\Wildcard::get('d2u_immo_form_required') .'<br><br>
				html||<br>'. \Sprog\Wildcard::get('d2u_immo_recommendation_privacy_policy') .'<br><br>

				submit|submit|'. \Sprog\Wildcard::get('d2u_immo_form_send') .'|no_db

				validate|empty|sender_name|'. \Sprog\Wildcard::get('d2u_immo_recommendation_validate_sender_name') .'
				validate|empty|sender_mail|'. \Sprog\Wildcard::get('d2u_immo_recommendation_validate_sender_mail') .'
				validate|type|sender_mail|email|'. \Sprog\Wildcard::get('d2u_immo_recommendation_validate_sender_mail') .'
				validate|empty|receipient_name|'. \Sprog\Wildcard::get('d2u_immo_recommendation_validate_receipient_name') .'
				validate|empty|receipient_mail|'. \Sprog\Wildcard::get('d2u_immo_recommendation_validate_receipient_mail') .'
				validate|type|receipient_mail|email|'. \Sprog\Wildcard::get('d2u_immo_recommendation_validate_receipient_mail') .'
				validate|empty|message|'. \Sprog\Wildcard::get('d2u_immo_recommendation_validate_message') .'
				validate|customfunction|immo_contact_validate_timer|TobiasKrais\D2UHelper\FrontendHelper::yform_validate_timer|3|'. \Sprog\Wildcard::get('d2u_immo_form_validate_spambots') .'|

				action|callback|sendRecommendation';

        $yform_recommend = new rex_yform();
        $yform_recommend->setFormData(trim($form_data));
        $yform_recommend->setObjectparams('form_action', $property->getUrl());
        $yform_recommend->setObjectparams('form_anchor', 'tab_recommendation');
        $yform_recommend->setObjectparams('Error-occured', \Sprog\Wildcard::get('d2u_immo_form_validate_title'));
        $yform_recommend->setObjectparams('real_field_names', true);
        $yform_recommend->setObjectparams('form_name', 'd2u_immo_module_70_1_recommend_'. $this->getCurrentSlice()->getId()); /** @phpstan-ignore-line */

        // action - showtext
        $yform_recommend->setActionField('showtext', [\Sprog\Wildcard::get('d2u_immo_recommendation_thanks')]);

        echo $yform_recommend->getForm();
        echo '</fieldset>';
        echo '</div>';
        echo '</div>';
        echo '</div>'; // END tab recommendation
        // End recommendation form
    }
    if (null === $print) {
        echo '</div>'; // END tab content
        echo '</div>'; // END div containing tab content
    }

    if ($property->contact instanceof Contact) {
        echo '<div class="col-12 d-none d-print-inline">';
        echo '<p>'. \Sprog\Wildcard::get('d2u_immo_print_foot') .'</p>';
        echo '<p>'. \Sprog\Wildcard::get('d2u_immo_print_foot_greetings') .'</p>';
        echo '<p>'. $property->contact->firstname .' '. $property->contact->lastname;
        if ('' !== $property->contact->phone) {
            echo '<br>'. $property->contact->phone;
        }
        if ('' !== $property->contact->email) {
            echo '<br>'. $property->contact->email;
        }
        echo '</p>';
        echo '</div>';
    }

} else {
    // Output property list
    $properties_leasehold = TobiasKrais\D2UImmo\Property::getAll(rex_clang::getCurrentId(), 'ERBPACHT', true);
    $properties_leasing = TobiasKrais\D2UImmo\Property::getAll(rex_clang::getCurrentId(), 'LEASING', true);
    $properties_rent = TobiasKrais\D2UImmo\Property::getAll(rex_clang::getCurrentId(), 'MIETE_PACHT', true);
    $properties_sale = TobiasKrais\D2UImmo\Property::getAll(rex_clang::getCurrentId(), 'KAUF', true);

    // Tabs
    echo '<div class="col-12">';
    echo '<ul class="nav nav-pills d-print-none">';
    $tab_active = true;
    if (count($properties_sale) > 0) {
        echo '<li class="nav-item"><a data-toggle="tab" class="nav-link active" href="#tab_sale">'. \Sprog\Wildcard::get('d2u_immo_tab_sale') .'</a></li>';
        $tab_active = false;
    }
    if (count($properties_rent) > 0) {
        echo '<li class="nav-item"><a data-toggle="tab" class="nav-link'. ($tab_active ? ' active' : '') .'" href="#tab_rent">'. \Sprog\Wildcard::get('d2u_immo_tab_rent') .'</a></li>';
        $tab_active = false;
    }
    if (count($properties_leasing) > 0) {
        echo '<li class="nav-item"><a data-toggle="tab" class="nav-link'. ($tab_active ? ' active' : '') .'" href="#tab_leasing">'. \Sprog\Wildcard::get('d2u_immo_tab_leasing') .'</a></li>';
        $tab_active = false;
    }
    if (count($properties_leasehold) > 0) {
        echo '<li class="nav-item"><a data-toggle="tab" class="nav-link'. ($tab_active ? ' active' : '') .'" href="#tab_leasehold">'. \Sprog\Wildcard::get('d2u_immo_tab_leasehold') .'</a></li>';
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
                if ('google' === $map_type) { /** @phpstan-ignore-line */
                    echo "google.maps.event.trigger(map, 'resize');";
                    echo 'map.setCenter(myLatlng);';
                } elseif ('osm' === $map_type && rex_addon::get('osmproxy')->isAvailable()) { /** @phpstan-ignore-line */
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
