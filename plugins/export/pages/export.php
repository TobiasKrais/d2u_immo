<?php
$func = rex_request('func', 'string');
$provider_id = rex_request('provider_id', 'int');
$property_id = rex_request('property_id', 'int');

/*
 * Do actions
 */
if ($func == 'online') {
	// Add to next export
	$export_property = new ExportedProperty($property_id, $provider_id);
	$export_property->addToExport();
}
else if ($func == 'offline') {
	// Remove to next export
	$export_property = new ExportedProperty($property_id, $provider_id);
	$export_property->removeFromExport();
}
else if ($func == 'all_online') {
	// Add all to next export
	ExportedProperty::addAllToExport($provider_id);
}
else if ($func == 'all_offline') {
	// Remove all from next export
	ExportedProperty::removeAllFromExport($provider_id);
}
else if ($func == 'export') {
	// Export
	$provider = new Provider($provider_id);
	$error = $provider->export();
	if($error != "") {
		print rex_view::error($provider->name .": ". $error);
	}
	else {
		print rex_view::success($provider->name .": ". rex_i18n::msg('d2u_immo_export_success'));
	}
}

// Fetch providers
$d2u_immo = rex_addon::get('d2u_immo');
$providers = Provider::getAll();

print '<table class="table table-striped table-hover">';
if(count($providers) > 0) {
	$properties = Property::getAll($d2u_immo->getConfig('default_lang'), '', TRUE);

	print "<thead>";
	print "<tr>";
	print "<th><b>". rex_i18n::msg('d2u_immo_property') ."</b></th>";
	foreach ($providers as $provider) {
		print "<th><b>". $provider->name ."</b></th>";
	}
	print "</tr>";
	print "<tr>";
	print "<td>&nbsp;</td>";
	foreach ($providers as $provider) {
		print "<td><a href='". rex_url::currentBackendPage(array('func'=>'export', 'provider_id'=>$provider->provider_id)) ."'>"
			. "<button class='btn btn-apply'>". rex_i18n::msg('d2u_immo_export_start') ."</button></a></td>";
	}
	print "</tr>";
	print "<tr>";
	print "<td><b>". rex_i18n::msg('d2u_immo_export_last_export_date') ."</b></td>";
	foreach ($providers as $provider) {
		print "<td>";
		if($provider->getLastExportTimestamp() > 0) {
			print date("d.m.Y H:i", $provider->getLastExportTimestamp()) ." ". rex_i18n::msg('d2u_immo_export_uhr');
		}
		print "</td>";
	}
	print "</tr>";print "</thead>";
	print "<tbody>";

	// Only if properties are available
	if(count($properties) > 0) {
		// Possibility to add all properties to export
		print "<tr>";
		print '<td><i>'. rex_i18n::msg('d2u_immo_export_all_online') ."</i></td>";
		foreach ($providers as $provider) {
			print '<td class="rex-table-action"><a href="'. rex_url::currentBackendPage(array('func'=>'all_online', 'provider_id'=>$provider->provider_id))
					.'" class="rex-online"><i class="rex-icon rex-icon-online"></i> '. rex_i18n::msg('status_online') .'</a></td>';
		}
		print "</tr>";
		// Posibility to remove all properties from export
		print "<tr>";
		print "<td><i>". rex_i18n::msg('d2u_immo_export_all_offline') ."</i></td>";
		foreach ($providers as $provider) {
			print '<td class="rex-table-action"><a href="'. rex_url::currentBackendPage(array('func'=>'all_offline', 'provider_id'=>$provider->provider_id))
					.'" class="rex-offline"><i class="rex-icon rex-icon-offline"></i> '. rex_i18n::msg('status_offline') .'</a></td>';
		}
		print "</tr>";
		// How many properties are set for export?
		print "<tr>";
		print "<td><i>". rex_i18n::msg('d2u_immo_export_number_online') ."</i></td>";
		foreach ($providers as $provider) {
			print '<td><i>'. $provider->getNumberOnlineProperties() ."</i></td>";
		}
		print "</tr>";

		foreach($properties as $property) {
			print "<tr>";
			print "<td>". $property->name ."</td>";
			foreach ($providers as $provider) {
				$exported_property = new ExportedProperty($property->property_id, $provider->provider_id);
				if($exported_property->isSetForExport()) {
					print '<td class="rex-table-action"><a href="'. rex_url::currentBackendPage(array('func'=>'offline', 'provider_id'=>$provider->provider_id, 'property_id'=>$property->property_id))
						.'" class="rex-online"><i class="rex-icon rex-icon-online"></i> '. rex_i18n::msg('status_online') .'</a></td>';
				}
				else {
					print '<td class="rex-table-action"><a href="'. rex_url::currentBackendPage(array('func'=>'online', 'provider_id'=>$provider->provider_id, 'property_id'=>$property->property_id))
						.'" class="rex-offline"><i class="rex-icon rex-icon-offline"></i> '. rex_i18n::msg('status_offline') .'</a></td>';
				}
			}
			print "</tr>";
		}
	}
	print "</tbody>";
}
else {
	print '<tr><th><b>'. rex_i18n::msg('d2u_immo_export_no_providers_found') .'</b></th></tr>';
}
print "</table>";