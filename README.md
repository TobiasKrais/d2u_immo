# Redaxo 5 Immobilienverwaltung

Mehrsprachig Immobilienverwaltung für Redaxo. Es wird nicht der volle OpenImmo Funktionsumfang für Immobilien dargestellt. Erweiterungen können gerne in Auftrag gegeben werden. Demoseite: <https://test.design-to-use.de/de/addontests/d2u-immobilien/>

## Installation

Nach der Installation in Redaxo sollten folgende Schritte ausgeführt werden:

1. Festlegen der Einstellungen im Addon.
2. Eingabe mindestens eines Kontaktes.
3. Eingabe mindestens einer Kategorie. Diese werden für die Generierung der URLs benötigt.
4. Nun kann mit der Eingabe von Immobilien begonnen werden.
5. Wenn gewünscht, kann unter Setup eines der auf Bootstrap 4 basierenden Beispielmodule installiert werden.

## Plugins

Es exisitieren 3 Plugins, die nachfolgend kurz beschrieben werden.

### Export Plugin (export)

Mit diesem Plugin können Immobilien aus der Redaxo Installation auf andere Portale übertragen werden. Zur Zeit steht der OpenImmo Export und der Export auf LinkedIn als Post zur Verfügung.

Nach der Installation sollten zuerst die Einstellungen festgelegt und dann die Portale konfiguriert werden. Danach können unter "Export" Immobilien für den Export als "online" markiert werden. Diese werden beim nächsten Export übertragen. Ein Export kann entweder manuell oder per Cronjob im Cronjob Addon durchgeführt werden. Der Cronjob kann in den Einstellungen installiert werden.

### Import Plugin (import)

Mit diesem Plugin können Immobilien in eine Redaxo Installation importiert werden.

Nach der Installation sollten zuerst die Einstellungen festgelegt werden. Der Pfad des Import Verzeichnisses liegt dabei im Redaxo Wurzelverzeichnis. Es empfiehlt sich in dieses Verzeichnis zu schützen um den Download der zu importierenden Dateien durch Dritte zu verhindern. Hier eine für diesen Zweck geeignete .htaccess Datei:

```Apache
Order deny,allow
deny from all
```

Nachdem eine OpenImmo Datei im ZIP Format in den Import Ordner hochgeladen wurde, kann sie manuell auf der Importseite importiert werden. Nach dem Import wird sie in den Datenbereich des Addons verschoben. Auf der Logseite können die letzten 10 Importe und zugehörigen Logdateien angesehen, bzw. heruntergeladen werden. Ein Import per Cronjob ist möglich. Dieser kann in den Einstellungen installiert werden und wird standartmäßg alle 5 Minuten ausgeführt. Ein Importlog wird an die hinterlegte E-Mail-Adresse gesendet.

Beim Import wird zu jeder Immobilie die OpenImmo Import Anbieter ID gespeichert. Anhand dieser Anbieter ID werden zu löschende Immobilien identifiziert. So werden bei einem Vollimport nicht mehr im Import enthaltene Immobilien des Anbieters gelöscht. Damit verbundene Medien und Kontakte werden ebenfalls gelöscht, sofern sie nicht andersweitig in Gebrauch sind. Dank dieser Funktion ist es möglich, Immobilien mehrerer Anbieter darzustellen, aktuell zu halten und somit ein eigenes Immobilienportal aufzubauen, das sich auch selbst aufräumt.

### Schaufenterwerbung Plugin (window_advertising)

Mit diesem Plugin können Immobilien und kurze Werbetexte im Kioskmodus eines Browsers in Dauerschleife dargestellt werden. Ein Wort zur Vorsicht: der Bildschirm sollte nicht zu nah hinter einer Schaufensterscheibe mit Sonneneinstrahlung positioniert werden, da die Scheibe sonst reißen kann.

Um dieses Plugin nutzen zu können, sollten Sie zuerst die Einstellungen festlegen. Ein Redaxo Template für Schaufensterwerbung steht im D2U Helper Addon unter Templates zur Verfügung. Dieses Template sollte dem Artikel, der in den Einstellungen festgelegt wird zugewiesen sein. In dem Artikel braucht es kein Modul. Die komplette Ausgabe wird über das Template gesteuert.