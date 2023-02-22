<?php

$func = rex_request('func', 'string');
$property_id = rex_request('property_id', 'int');

/*
 * Do actions
 */
if ('online' == $func || 'offline' == $func) {
    // Change status
    $property = new D2U_Immo\Property($property_id, (int) rex_config::get('d2u_helper', 'default_lang'));
    $property->property_id = $property_id; // Ensure correct ID in case language has no object
    $property->changeWindowAdvertisingStatus();

    header('Location: '. rex_url::currentBackendPage());
    exit;
}

echo '<table class="table table-striped table-hover">';
$properties = D2U_Immo\Property::getAll((int) rex_config::get('d2u_helper', 'default_lang'), '', true);

echo '<thead>';
echo '<tr>';
echo '<th><b>'. rex_i18n::msg('d2u_immo_property') .'</b></th>';
echo '<th><b>'. rex_i18n::msg('d2u_immo_window_advertising_show') .'</b></th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

// Only if properties are available
if (count($properties) > 0) {
    foreach ($properties as $property) {
        echo '<tr>';
        echo '<td>'. $property->name .'</td>';
        if ($property->window_advertising_status) {
            echo '<td class="rex-table-action"><a href="'. rex_url::currentBackendPage(['func' => 'offline', 'property_id' => $property->property_id])
                .'" class="rex-online"><i class="rex-icon rex-icon-online"></i> '. rex_i18n::msg('status_online') .'</a></td>';
        } else {
            echo '<td class="rex-table-action"><a href="'. rex_url::currentBackendPage(['func' => 'online', 'property_id' => $property->property_id])
                .'" class="rex-offline"><i class="rex-icon rex-icon-offline"></i> '. rex_i18n::msg('status_offline') .'</a></td>';
        }
        echo '</tr>';
    }
}
echo '</tbody>';
echo '</table>';
