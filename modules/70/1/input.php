<div class="row">
	<div class="col-xs-4">Art der Karte:</div>
	<div class="col-xs-8">
		<?php 
			$map_types = ["osm" => "OpenStreetMap". (rex_addon::get('osmproxy')->isAvailable() ? "" : " (osmproxy Addon muss noch installiert werden)"), "google" => "Google Maps"];

			if(count($map_types) > 0) {
				print ' <select name="REX_INPUT_VALUE[1]" class="form-control">';
				foreach ($map_types as $map_type_id => $map_type_name) {
					echo '<option value="'. $map_type_id .'" ';

					if ("REX_VALUE[1]" == $map_type_id) {
						echo 'selected="selected" ';
					}
					echo '>'. $map_type_name .'</option>';
				}
				print '</select>';
			}
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