<?php
/*
 * Modules
 */
$d2u_module_manager = new D2UModuleManager(D2UImmoModules::getModules(), "modules/", "d2u_immo");

// D2UModuleManager actions
$d2u_module_id = rex_request('d2u_module_id', 'string');
$paired_module = rex_request('pair_'. $d2u_module_id, 'int');
$function = rex_request('function', 'string');
if($d2u_module_id != "") {
	$d2u_module_manager->doActions($d2u_module_id, $function, $paired_module);
}

// D2UModuleManager show list
$d2u_module_manager->showManagerList();

/*
 * Templates
 */
?>
<h2>Template</h2>
<p>Beispielseiten</p>
<ul>
	<li>Immobilien Addon: <a href="https://www.immobiliengaiser.de/" target="_blank">
		ImmobilienGaiser</a>.</li>
</ul>
<p>Im D2U Helper Addon kann das "Header Pic Template - 2 Columns" installiert werden. In
	diesem Template sind alle nötigen Anpassungen integriert.</p>
<h2>Support</h2>
<p>Fehlermeldungen bitte im <a href="https://github.com/TobiasKrais/d2u_immo" target="_blank">GitHub Repository</a> melden.</p>
<h2>Changelog</h2>

<p>1.1.5-DEV:</p>
<ul>
	<li>Notice beim Speichern der Einstellungen entfernt.</li>
	<li>Schreibfehler Einbauküche behoben.</li>
	<li>Export Plugin: Facebook Export endgültig entfernt.</li>
	<li>Backend: Beim online stellen eines Objekts in der Objektliste gab es beim Aufruf im Frontend einen Fatal Error, da der URL cache nicht neu generiert wurde.</li>
</ul>
<p>1.1.4:</p>
<ul>
	<li>Backend: Einstellungen und Setup Tabs rechts eingeordnet um sie vom Inhalt besser zu unterscheiden.</li>
	<li>Anpassungen an neueste Version des URL Addons Version 2.</li>
	<li>Bugfix: das Löschen eines Bildes im Medienpool wurde unter Umständen mit der Begründung verhindert, dass das Bild in Benutzung sei, obwohl das nicht der Fall war.</li>
	<li>Modul 70-1: Exposé beinhaltet jetzt Telefonnummer und E-Mail des Kontakts. Falls ein PDF Dokument im Medienpool keine Bezeichnung hat, wird nun der Name der Datei ausgegeben.</li>
	<li>Kontaktformular funktioniert nun auch wenn über Office 365 versendet wird.</li>
	<li>Bugfix: wenn Schaufenster Plugin nicht installiert war, kam es beim Speichern einer Immobilie zu einem fatal error.</li>
</ul>
<p>1.1.3:</p>
<ul>
	<li>Bugfix: Fatal error beim Speichern verursacht durch die URL Addon Version 2 Anpassungen behoben.</li>
	<li>Bugfix: bei Installation des Export Plugins wurde ein Datenbankfeld vergessen anzulegen.</li>
</ul>
<p>1.1.2:</p>
<ul>
	<li>Modul 70-1 prüft bei aktiviertem YCom ob Downloadrechte für Dateien bestehen.</li>
	<li>Bild in sitemap.xml aufgenommen.</li>
	<li>Anpassungen an URL Addon 2.x.</li>
</ul>
<p>1.1.1:</p>
<ul>
	<li>Kaufpreis kann jetzt auch zzgl. MwSt. angegeben werden (ACHTUNG: Feld "Property::rent_plus_vat" wird in "Property::price_plus_vat" umbenannt. "Property::rent_plus_vat" ist nun deprecated.).</li>
	<li>Listen im Backend werden jetzt nicht mehr in Seiten unterteilt.</li>
	<li>YRewrite Multidomain support.</li>
	<li>Aus OpenImmo Export 'alter alter_attr="NEUBAU/ALTBAU"' entfernt, da bei Nutzern die Aussage (ALTBAU = vor 1945, NEUBAU = nach 1945) zu Irritationen führte.</li>
	<li>Energieausweis wird bei "Zustand des Objektes" "Projektiert" nicht mehr erzwungen.</li>
	<li>Konvertierung der Datenbanktabellen zu utf8mb4.</li>
</ul>
<p>1.1.0:</p>
<ul>
	<li>Anpassungen an YForm 3.</li>
</ul>
<p>1.0.9:</p>
<ul>
	<li>Für Objektart Grundstück und Parken werden nicht benötigte Felder im Backend ausgeblendet.</li>
	<li>Sprachdetails werden ausgeblendet, wenn Speicherung der Sprache nicht vorgesehen ist.</li>
	<li>Bugfix: Prioritäten wurden beim Löschen nicht reorganisiert.</li>
	<li>Bugfix OpenImmo Export: Kaltmiete wurde nicht übermittelt.</li>
	<li>Kaution wurde in einen Zahlenwert geändert um mit dem OpenImmo Export kompatibel zu sein. ACHTUNG: Texteingaben in dem Feld gehen verloren! Eventuell vor dem Update die Werte manuell anpassen.</li>
</ul>
<p>1.0.8:</p>
<ul>
	<li>Unterstützung von OpenImmo Objektart Parken (für Garagen, Stellplätze, ...).</li>
	<li>Bei der Eingabe einer Adresse gibt es jetzt die Möglichkeit eine Adresse direkt zu geocodieren wenn im D2U Helper Addon ein Google Maps API Key mit Zugriff auf die Geocoding API hinterlegt ist.
		Geocodierte Adressen werden auf der Karte schneller geladen und belasten das Budget des Google Kontos weniger.</li>
</ul>
<p>1.0.7:</p>
<ul>
	<li>Bugfix: Deaktiviertes Addon zu deinstallieren führte zu fatal error.</li>
	<li>In den Einstellungen gibt es jetzt eine Option, eigene Übersetzungen in SProg dauerhaft zu erhalten.</li>
	<li>Bugfix: CronJob wird - wenn installiert - nicht immer richtig aktiviert.</li>
	<li>Flächenangaben können jetzt auch mit einem Komma gemacht werden.</li>
	<li>Nicht zutreffende Felder in Eingabemaske der Immobilien werden jetzt ausgeblendet statt ausgegraut.</li>
	<li>Unterstützung von Openimmo Objektart Lager, Halle, Produktion.</li>
	<li>Feld "Miete zzgl. MwSt." für gewerbliche Mietobjekte hinzugefügt.</li>
	<li>Modul 70-1 zeigt die Mieterselbstauskunft jetzt nicht mehr bei Nutzungsart Gewerbe an.</li>
</ul>
<p>1.0.6:</p>
<ul>
	<li>Bugfix: Updatefehler behoben.</li>
	<li>Bugfix: Kategorie des Objekts wurde nicht korrekt ausgelesen.</li>
	<li>Methode zum Erstellen von Meta Tags d2u_immo_frontend_helper::getAlternateURLs() hinzugefügt.</li>
	<li>Methode zum Erstellen von Meta Tags d2u_immo_frontend_helper::getMetaTags() hinzugefügt.</li>
</ul>
<p>1.0.5:</p>
<ul>
	<li>Formulare um zeitverzögerten Spamschutz (10 Sekunden) ergänzt.</li>
	<li>Formular um Einwilligung zur telefonischen Kontaktaufnahme ergänzt.</li>
	<li>Bugfix: Fatal Error beim Löschen eines Kontaktes behoben.</li>
	<li>Bugfix: Übersetzung Datenschutzerklärung Formular aus Gästebuch Addon übernommen.</li>
	<li>Bugfix: Style Kompatibilität zu D2U Gästebuch Tabs hergestellt.</li>
</ul>
<p>1.0.4:</p>
<ul>
	<li>Feld Datenschutzerklärung akzeptiert im Frontend Formular hinzugefügt.</li>
	<li>Fehler beim Speichern von einfachen Anführungszeichen behoben.</li>
	<li>YRewrite Multidomain Anpassungen.</li>
	<li>Export Plugin: Portale können offline geschaltet werden.</li>
	<li>Export Plugin: Bei Installation des Autoexportes künftig Ausführung im Frontend und Backend.</li>
	<li>Export Plugin: Code hatte seit Einführung des namespace nicht mehr funktioniert.</li>
	<li>Downloads, die durch ycom/media_auth geschützt sind werden nicht mehr angezeigt.</li>
	<li>Anpassungen Druckausgabe an Bootstrap 4.0.0.</li>
</ul>
<p>1.0.3:</p>
<ul>
	<li>Übersetzungshile aus D2U Helper 1.3.0 integriert. ACHTUNG: Namespace hinzugefügt! Eigene Module müssen angepasst werden!</li>
	<li>Update auf URL Addon 1.0.1.</li>
	<li>Bugfix Finanzierungsrechner bei Summen über 1.000.000,-.</li>
	<li>Bugfix wenn Standardsprache die zweite Sprache ist.</li>
	<li>Bugfix wenn Standardsprache gelöscht wird.</li>
	<li>Editierrechte für Übersetzer eingeschränkt.</li>
</ul>
<p>1.0.2:</p>
<ul>
	<li>Update auf Bootstrap 4beta.</li>
</ul>
<p>1.0.1:</p>
<ul>
	<li>Bugfix Release, besonders den Export betreffend.</li>
</ul>
<p>1.0.0:</p>
<ul>
	<li>Initiale Version.</li>
</ul>