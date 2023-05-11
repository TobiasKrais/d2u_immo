<?php

use D2U_Immo\ImportOpenImmo;

ImportOpenImmo::autoimport();

//$logFile = rex_logger::getPath();
$importFilePath = $this->getDataPath() . 'input/ftp_in/';
$backupFilePath = $this->getDataPath() . 'input/ftp_in/BACKUP/';
//$import_files = array_slice(scandir($importFilePath),2);
$first_files    = glob($importFilePath . '*.{zip,ZIP}', GLOB_BRACE);
$second_files   = glob($backupFilePath . '*.{zip,ZIP}', GLOB_BRACE);


$import_files = array();
$backup_files = array();

foreach ($first_files as $clean) {
    $clean = str_replace(rex_path::pluginData('d2u_immo', 'import') . 'input/ftp_in/', '', $clean);
    //echo $clean;
    array_push($import_files, $clean);
}

foreach ($second_files as $clean2) {
    $clean2 = str_replace(rex_path::pluginData('d2u_immo', 'import') . 'input/ftp_in/BACKUP/', '', $clean2);
    //echo $clean;
    array_push($backup_files, $clean2);
}


/*
echo '<pre>';
print_r( $import_files );
echo '</pre>';
*/

//echo checkImmoObject("OLAG20181103120614000FZYYCG58RM"); 


if (count($import_files) >= 1) {
    $i           = 0;
    $table_array = array();
    foreach ($import_files as $zip) {
        
        if (is_file($importFilePath . $zip) ) {
            
			$orig_filename = $zip;
            $zip_size = round(filesize($importFilePath . $zip) / 1000000, 3);
			
			$vollablgeich = '';
			if(strpos($zip, '__')) {
				$zip = str_replace('__','_', $zip);
				$vollabgleich = ' - Vollabgleich';
				}
            
            $zip_date = explode('_', $zip);
            $zip_date = explode('.zip', $zip_date[2]);
            $zip_date = $zip_date[0];
            
            $zip_length      = strlen($zip_date);
            $zip_date_year   = substr($zip_date, 0, 4);
            $zip_date_month  = substr($zip_date, 4, 2);
            $zip_date_day    = substr($zip_date, 6, 2);
            $zip_date_hour   = substr($zip_date, 8, 2);
            $zip_date_minute = substr($zip_date, 10, 2);
            $zip_date_second = substr($zip_date, 12, 2);
			
			$zip_sort = str_replace('_','',$zip);
			$zip_sort = str_replace('.zip','',$zip_sort);
			$zip_sort = str_replace('54015IMMONET','',$zip_sort);
            
            $zip_date_full = $zip_date_day . '.' . $zip_date_month . '.' . $zip_date_year . ' - ' . $zip_date_hour . ':' . $zip_date_minute . ':' . $zip_date_second . ' Uhr';
            // Tatsächliche Änderung der Datei - muss aber nicht stimmen, deshalb aus Filename ermitteln
            //$zip_date_full = filemtime($importFilePath.$zip);
			
            
			$zip_integrity = ' <span style="color:#c7254e;"><i class="rex-icon fa-times-circle"></i> <small>Datei ist beschädigt / noch nicht vollständig</small></span>';	
			if(OPfile::zipCheck($importFilePath, $orig_filename) === TRUE ) {
				$zip_integrity = ' <span style="color:#3bb594;"><i class="rex-icon fa-check-circle"></i> <small>Datei ist unbeschädigt / vollständig</small></span>';	
				}	
            $table_content = '<tr id=' . $zip_sort . '>
            <td>' . $zip_date_full . $vollabgleich.'<br /><span style="color:#ccc;"><small>Änderungsdatum auf dem Server:<br /> ' . date('d.m.Y - H:i:s', filemtime($importFilePath . $orig_filename)) . ' Uhr</small></span></td>
            <td><a href="'.rex_media_manager::getUrl('download_import_zip',$orig_filename).'" target="_blank">' . $orig_filename . '</a><br>'.$zip_integrity.'</td>
            <td>' . $zip_size . ' MB</td>
            <td class="rex-table-action"><a href="../index.php?rex-api-call=import_start&filename=' . $orig_filename . '" class="btn btn-success" target="_blank">Manuell importieren <i class="fa fa-download"></i></a>
            </td>
            </tr>';
            //array_push($table_array, $table_content);
            $table_array = OPfile::array_push_assoc($table_array, $zip_sort, $table_content);
            
            $i++;
        } // EoF if nur die Ordner
    } // Eof Foreach
    
} // EoF if count / welche vorhanden

else {
    $table_content = '<tr>
        <td><em>Keine Importdatei verfügbar.</em></td>
        </tr>';
    $table_array   = OPfile::array_push_assoc($table_array, $zip_sort, $table_content);
} // EoF else / keine vorhanden


/*
 * Nochmal eine Tabelle für die Backup-Dateien
 */
if (count($backup_files) >= 1) {
    $j           = 0;
    $backup_array = array();
    foreach ($backup_files as $zip2) {
        
        if (is_file($backupFilePath . $zip2)) {
            
			$orig_filename2 = $zip2;
            $zip_size2 = round(filesize($backupFilePath . $zip2) / 1000000, 3);
            
			$vollabgleich2 = '';
			if(strpos($zip2, '__')) {
				$zip2 = str_replace('__','_', $zip2);
				$vollabgleich2 = ' - Vollabgleich';
				}
				
            $zip_date2 = explode('_', $zip2);
            $zip_date2 = explode('.zip', $zip_date2[2]);
            $zip_date2 = $zip_date2[0];
            
            $zip_length2      = strlen($zip_date2);
            $zip_date_year2   = substr($zip_date2, 0, 4);
            $zip_date_month2  = substr($zip_date2, 4, 2);
            $zip_date_day2    = substr($zip_date2, 6, 2);
            $zip_date_hour2   = substr($zip_date2, 8, 2);
            $zip_date_minute2 = substr($zip_date2, 10, 2);
            $zip_date_second2 = substr($zip_date2, 12, 2);
			
			$zip_sort2 = str_replace('_','',$zip2);
			$zip_sort2 = str_replace('.zip','',$zip_sort2);
			$zip_sort2 = str_replace('54015IMMONET','',$zip_sort2);
            
            $zip_date_full2 = $zip_date_day2 . '.' . $zip_date_month2 . '.' . $zip_date_year2 . ' - ' . $zip_date_hour2 . ':' . $zip_date_minute2 . ':' . $zip_date_second2 . ' Uhr';
            // Tatsächliche Änderung der Datei - muss aber nciht stimmen, deshalb aus Filename ermitteln
            //$zip_date_full = filemtime($importFilePath.$zip);
            
			$zip_integrity2 = ' <span style="color:#c7254e;"><i class="rex-icon fa-times-circle"></i> <small>Datei ist beschädigt / noch nicht vollständig</small></span>';	
			if(OPfile::zipCheck($backupFilePath, $orig_filename2) === TRUE) {
				$zip_integrity2 = ' <span style="color:#3bb594;"><i class="rex-icon fa-check-circle"></i> <small>Datei ist unbeschädigt / vollständig</small></span>';	
				}
				 
            $table_content2 = '<tr id=backup-' . $zip_sort2 . '>
            <td>' . $zip_date_full2 . $vollabgleich2.'<br /><span style="color:#ccc;"><small>Änderungsdatum auf dem Server:<br /> ' . date('d.m.Y - H:i:s', filemtime($backupFilePath . $orig_filename2)) . ' Uhr</small></span></td>
            <td><a href="'.rex_media_manager::getUrl('download_backup_zip',$orig_filename2).'" target="_blank">' . $orig_filename2. '</a><br>'.$zip_integrity2.'</td>
            <td>' . $zip_size2 . ' MB</td>
            <td class="rex-table-action"><a href="../index.php?rex-api-call=backup_rollback&backup_file=' . $orig_filename2 . '" class="btn btn-setup" target="_blank">Datei zurücklegen <i class="fa fa-upload"></i></a>
            </td>
			
            </tr>';
            //array_push($table_array, $table_content);
            $backup_array   = OPfile::array_push_assoc($backup_array, $zip_sort2, $table_content2);
            $j++;
        } // EoF if nur die Ordner
    } // Eof Foreach
    
} // EoF if count / welche vorhanden

else {
    $table_content2 = '<tr>
        <td><em>Keine Backupdatei verfügbar.</em></td>
        </tr>';
    $backup_array   = OPfile::array_push_assoc($backup_array, $zip_sort2, $table_content2);
} // EoF else / keine vorhanden
//dump($backup_array);
print '<h3>Auf dem Server verfügbare Importdateien, die noch nicht importiert wurden ('.count($import_files).')</h3>
<table class="table table-striped table-hover">';
print "<thead>";
print "<tr>";
print "<th><b>Datum</b></th>";
print "<th><b>Dateiname</b></th>";
print "<th><b>Größe</b></th>";
print "<th><b>Importieren</b></th>";
print "</tr>";
print "</thead>";
print "<tbody>";

ksort($table_array); // Nach Datum sortieren - das passiert alphabetisch
$table_array = array_reverse($table_array); // Array umdrehen, damit die neusten oben stehen
foreach ($table_array as $table_row) {
    print $table_row;
}

print "</tbody>";
print "</table>";
?>


<hr />


<div style="opacity: 0.5;">
<h3>Dateien, die bereits importiert wurden und in <code>BACKUP</code> liegen (<?= count($backup_files); ?>)</h3>
<?php

print '<table class="table table-striped table-hover">';
print "<thead>";
print "<tr>";
print "<th><b>Datum</b></th>";
print "<th><b>Dateiname</b></th>";
print "<th><b>Größe</b></th>";
print "<th><b>Zurücklegen</b></th>";
print "</tr>";
print "</thead>";
print "<tbody>";

ksort($backup_array); // Nach Datum sortieren - das passiert alphabetisch
$backup_array = array_reverse($backup_array); // Array umdrehen, damit die neusten oben stehen
//dump($backup_array);
foreach ($backup_array as $backup_row) {
    print $backup_row;
}

print "</tbody>";
print "</table>";
?>

</div>



<h3>Immobilien KOMPLETT aus DB löschen</h3>
<p><code>Immobilien, Immobilien-Texte, DB-Einträge der Bilder der gewählten Immobilien-Kategorie, Media-Files werden gelöscht</code></p> 
<a href="../index.php?rex-api-call=clear_immo_db" class="btn btn-delete" target="_blank">Alle Immobilien aus DB löschen <i class="fa fa-trash"></i></a><br /><br />
  
<button>Click</button>



<div id="div1"></div>

 <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog" style="background-color: rgba(0,0,0,0.5);" >
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
        </div>
        <div class="modal-body">
          Inhalt
        </div>
      </div>
    </div>
  </div> 
  

<script>
$( document ).ready(function() {

	$("button").click(function(){
		$("#myModal").modal()

		$.ajax({url: "foo", success: function(result){
			$("#div1").html(result);
		}});
	});

});	
</script>