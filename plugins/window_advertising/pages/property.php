<?php
$func = rex_request('func', 'string');
$property_id = rex_request('property_id', 'int');

/*
 * Do actions
 */
if ($func == 'online' || $func == 'offline') {
	// Change status
	$property = new D2U_Immo\Property($property_id, rex_config::get("d2u_helper", "default_lang"));
	$property->property_id = $property_id; // Ensure correct ID in case language has no object
	$property->changeWindowAdvertisingStatus();

	header("Location: ". rex_url::currentBackendPage());
	exit;
}

print '<table class="table table-striped table-hover">';
$properties = D2U_Immo\Property::getAll(rex_config::get("d2u_helper", "default_lang"), '', TRUE);

print "<thead>";
print "<tr>";
print "<th><b>". rex_i18n::msg('d2u_immo_property') ."</b></th>";
print "<th><b>". rex_i18n::msg('d2u_immo_window_advertising_show') ."</b></th>";
print "</tr>";
print "</thead>";
print "<tbody>";

// Only if properties are available
if(count($properties) > 0) {
	foreach($properties as $property) {
		print "<tr>";
		print "<td>". $property->name ."</td>";
		if($property->window_advertising_status) {
			print '<td class="rex-table-action"><a href="'. rex_url::currentBackendPage(array('func'=>'offline', 'property_id'=>$property->property_id))
				.'" class="rex-online"><i class="rex-icon rex-icon-online"></i> '. rex_i18n::msg('status_online') .'</a></td>';
		}
		else {
			print '<td class="rex-table-action"><a href="'. rex_url::currentBackendPage(array('func'=>'online', 'property_id'=>$property->property_id))
				.'" class="rex-offline"><i class="rex-icon rex-icon-offline"></i> '. rex_i18n::msg('status_offline') .'</a></td>';
		}
		print "</tr>";
	}
}
print "</tbody>";
print "</table>";