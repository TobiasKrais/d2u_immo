<div class="row">
	<div class="col-xs-4">Art der Karte:</div>
	<div class="col-xs-8">
		<?php
            $map_types = [];
            if (rex_addon::get('geolocation')->isAvailable()) {
                $map_types['geolocation'] = 'Geolocation Addon: Standardkarte';
                $mapsets = [];
                if(rex_version::compare('2.0.0', rex_addon::get('geolocation')->getVersion(), '<=')) {
                    // Geolocation 2.x
                    $mapsets = \FriendsOfRedaxo\Geolocation\Mapset::query()
                        ->orderBy('title')
                        ->findValues('title', 'id');
                }
                else {
                    // Geolocation 1.x
                    $mapsets = \Geolocation\mapset::query()
                        ->orderBy('title')
                        ->findValues('title', 'id');
                }
                foreach ($mapsets as $id => $name) {
                    $map_types[$id] = 'Geolocation Addon: '. $name;
                }
            }
            if (rex_addon::get('osmproxy')->isAvailable()) {
                $map_types['osm'] = 'OSM Proxy Addon: OpenStreetMap Karte';
            }
            $map_types['google'] = 'Google Maps'. ('' !== rex_config::get('d2u_helper', 'maps_key', '') ? '' : ' (in den Einstellung des D2U Helper Addons muss hierfür noch ein Google Maps API Key eingegeben werden)');

            echo '<select name="REX_INPUT_VALUE[1]" class="form-control">';
            foreach ($map_types as $map_type_id => $map_type_name) {
                echo '<option value="'. $map_type_id .'"';

                if ('REX_VALUE[1]' === (string) $map_type_id) {
                    echo ' selected="selected" ';
                }
                echo '>'. $map_type_name .'</option>';
            }
            echo '</select>';
        ?>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">&nbsp;</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<br>
		Alle Einstellungen können im <a href="index.php?page=d2u_immo">
				D2U Immo Addon</a> vorgenommen werden.
	</div>
</div>