<?php

$func = rex_request('func', 'string');
$provider_id = (int) rex_request('provider_id', 'int');
$property_id = (int) rex_request('property_id', 'int');

/*
 * Do actions
 */
if ('online' === $func) {
    // Add to next export
    $export_property = new D2U_Immo\ExportedProperty($property_id, $provider_id);
    $export_property->addToExport();
} elseif ('offline' === $func) {
    // Remove to next export
    $export_property = new D2U_Immo\ExportedProperty($property_id, $provider_id);
    $export_property->removeFromExport();
} elseif ('all_online' === $func) {
    // Add all to next export
    D2U_Immo\ExportedProperty::addAllToExport($provider_id);
} elseif ('all_offline' === $func) {
    // Remove all from next export
    D2U_Immo\ExportedProperty::removeAllFromExport($provider_id);
} elseif ('export' === $func) {
    // Export
    $provider = new D2U_Immo\Provider($provider_id);
    $error = $provider->export();
    if ('' !== $error) {
        echo rex_view::error($provider->name .': '. $error);
    } else {
        echo rex_view::success($provider->name .': '. rex_i18n::msg('d2u_immo_export_success'));
    }
}

// Fetch providers
$providers = D2U_Immo\Provider::getAll();

echo '<table class="table table-striped table-hover">';
if (count($providers) > 0) {
    $properties = D2U_Immo\Property::getAll((int) rex_config::get('d2u_helper', 'default_lang'), '', true);

    echo '<thead>';
    echo '<tr>';
    echo '<th><b>'. rex_i18n::msg('d2u_immo_property') .'</b></th>';
    foreach ($providers as $provider) {
        echo '<th><b>'. $provider->name .'</b></th>';
    }
    echo '</tr>';
    echo '<tr>';
    echo '<td>&nbsp;</td>';
    foreach ($providers as $provider) {
        echo '<td>';
        if ($provider->isExportPossible()) {
            echo "<a href='". rex_url::currentBackendPage(['func' => 'export', 'provider_id' => $provider->provider_id]) ."'>"
                . "<button class='btn btn-apply'>". rex_i18n::msg('d2u_immo_export_start') .'</button></a>';
        }
        echo '</td>';
    }
    echo '</tr>';
    echo '<tr>';
    echo '<td><b>'. rex_i18n::msg('d2u_immo_export_last_export_date') .'</b></td>';
    foreach ($providers as $provider) {
        echo '<td>';
        if ('' !== $provider->getLastExportTimestamp()) {
            echo date('d.m.Y H:i', strtotime($provider->getLastExportTimestamp())) .' '. rex_i18n::msg('d2u_immo_export_uhr');
        }
        echo '</td>';
    }
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    // Only if properties are available
    if (count($properties) > 0) {
        // Possibility to add all properties to export
        echo '<tr>';
        echo '<td><i>'. rex_i18n::msg('d2u_immo_export_all_online') .'</i></td>';
        foreach ($providers as $provider) {
            echo '<td class="rex-table-action"><a href="'. rex_url::currentBackendPage(['func' => 'all_online', 'provider_id' => $provider->provider_id])
                    .'" class="rex-online"><i class="rex-icon rex-icon-online"></i> '. rex_i18n::msg('status_online') .'</a></td>';
        }
        echo '</tr>';
        // Posibility to remove all properties from export
        echo '<tr>';
        echo '<td><i>'. rex_i18n::msg('d2u_immo_export_all_offline') .'</i></td>';
        foreach ($providers as $provider) {
            echo '<td class="rex-table-action"><a href="'. rex_url::currentBackendPage(['func' => 'all_offline', 'provider_id' => $provider->provider_id])
                    .'" class="rex-offline"><i class="rex-icon rex-icon-offline"></i> '. rex_i18n::msg('status_offline') .'</a></td>';
        }
        echo '</tr>';
        // How many properties are set for export?
        echo '<tr>';
        echo '<td><i>'. rex_i18n::msg('d2u_immo_export_number_online') .'</i></td>';
        foreach ($providers as $provider) {
            echo '<td><i>'. $provider->getNumberOnlineProperties() .'</i></td>';
        }
        echo '</tr>';

        foreach ($properties as $property) {
            echo '<tr>';
            echo '<td>'. $property->name .'</td>';
            foreach ($providers as $provider) {
                $exported_property = new D2U_Immo\ExportedProperty($property->property_id, $provider->provider_id);
                if ($exported_property->isSetForExport()) {
                    echo '<td class="rex-table-action"><a href="'. rex_url::currentBackendPage(['func' => 'offline', 'provider_id' => $provider->provider_id, 'property_id' => $property->property_id])
                        .'" class="rex-online"><i class="rex-icon rex-icon-online"></i> '. rex_i18n::msg('status_online') .'</a></td>';
                } else {
                    echo '<td class="rex-table-action"><a href="'. rex_url::currentBackendPage(['func' => 'online', 'provider_id' => $provider->provider_id, 'property_id' => $property->property_id])
                        .'" class="rex-offline"><i class="rex-icon rex-icon-offline"></i> '. rex_i18n::msg('status_offline') .'</a></td>';
                }
            }
            echo '</tr>';
        }
    }
    echo '</tbody>';
} else {
    echo '<tr><th><b>'. rex_i18n::msg('d2u_immo_export_no_providers_found') .'</b></th></tr>';
}
echo '</table>';
