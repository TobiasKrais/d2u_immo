# D2U Immo - Redaxo Addon

A Redaxo 5 CMS addon for managing real estate properties with OpenImmo compatibility. Includes property listings, categories, contacts, energy certificates, financial calculators, OpenImmo import/export, and window advertising.

## Tech Stack

- **Language:** PHP >= 8.0
- **CMS:** Redaxo >= 5.15.0
- **Frontend Framework:** Bootstrap 4/5 (via d2u_helper templates)
- **Namespace:** `TobiasKrais\D2UImmo`
- **Deprecated Namespace:** `D2U_Immo` (backward compatibility until 2.0.0)

## Project Structure

```text
d2u_immo/
├── boot.php               # Addon bootstrap (extension points, permissions)
├── install.php             # Installation (database tables, views, media manager types, URL profiles)
├── update.php              # Update (calls install.php)
├── uninstall.php           # Cleanup (tables, views, media manager types, URL profiles)
├── package.yml             # Addon configuration, version, dependencies
├── README.md
├── assets/                 # Backend CSS, energy scale images
├── lang/                   # Backend translations (de_de only)
├── lib/                    # PHP classes
│   ├── Property.php        # Property model (~50 fields, OpenImmo-compatible)
│   ├── Category.php        # Category model (hierarchical)
│   ├── Contact.php         # Contact person model
│   ├── FrontendHelper.php  # Frontend utilities (alternate URLs, breadcrumbs)
│   ├── LangHelper.php      # Sprog wildcard provider (~140 keys, DE/EN)
│   ├── Module.php          # Module definitions and revisions
│   ├── export/             # Export-related classes
│   │   ├── AExport.php         # Abstract export base
│   │   ├── AFTPExport.php      # FTP/ZIP export base
│   │   ├── ExportCronjob.php   # Auto-export cronjob
│   │   ├── ExportedProperty.php # Export queue entries
│   │   ├── OpenImmo.php        # OpenImmo export implementation
│   │   └── Provider.php        # Export provider configuration
│   ├── import/             # Import-related classes
│   │   ├── ImportCronjob.php   # Auto-import cronjob
│   │   └── ImportOpenImmo.php  # OpenImmo ZIP/XML import
│   └── deprecated_classes.php
├── modules/                # 3 module variants in group 70
│   └── 70/
│       ├── 1/              # Hauptausgabe (main output)
│       ├── 2/              # Infobox Ansprechpartner (contact info box)
│       └── 3/              # Ausgabe Kategorie (category output)
├── pages/                  # Backend pages
│   ├── index.php           # Page router
│   ├── property.php        # Property management
│   ├── property.online.php # Active properties
│   ├── property.archive.php # Archived properties
│   ├── category.php        # Category management
│   ├── contact.php         # Contact management
│   ├── export.php          # Export overview
│   ├── export_provider.php # Export provider management
│   ├── import.php          # OpenImmo import UI
│   ├── import_logs.php     # Import logs + ZIP downloads
│   ├── window_advertising_property.php      # Property selection for window advertising
│   ├── window_advertising_advertisement.php # Window advertising content management
│   ├── settings.settings.php  # Addon settings
│   ├── settings.setup.php     # Module manager
│   ├── help.readme.php        # Help/README page
│   └── help.changelog.php     # Changelog
```

## Coding Conventions

- **Namespace:** `TobiasKrais\D2UImmo` for all classes
- **Deprecated Namespace:** `D2U_Immo` (backward compatibility, removal planned for 2.0.0)
- **Naming:** camelCase for variables, PascalCase for classes
- **Indentation:** 4 spaces in PHP classes, tabs in module files
- **Comments:** English comments only
- **Frontend labels:** Use `Sprog\Wildcard::get()` backed by lang helper, not `rex_i18n::msg()`
- **Backend labels:** Use `rex_i18n::msg()` with keys from `lang/` files

## AGENTS.md Maintenance

- When new project insights are gained during work and they are relevant to agent guidance, workflows, conventions, architecture, or known pitfalls, update this AGENTS.md accordingly.

## Key Classes

| Class | Description |
| ----- | ----------- |
| `Property` | Property model: extensive OpenImmo-compatible fields (~50), prices, areas, rooms, energy pass, features, pictures, plans, floor plans. Implements `ITranslationHelper` |
| `Category` | Category model: hierarchical with parent category, picture, teaser. Implements `ITranslationHelper` |
| `Contact` | Contact person model: name, address, phone, fax, mobile, email, picture |
| `FrontendHelper` | Frontend utilities: alternate URLs and breadcrumbs for properties and categories |
| `LangHelper` | Sprog wildcard provider with ~140 translation keys (DE, EN) for real estate terms |
| `Module` | Module definitions and revision numbers |

## Database Tables

| Table | Description |
| ----- | ----------- |
| `rex_d2u_immo_properties` | Properties (language-independent): ~50 fields for types, address, prices, areas, features, energy, pictures |
| `rex_d2u_immo_properties_lang` | Properties (language-specific): name, teaser, description, equipment, location, documents |
| `rex_d2u_immo_categories` | Categories (language-independent): priority, parent category, picture |
| `rex_d2u_immo_categories_lang` | Categories (language-specific): name, teaser |
| `rex_d2u_immo_contacts` | Contact persons: name, address, phone, fax, mobile, email, picture |

### Database Views (for URL addon)

- `rex_d2u_immo_url_properties` — Property URLs for URL addon
- `rex_d2u_immo_url_categories` — Category URLs for URL addon

## Architecture

### Extension Points

| Extension Point | Location | Purpose |
| --------------- | -------- | ------- |
| `D2U_HELPER_TRANSLATION_LIST` | boot.php (backend) | Registers addon in D2U Helper translation manager |
| `ART_PRE_DELETED` | boot.php (backend) | Prevents deletion of articles used by the addon |
| `CLANG_DELETED` | boot.php (backend) | Cleans up language-specific data when a language is deleted |
| `MEDIA_IS_IN_USE` | boot.php (backend) | Prevents deletion of media files used by contacts/categories/properties |
| `D2U_HELPER_ALTERNATE_URLS` | boot.php (frontend) | Provides alternate URLs for properties and categories |
| `D2U_HELPER_BREADCRUMBS` | boot.php (frontend) | Provides breadcrumb segments for properties and categories |

### Modules

3 module variants in group 70:

| Module | Name | Description |
| ------ | ---- | ----------- |
| 70-1 | D2U Immo - Hauptausgabe | Main property output with gallery, videos, inquiry form |
| 70-2 | D2U Immo - Infobox Ansprechpartner | Contact person info box |
| 70-3 | D2U Immo - Ausgabe Kategorie | Category listing |

#### Module Versioning

Each module has a revision number defined in `lib/Module.php` inside the `getModules()` method. When a module is changed:

1. Add a changelog entry in `pages/help.changelog.php` describing the change.
2. Increment the module's revision number in `Module::getModules()` by one.

**Important:** The revision only needs to be incremented **once per release**, not per commit. Check the changelog: if the version number is followed by `-DEV`, the release is still in development and no additional revision bump is needed.

### Integrated Features

| Feature | Description | Extra Dependencies |
| ------- | ----------- | ------------------ |
| `export` | OpenImmo XML export to FTP-based providers with cronjob automation | `cronjob`, `media_manager`, `phpmailer`, PHP ext: `xml`, `zip` |
| `import` | OpenImmo XML/ZIP import with cronjob automation | `cronjob`, `mediapool`, `phpmailer`, PHP ext: `xml`, `zip` |
| `window_advertising` | Window/display advertising management for properties | `sprog` |

## Redaxo 6 Migration Note

- `export`, `import`, and `window_advertising` are integrated into the main addon so the addon no longer depends on a `plugins/` directory.
- Existing database tables remain stable. The old `D2U_Immo` namespace and legacy global class names remain available via `lib/deprecated_classes.php` until 2.0.0.
- Export-specific wildcards are maintained directly in `LangHelper`; there is no separate `ExportLangHelper` class anymore.

### Media Manager Types

| Type | Purpose |
| ---- | ------- |
| `d2u_immo_contact` | Contact picture (400×400 max, workspace centered) |
| `d2u_immo_list_tile` | Property list thumbnail (768×768 max) |

### YForm Email Templates

- `d2u_immo_request` — Property inquiry form email template

## Settings

Managed via `pages/settings.settings.php` and stored in `rex_config`:

- `default_category_sort` / `default_property_sort` — Sorting options
- `finance_calculator_interest_rate` / `finance_calculator_notary_costs` / `finance_calculator_real_estate_tax` / `finance_calculator_repayment` — Financial calculator defaults
- `lang_wildcard_overwrite` — Preserve custom Sprog translations
- `lang_replacement_{clang_id}` — Language mapping per REDAXO language

## Dependencies

| Package | Version | Purpose |
| ------- | ------- | ------- |
| `d2u_helper` | >= 1.14.0 | Backend/frontend helpers, module manager, translation interface |
| `media_manager` | >= 2.2 | Image processing |
| `sprog` | >= 1.0.0 | Frontend translation wildcards |
| `url` | >= 2.0 | SEO-friendly URLs |
| `yform` | >= 3.0 | Form handling (inquiry forms) |
| `yrewrite` | >= 2.0.1 | URL rewriting |

## Multi-language Support

- **Backend:** de_de only
- **Frontend (Sprog Wildcards):** DE, EN (2 languages, ~140 keys for real estate terms)

## Versioning

This addon follows [Semantic Versioning](https://semver.org/):

- **Major** (1st digit): Breaking changes (e.g. removed classes, renamed methods, incompatible DB changes)
- **Minor** (2nd digit): New features, new modules, new database fields (backward compatible)
- **Patch** (3rd digit): Bug fixes, small improvements (backward compatible)

The version number is maintained in `package.yml`. During development, the changelog uses a `-DEV` suffix.

## Changelog

The changelog is located in `pages/help.changelog.php`.
