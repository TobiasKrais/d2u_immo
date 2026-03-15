<h2>Support</h2>
<p>Fehlermeldungen bitte im <a href="https://github.com/TobiasKrais/d2u_immo" target="_blank">GitHub Repository</a> melden.</p>

<h2>Changelog</h2>

<p>1.4.0-DEV:</p>
<ul>
	<li>Wichtige Hinweise
		<ul>
			<li>Die Klassen stehen jetzt im Namespace <code>TobiasKrais\D2UImmo</code> zur Verfügung.</li>
			<li>Der bisherige Namespace <code>D2U_Immo</code> und die alten globalen Klassennamen sind nur noch als deprecated Übergangsschicht vorhanden und werden mit Version 2.0.0 entfernt.</li>
			<li>Folgende Klassen wurden umbenannt und stehen künftig unter diesen neuen Namen zur Verfügung:
				<ul>
					<li><code>D2U_Immo\AExport</code> wird zu <code>TobiasKrais\D2UImmo\AExport</code>.</li>
					<li><code>D2U_Immo\AFTPExport</code> wird zu <code>TobiasKrais\D2UImmo\AFTPExport</code>.</li>
					<li><code>D2U_Immo\Advertisement</code> wird zu <code>TobiasKrais\D2UImmo\Advertisement</code>.</li>
					<li><code>D2U_Immo\Category</code> wird zu <code>TobiasKrais\D2UImmo\Category</code>.</li>
					<li><code>D2U_Immo\Contact</code> wird zu <code>TobiasKrais\D2UImmo\Contact</code>.</li>
					<li><code>D2U_Immo\ExportedProperty</code> wird zu <code>TobiasKrais\D2UImmo\ExportedProperty</code>.</li>
					<li><code>D2U_Immo\FrontendHelper</code> wird zu <code>TobiasKrais\D2UImmo\FrontendHelper</code>.</li>
					<li><code>D2U_Immo\ImportCronjob</code> wird zu <code>TobiasKrais\D2UImmo\ImportCronjob</code>.</li>
					<li><code>D2U_Immo\ImportOpenImmo</code> wird zu <code>TobiasKrais\D2UImmo\ImportOpenImmo</code>.</li>
					<li><code>D2U_Immo\OpenImmo</code> wird zu <code>TobiasKrais\D2UImmo\OpenImmo</code>.</li>
					<li><code>D2U_Immo\Property</code> wird zu <code>TobiasKrais\D2UImmo\Property</code>.</li>
					<li><code>D2U_Immo\Provider</code> wird zu <code>TobiasKrais\D2UImmo\Provider</code>.</li>
					<li><code>d2u_immo_export_cronjob</code> wird zu <code>TobiasKrais\D2UImmo\ExportCronjob</code>.</li>
					<li><code>d2u_immo_frontend_helper</code> wird zu <code>TobiasKrais\D2UImmo\FrontendHelper</code>.</li>
					<li><code>d2u_immo_lang_helper</code> wird zu <code>TobiasKrais\D2UImmo\LangHelper</code>.</li>
					<li><code>D2UImmoModules</code> wird zu <code>TobiasKrais\D2UImmo\Module</code>.</li>
					<li><code>export_lang_helper</code> wird zu <code>TobiasKrais\D2UImmo\LangHelper</code>.</li>
				</ul>
			</li>
			<li>Die Klassen-Dateien im lib-Verzeichnis wurden auf CamelCase-Dateinamen umgestellt. Export- und Import-Klassen liegen jetzt zusätzlich in den Unterverzeichnissen <code>lib/export</code> und <code>lib/import</code>.</li>
			<li>Die Rückwärtskompatibilität für alte Klassennamen wird vorübergehend über <code>lib/deprecated_classes.php</code> bereitgestellt.</li>
		</ul>
	</li>
	<li>Vorbereitung auf Redaxo 6: Plugins in Hauptverzeichnis verschoben.</li>
	<li>Neue Einstellung für eine Widerrufsbelehrung ergänzt und vor dem Exposé-Druck in den Modulen 70-1 und 70-4 als Hinweisdialog verlinkt.</li>
	<li>Immobilienausgabe ergänzt um die berechnete Energieeffizienzklasse in Listen- und Detailansicht der Module 70-1 und 70-4.</li>
	<li>Neue Module 70-4 bis 70-6 als Bootstrap-5-Varianten der bestehenden Beispielmodule hinzugefügt.</li>
	<li>Module 70-1 bis 70-3 als "(BS4, deprecated)" markiert. Die BS4-Varianten werden im nächsten Major Release entfernt.</li>
	<li>BS5-Module auf Bootstrap-5-Tabs und die d2u_helper Lightbox umgestellt.</li>
	<li>Benötigt d2u_helper &gt;= 2.1.0.</li>
	<li>Bugfix Import Plugin: Reserviert wurde nicht korrekt importiert.</li>
	<li>Bugfix: Module wurden beim Update nicht korrekt aktualisiert.</li>
	<li>Bugfix Export Plugin: SSH Upload als Fallback wenn FTP Upload fehlschlägt.</li>
	<li>Bugfix: Grundstücke und Parkplätze konnten nicht gespeichert werden.</li>
</ul>
<p>1.3.1:</p>
<ul>
	<li>Modul 70-1 "D2U Immo Addon - Hauptausgabe": Fehler bei der Ausgabe der Courtage behoben.</li>
	<li>Import Plugin: Bugfix bei deutscher Datumseingabe des Energiepasses und Endenergiebedarfs, denkmalgeschützten Immobilien, der Ausstattung und dem Verkaufstatus. Außerdem 2 PHP Warnungen entfernt.</li>
	<li>Spaltenüberschriften in Übersichtslisten sortierbar gemacht.</li>
	<li>Buttons zum Sortieren der Prioritäten in der Übersichtsliste der Immobilien hinzugefügt.</li>
	<li>Anpasssungen an kommende Version von D2U Helper 2.x.</li>
</ul>
<p>1.3.0:</p>
<ul>
	<li>Import Plugin hinzugefügt: es ist nun möglich OpenImmo ZIP Dateien zu importieren.</li>
	<li>Export Plugin: Linkedin Export entfernt, da die Linkedin API V1 schon länger nicht mehr Unterstützt wurde.</li>
	<li>Neue Felder Denkmalgeschützt, Jahrgang des Energieausweises und Kaufpreis auf Anfrage hinzugefügt.</li>
	<li>Hinweistext zur Bestimmung der Längen- und Breitengrade eines Objekts verbessert, wenn Google Maps API Key nicht im D2U Helper Addon eingetragen ist.</li>
	<li>Nutzt das neue Bilderliste Feld mit Vorschaufunktion der Bilder.</li>
	<li>README hinzugefügt.</li>
	<li>Bugfix: Wohnungstyp für Etagenwohnungen war inkorrekt in der Datenbank gespeichert.</li>
	<li>Bugfix: Mehrsprachige Seiten enthielten einen Fehler bei der Anzeige der Übersetzungsaktion.</li>
	<li>Bugfix: aktive Checkboxen in den Einstellungen wurden nicht als aktiviert angezeigt.</li>
	<li>Modul 70-1 "D2U Immo Addon - Hauptausgabe": Fehler im Spamschutz / CSRF Schutz behoben.</li>
	<li>Modul 70-1 "D2U Immo Addon - Hauptausgabe": Nun kompatibel mit Geolocation Addon Version 1 und auch 2.</li>
</ul>
<p>1.2.0:</p>
<ul>
	<li>Feld für 360° Bilder hinzugefügt. Der Export von 360° Bildern kann in den Einstellungen jedes Portals manuell aktiviert werden.</li>
	<li>Feld "Property::rent_plus_vat" war seit Version 1.1.1 deprecated und ist nun entfernt.</li>
	<li>Twitter Support endgültig entfernt.</li>
	<li>Ca. 1400 rexstan Level 9 Anpassungen.</li>
	<li>Modul 70-1 "D2U Immo Addon - Hauptausgabe" CSS Scope auf Modul eingegrenzt und 360° Bilder hinzugefügt.</li>
	<li>Modul 70-2 "D2U Immo Addon - Infobox Ansprechpartner" rexstan Anpassungen.</li>
	<li>Modul 70-3 "D2U Immo Addon - Ausgabe Kategorie" rexstan Anpassungen.</li>
</ul>
<p>1.1.7:</p>
<ul>
	<li>PHP-CS-Fixer Code Verbesserungen.</li>
	<li>.github Verzeichnis aus Installer Action ausgeschlossen.</li>
	<li>Erste rexstan Anpassungen</li>
	<li>Modul 70-1 "D2U Immo Addon - Hauptausgabe" kann jetzt auch Geolocation Karten verwenden. Außerdem Formular mit Formularnamen versehen um bessere YForm Spamprotection Kompatibilität bei mehreren Formularen auf einer Seite herzustellen.</li>
	<li>Modul 70-3 "D2U Immo Addon - Ausgabe Kategorie" Bugfix. Überflüssiger Code hatte in Verbindung mit dem URL Addon 2 zu Fehler geführt.</li>
</ul>
<p>1.1.6:</p>
<ul>
	<li>Anpassungen an Publish Github Release to Redaxo.</li>
	<li>Methode d2u_immo_frontend_helper::getMetaTags() entfernt, da das URL Addon eine bessere Funktion anbietet.
		Ebenso die Methoden getMetaAlternateHreflangTags(), getMetaDescriptionTag(), getCanonicalTag und getTitleTag() der aller Klassen, die diese Methoden angeboten hatten.</li>
	<li>Konvertiert die Wohnfläche vor dem Speichern in eine Zahl.</li>
	<li>HTML Entities (codiertes ä, ö, ü, ...) werden entfernt, da nicht alle Immobilienportale diese decodieren können.</li>
	<li>Unterstützt nur noch URL Addon >= 2.0.</li>
	<li>Bugfix: Beim Löschen von Artikeln und Medien die vom Addon verlinkt werden wurde der Name der verlinkenden Quelle in der Warnmeldung nicht immer korrekt angegeben.</li>
	<li>Modul 70-1 "D2U Immo Addon - Hauptausgabe" gibt die Adresse beim Drucken nicht mehr aus wenn die Adresse nicht veröffentlicht werden soll.</li>
	<li>Modul 70-1 "D2U Immo Addon - Hauptausgabe" kann auch OpenStreetMap Karte statt Google Maps verwenden.</li>
</ul>
<p>1.1.5:</p>
<ul>
	<li>Kontakte können geklont werden.</li>
	<li>Benötigt Redaxo >= 5.10, da die neue Klasse rex_version verwendet wird.</li>
	<li>Fehler behoben, wenn bei einer Immobilie in der Übersichtsliste auf offline geklickt wurde.</li>
	<li>Modul 70-1 "D2U Immo Addon - Hauptausgabe" leitet Offlineobjekte auf die Fehlerseite weiter und wurde an YForm 3.4 angepasst.</li>
	<li>Modul 70-2 "D2U Immo Addon - Infobox Ansprechpartner" hat bei Klick auf E-Mail nicht mehr das Angebotstab angezeigt.</li>
	<li>Modul 70-2 "D2U Immo Addon - Infobox Ansprechpartner" hat kann jetzt auf Fallbackkontakt verzichten.</li>
	<li>Fehler entfernt wenn in den Einstellungen kein Artikel gesetzt ist.</li>
	<li>Notice beim Speichern der Einstellungen entfernt.</li>
	<li>Schreibfehler Einbauküche behoben.</li>
	<li>Export Plugin: Facebook Export (Schnittstelle war veraltet) und ImmoScout24 XML (Schnittstelle eingestellt) endgültig entfernt.</li>
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