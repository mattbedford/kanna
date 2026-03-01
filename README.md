# Kezuru
![Development Status](https://img.shields.io/badge/development-active-blue)
![Status](https://img.shields.io/badge/status-in%20progress-yellow)

A lightweight PHP starter kit for developers who want to write PHP, not "framework".
Built on Slim 4, inspired by Japanese craft tooling philosophy: remove what's unnecessary to reveal what's underneath.


## Goals
There's a gap in the PHP world. Full frameworks bring power but also complexity and opinions you might not share. WordPress has evolved in directions that don't suit every project. Micro-frameworks give you freedom but leave you building everything from scratch.

***Kezuru*** sits in that gap. It's a starter package that gives your clients a clean admin panel and content editing out of the box, while letting you as the developer build exactly what's needed with PHP as the driving engine.

To succeed it must:
- be light and portable;
- free of vendor lock-in;
- be easily extensible;
- allow end-clients to manage their content through a modern UI and not worry about breaking infra or designs;
- feature a modern log in and auth system and allow multi-language sites;
- [in future] allow e-Commerce builds with full security and transparency as well as offer an easy way to build "front-end admin" sites like membership sites or web-apps.


Built on the foundation and ideas of [samuelgfeller/slim-starter](https://github.com/samuelgfeller/slim-starter).


## Tech Stack

| Layer            | Tool                        | Why                                          |
|------------------|-----------------------------|----------------------------------------------|
| Framework        | Slim 4                      | Micro-framework, no opinions, just PHP       |
| DI Container     | PHP-DI                      | Clean, annotation-free                       |
| Database         | SQLite / MariaDB / PgSQL    | One config change to switch                  |
| Query Builder    | cakephp/database            | Standalone, multi-driver, not an ORM         |
| Migrations       | Phinx                       | Standard PHP migration tool                  |
| Templating       | PHP-View                    | Plain PHP templates, no Twig                 |
| CSS              | Tailwind v4 (standalone CLI)| No Node.js, no npm — single binary           |
| Interaction      | HTMX v4 (pre-release)       | Server returns HTML, HTMX swaps it in        |
| JS               | Vanilla ES modules          | No jQuery, no bundler                        |
| Logging          | Monolog                     | Industry standard                            |
| Testing          | PHPUnit                     | Industry standard                            |

**A note on HTMX v4:** We're using HTMX 4.0.0-alpha7, which is a pre-release version. We chose v4 because its `hx-status:XXX` feature gives us clean validation handling without server-side workarounds. If the v4 beta causes trouble, the HTMX team provides an `htmx-2-compat` extension for backwards compatibility. The file lives locally at `public/assets/vendor/htmx/htmx.min.js` — no CDN dependency. See `docs/HTMX-REFERENCE.md` for a quick-reference guide.

**A note on the "no build step" claim:** Tailwind uses a standalone CLI binary to compile your CSS. It scans your templates and outputs only the classes you actually use, so the resulting file is small and cache-friendly. It's not Node, not npm, not webpack — but it is a compilation step. We think that's the right trade-off: you get utility-class CSS without pulling in 200MB of node_modules.


## Design Principles

1. **You write PHP.** Not framework DSL, not YAML incantations, not React.
2. **Clients edit content.** Clean fields, clear labels, nothing they don't need.
3. **One server, one directory.** No microservices, no Docker required, no Node.
4. **Schema defines everything.** One config file per content type. The engine does the rest.
5. **Swap what you want.** SQLite today, PostgreSQL tomorrow. Tailwind today, custom CSS tomorrow.
6. **No private equity.** MIT licensed. No "free tier" that turns paid. No rug pulls.


## Getting Started

```bash
git clone git@github.com:mattbedford/kezuru.git mysite
cd mysite
composer install
php bin/setup.php
./bin/serve.sh
```

Then navigate to `http://localhost:8080` to see what you've got.


## Rebuilding CSS

After changing templates or adding Tailwind classes:

```bash
./bin/tailwindcss -i resources/css/app.css -o public/assets/css/app.css
```

For watch mode during development:

```bash
./bin/tailwindcss -i resources/css/app.css -o public/assets/css/app.css --watch
```


## Architecture

See [ARCHITECTURE.md](ARCHITECTURE.md) for a full guide to the ADR (Action–Domain–Responder) pattern and directory conventions used in this project.