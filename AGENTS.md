# D2U Immo - Agent Notes

Nur projektspezifische Regeln, die für KI-Arbeit relevant sind.

## Kernregeln

- Namespace für Addon-Klassen: `TobiasKrais\D2UImmo`
- Veralteter Namespace für Rückwärtskompatibilität: `D2U_Immo`
- Einrückung: 4 Spaces in PHP-Klassen, Tabs in Moduldateien
- Kommentare nur auf Englisch
- Frontend-Labels über `Sprog\Wildcard::get()`, Backend-Labels über `rex_i18n::msg()` mit Keys aus `lang/`

## Wichtige Projekthinweise

- Backend-Translation-Keys müssen in allen Sprachdateien unter `lang/` synchron bleiben. Aktuell: `de_de`, `nl_nl`.
- Wenn Module unter `modules/70/*` geändert werden, Changelog in `pages/help.changelog.php` prüfen oder aktualisieren.
- Die Revisionsnummer in `lib/Module.php` nur einmal pro Release erhöhen. Wenn die Zielversion im Changelog bereits `-DEV` trägt, innerhalb derselben Entwicklungsphase nicht erneut hochzählen.
- In Changelog-Dateien, AGENTS.md und README.md sind Umlaute erlaubt und müssen nicht auf ASCII umgeschrieben werden.

## Pflege

- Diese Datei kurz und handlungsorientiert halten.
- Neue Einträge nur aufnehmen, wenn sie wiederkehrende Stolperfallen, verbindliche Projektkonventionen oder agentenrelevante Workflows betreffen.
