# Kanna — Roadmap

A lightweight PHP starter kit for developers who want to write PHP, not "framework".
Built on Slim 4, inspired by Japanese craft tooling philosophy: remove what's unnecessary to reveal what's underneath.

Base: [samuelgfeller/slim-starter](https://github.com/samuelgfeller/slim-starter)

---

## What's Already Working

- [x] Slim 4 framework with PHP-DI dependency injection
- [x] SQLite database (swappable to MariaDB/PostgreSQL via config)
- [x] User CRUD with validation
- [x] PHP-View templating (plain PHP, no Twig)
- [x] CakePHP query builder (not ORM — just queries)
- [x] Phinx database migrations
- [x] Monolog logging
- [x] CSRF protection
- [x] Dark/light theme toggle
- [x] ES module JS architecture (no jQuery, no build step)
- [x] PHPUnit test suite
- [x] Tailwind CSS v4 via standalone CLI (no Node, no npm)
- [x] Strapi-inspired colour theme with light/dark mode tokens
- [x] `bin/setup.php` — one-command project setup (directories, database, Tailwind CLI download, initial CSS build)

---

## Phase 1 — Content Engine (MVP)

The core of Kanna. A schema-driven content management system that auto-generates admin CRUD screens from simple PHP config files.

### 1.1 Schema Definition Format

Define content types as PHP arrays in `config/content/`. One file per type.

```php
// config/content/team_member.php
return [
    'label'        => 'Team Members',
    'label_single' => 'Team Member',
    'icon'         => 'users',
    'fields'       => [
        'name'       => ['type' => 'text', 'required' => true, 'label' => 'Full Name'],
        'job_title'  => ['type' => 'text', 'label' => 'Job Title'],
        'bio'        => ['type' => 'textarea', 'label' => 'Biography'],
        'headshot'   => ['type' => 'image', 'label' => 'Photo'],
        'start_date' => ['type' => 'date', 'label' => 'Start Date'],
        'website'    => ['type' => 'url', 'label' => 'Website'],
        'sort_order' => ['type' => 'number', 'label' => 'Sort Order'],
    ],
];
```

**MVP field types:**

| Type       | Renders as              | DB column    |
|------------|-------------------------|--------------|
| `text`     | `<input type="text">`   | VARCHAR(255) |
| `textarea` | `<textarea>`            | TEXT         |
| `richtext` | Textarea + simple editor| TEXT         |
| `number`   | `<input type="number">` | INTEGER      |
| `date`     | `<input type="date">`   | DATE         |
| `url`      | `<input type="url">`    | VARCHAR(500) |
| `email`    | `<input type="email">`  | VARCHAR(254) |
| `image`    | File upload + preview   | VARCHAR(500) |
| `select`   | `<select>`              | VARCHAR(255) |
| `toggle`   | Checkbox/switch         | BOOLEAN      |

### 1.2 Schema-to-Database (SchemaToTable)

- Reads content type definitions from `config/content/`
- Compares against actual database tables
- Creates or alters tables to match (prefix: `content_`)
- Runs automatically on setup, can be triggered via CLI
- Must work identically across SQLite, MariaDB, PostgreSQL

### 1.3 Schema-to-Form (FormRenderer)

- Reads content type definition
- Outputs correct HTML form fields per type
- Handles validation rules (required, max length, etc.)
- FieldTypeRegistry maps type string to renderer class
- New field types added by registering one class

### 1.4 Generic Admin CRUD

All content types share one set of actions/routes:

```
GET    /admin/content/{type}             → List view
GET    /admin/content/{type}/create      → Create form
POST   /admin/content/{type}             → Store
GET    /admin/content/{type}/{id}        → Edit form
PUT    /admin/content/{type}/{id}        → Update
DELETE /admin/content/{type}/{id}        → Delete
```

Components:
- `ContentListAction` — generic list with sortable columns from schema
- `ContentCreateAction` / `ContentStoreAction` — form + save
- `ContentEditAction` / `ContentUpdateAction` — form + save
- `ContentDeleteAction` — soft or hard delete
- `ContentRepository` — generic CRUD using query builder
- `ContentValidator` — validates input against schema rules

### 1.5 File/Image Upload

- `ImageUploadService` handles uploads to `storage/uploads/`
- Resize/thumbnail generation
- Returns stored path for DB
- Abstracted via Flysystem for future S3/CDN support (optional)

### 1.6 Admin Navigation

- Auto-generated from content type definitions
- Sidebar or top nav listing each content type with its icon and label
- "Users" module remains as-is alongside content types

---

## Phase 2 — Admin UI Overhaul

The current Gfeller UI is functional but won't wow colleagues or clients.

### Goals
- Modern, clean, professional admin feel
- Clients see it and feel confident
- Not over-designed — just polished

### Decision: Tailwind CSS via Standalone CLI
Tabler was considered but felt too Bootstrap-heavy. Tailwind gives us modern utility-first styling without introducing Node or npm into the project. The standalone CLI binary lives at `bin/tailwindcss` and is downloaded automatically by `bin/setup.php`.

- **Source CSS:** `resources/css/app.css` (theme tokens defined here)
- **Compiled CSS:** `public/assets/css/app.css`
- **Dev workflow:** `./bin/tailwindcss -i resources/css/app.css -o public/assets/css/app.css --watch`
- **Dark mode:** Uses existing `data-theme` attribute via `@variant dark` override
- **Colour system:** Strapi-inspired tokens — `primary`, `surface`, `body`, `muted`, `danger`, etc.

### Remaining
- Admin sidebar layout (Strapi-style left nav)
- Overall page structure for content engine screens
- Responsive for tablet (clients on iPad)
- Gradually replace existing Gfeller CSS with Tailwind equivalents

### Inspiration
- Strapi's clean content editing experience
- Statamic's field UI polish
- NOT WordPress. Never WordPress.

---

## Phase 3 — Live Preview (The Dream)

Split-pane editing: fields on the left, front-end preview on the right.

### Architecture
- `ContentPreviewAction` — receives form data via AJAX POST
- Renders through the actual front-end template with unsaved data
- Returns HTML to an iframe in the right pane
- Debounced onChange fires preview refresh as client types

### Notes
- Does NOT need to be in MVP
- But architecture should not prevent it — keep templates reusable
- Preview uses same template files as the live site

---

## Phase 4 — Packaging & Distribution

### Composer create-project
```bash
composer create-project kanna/kanna mysite
cd mysite
php bin/setup.php
```

### bin/setup.php CLI Installer
Already implemented:
- Creates `storage/`, `storage/uploads/`, `logs/` directories
- Initialises SQLite database file
- Downloads Tailwind CSS standalone CLI (platform-detected)
- Runs initial Tailwind CSS build

Still to add:
- Run SchemaToTable against all content definitions
- Prompt for admin email + password
- Create first admin user

### Deployment
- Single directory, single process
- PHP-FPM + Caddy (auto SSL)
- SQLite for small sites, MariaDB/PostgreSQL via one config change
- No Docker required (but Dockerfile provided for those who want it)
- No Node, no build step, no compilation

---

## Phase 5 — Community Release

### Before release
- Clean README ("This is not a CMS. It's a starter kit.")
- Example content definition shipped (basic pages type)
- MIT licence
- Contributing guide
- Changelog

### Positioning
For PHP developers who:
- Want to write PHP, not "Laravel"
- Need a clean admin for client content
- Don't want WordPress's React-driven complexity
- Value simplicity, control, and independence from private equity

---

## Architecture Reference

```
src/
├── Core/
│   ├── Application/
│   │   ├── Middleware/
│   │   └── Responder/
│   ├── Domain/
│   └── Infrastructure/
│
├── Module/
│   ├── Home/
│   ├── User/                    # Existing Gfeller module
│   │   ├── Create/
│   │   ├── Read/
│   │   ├── Update/
│   │   └── Delete/
│   │
│   └── ContentEngine/           # NEW
│       ├── Schema/
│       │   ├── SchemaLoader.php
│       │   ├── SchemaToTable.php
│       │   └── FieldTypeRegistry.php
│       ├── Admin/
│       │   ├── Action/
│       │   └── Service/
│       │       ├── FormRenderer.php
│       │       ├── ListRenderer.php
│       │       └── PreviewRenderer.php  (Phase 3)
│       ├── Repository/
│       │   └── ContentRepository.php
│       ├── Validation/
│       │   └── ContentValidator.php
│       └── FileHandling/
│           └── ImageUploadService.php

config/
├── content/                     # Content type definitions
│   ├── page.php
│   ├── team_member.php
│   └── testimonial.php

resources/
├── css/
│   └── app.css                  # Tailwind theme tokens + imports

storage/
├── database.sqlite
└── uploads/

templates/
├── admin/
│   ├── layout.php
│   ├── content/
│   │   ├── list.php
│   │   └── form.php
│   └── dashboard.php
├── front/
│   ├── layout.php
│   └── components/
```

---

## Tech Stack

| Layer            | Tool                     | Why                                    |
|------------------|--------------------------|----------------------------------------|
| Framework        | Slim 4                   | Micro-framework, no opinions, just PHP |
| DI Container     | PHP-DI                   | Clean, annotation-free                 |
| Database         | SQLite / MariaDB / PgSQL | One config change to switch            |
| Query Builder    | cakephp/database         | Standalone, multi-driver, not an ORM   |
| Migrations       | Phinx                    | Standard PHP migration tool            |
| Templating       | PHP-View                 | Plain PHP templates, no Twig           |
| JS               | Vanilla ES modules       | No jQuery, no bundler, no build step   |
| Logging          | Monolog                  | Industry standard                      |
| Testing          | PHPUnit                  | Industry standard                      |
| Admin UI         | Tailwind CSS v4          | Standalone CLI, no Node, utility-first |

---

## Design Principles

1. **You write PHP.** Not framework DSL, not YAML incantations, not React.
2. **Clients edit content.** Clean fields, clear labels, nothing they don't need.
3. **One server, one directory.** No microservices, no Docker required, no Node.
4. **Schema defines everything.** One config file per content type. The engine does the rest.
5. **Swap what you want.** SQLite today, PostgreSQL tomorrow. Tabler today, custom UI tomorrow.
6. **No private equity.** MIT licensed. No "free tier" that turns paid. No rug pulls.
