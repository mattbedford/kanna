# Kezuru

This project is called **Kezuru** (削る, "to whittle/shave"). Not "Kanna" — that was the old name. Domain: kezuru.dev

A PHP content management starter kit built on Slim 4. Alternative to WordPress for developers who prefer writing plain PHP over framework abstractions. Built on Samuel Gfeller's slim-starter.

## Architecture: ADR (Non-Negotiable)

This project follows Action–Domain–Responder. Not MVC. This is strict — no exceptions, no shortcuts, no "just this once."

- **Action**: One class, one route. Receives request, calls Service, hands result to Responder. No business logic. No database calls. No HTML. Ever.
- **Service**: Orchestrates business logic. No knowledge of HTTP. Receives plain data, returns plain data.
- **Repository**: The only thing that touches the database. Pure data access.
- **Validator**: Checks business rules. Doesn't save anything.
- **Responder**: Turns Domain result into HTTP response (template or JSON).

If you're about to put an `if` with business logic in an Action, stop. It belongs in a Service. If you're about to query the database from a Service, stop. It belongs in a Repository. Read `ARCHITECTURE.md` for the full specification.

### Directory conventions

- `src/Core/` — shared framework plumbing (middleware, responders, database utilities)
- `src/Module/` — feature modules, grouped by operation (Create/, Read/, Update/, Delete/, List/)
- Each operation folder contains its Action, Service, and Repository together
- Validation and Data objects live at module level, not inside operations
- No unnecessary nesting — if a folder would contain one file, don't create the folder

### Template architecture (three zones)

- `templates/admin/` — the polished product clients see. Ships ready.
- `templates/front/` — developer skeleton. Expect devs to replace entirely.
- `templates/account/` — developer skeleton.

Templates are not logic. They receive data and display it. No database calls, no validation, no business decisions in templates.

### Rendering strategy

Template-first, API-available. Server-rendered monolith by default. JSON API endpoints exposed alongside template routes for developers who need headless.

### Routing philosophy

Deterministic routing with flat content collections. No WordPress-style hierarchical content that auto-generates archive pages and routes.

## Working With Matt

**PHP: Coach, don't write.** Matt is building this to learn and own every line. When PHP work is needed, explain what needs to be written, why, and where it goes — then let Matt write it. Offer the code if he asks, or if he says he's feeling lazy. But default to teaching.

**JavaScript: Just do it.** Matt doesn't enjoy JS. Write the JS yourself, explain what it does briefly, and move on. All JS must be simple and straightforward — no clever patterns, no abstractions, no fancy shit. Comment everything at an OCD level: every function, every block, every non-obvious line. Matt may need to debug it and needs to understand what's happening at a glance.

**Go step by step.** One task at a time. Wait for confirmation before proceeding. Never dump multiple steps at once. Matt wants to understand and control what's happening.

**Explain as you go.** Offer useful bits of information about the code — why a pattern exists, what a function does, how something connects. Keep it proportionate to Matt's level (experienced PHP dev, comfortable with architecture, less so with JS).

## Tech Stack

- Framework: Slim 4 with PHP-DI
- Database: SQLite (dev) / MariaDB (prod), via CakePHP query builder (not ORM)
- Migrations: Phinx
- Templating: PHP-View (plain PHP, no Twig)
- CSS: Tailwind v4 standalone CLI (no Node.js — this is deliberate)
- Icons: Ionicons (inline SVG)
- JS: Vanilla ES modules (no jQuery, no bundler, no build step)
- Testing: PHPUnit
- Logging: Monolog

## Commands

- `php bin/setup.php` — project initialisation
- `./tailwindcss -i resources/css/app.css -o public/assets/css/app.css --watch` — Tailwind build
- `vendor/bin/phpunit` — run tests
- `vendor/bin/phinx migrate` — run database migrations