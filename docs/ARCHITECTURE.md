# Kezuru — Architecture Guide

This document explains how code is organised in Kezuru, where things go, and why. Read this before you write a single class.

If you've used Laravel or WordPress, some of this will feel familiar and some won't. The differences are deliberate.

---

## The Pattern: Action–Domain–Responder (ADR)

Kezuru follows ADR, not MVC. They look similar on the surface, but ADR draws sharper boundaries — and that's the point.

In MVC, a "Controller" handles multiple routes and tends to accumulate business logic because the pattern doesn't tell you where else to put it. The "Model" is even worse — it becomes your database layer, your validation, your business rules, and your serialisation format all in one class. Six months in, you've got 400-line controllers calling 600-line models and nobody can untangle them.

ADR fixes this by being specific about what each layer does.

### The Three Layers

```
HTTP Request → Action → Domain → Responder → HTTP Response
```

**Action** — One class per route. Its only job is to receive the HTTP request, pass data to the Domain, and hand the result to a Responder. It contains no business logic, no database calls, no HTML. Think of it as a receptionist: takes the message, routes it, passes back the reply.

**Domain** — Where the actual work happens. This layer has no knowledge of HTTP. It doesn't know what a Request or Response is. It receives plain data and returns plain data. The Domain is subdivided into:

- **Service** — Orchestrates the operation. Calls the Validator, then the Repository, maybe triggers a side effect. Decides *what* to do.
- **Validator** — Checks business rules. Is this email already taken? Is this field required? Are these values sane?
- **Repository** — The only thing that touches the database. Pure data access: find, insert, update, delete. Decides *how* to store it.

**Responder** — Turns the Domain's result into an HTTP response. For a template page, this means rendering a PHP view. For an API endpoint, this means encoding JSON. The Domain doesn't know or care which one gets used.

### Why This Matters

The key discipline: **Actions are thin. Domain is subdivided. Responders are swappable.**

This means:

- You can serve the same data as both a rendered page and a JSON API without duplicating logic.
- You can change your database layer without touching your business rules.
- You can test your Domain logic without booting up an HTTP server.
- You can look at any operation folder and see the full flow in three files.

### If You're Coming From Laravel

| Laravel | Kezuru | Why it's different |
|---------|--------|--------------------|
| Controller (handles many routes) | Action (one class, one route) | Can't bloat — there's nowhere for unrelated logic to creep in |
| Eloquent Model (DB + validation + business logic + serialisation) | Repository (DB only) + Validator + Service | Each job has its own class with clear boundaries |
| FormRequest | Validator (in Domain layer) | Validation lives with business logic, not HTTP |
| Blade template | PHP-View (plain PHP) | No template language to learn, no compilation step |
| `php artisan make:controller` | Create the three files yourself | You learn the structure by building it, not generating it |

### If You're Coming From WordPress

| WordPress | Kezuru | Why it's different |
|-----------|--------|--------------------|
| `functions.php` (everything everywhere) | Logic split across Service, Validator, Repository | Every piece of logic has one home |
| `$wpdb->get_results()` scattered in templates | Repository classes — the only place SQL lives | Templates never touch the database |
| `add_action` / `add_filter` hooks | Direct dependency injection | No hidden execution order, no hook priority guessing |
| `get_header()` / `get_footer()` / `get_template_part()` | PHP includes via layout templates | Same concept, without the WordPress abstraction layer |
| The Loop | Query in Repository, loop in template | Data fetching and data display are completely separated |

---

## Directory Structure

```
src/
├── Core/                           # Framework plumbing (shared by all modules)
│   ├── Database/                   # Query factory, hydrator
│   │   └── Exception/             # Database-specific exceptions
│   ├── Exception/                  # Framework-level exceptions
│   ├── Middleware/                  # CORS, view setup, validation handling
│   ├── Responder/                  # Template renderer, JSON responder, redirects
│   ├── Settings/                   # App configuration
│   └── Utility/                    # Dev tools (JS cache busting, etc.)
│
└── Module/                         # Feature modules (where your work lives)
    ├── Home/                       # Simple module — just Actions, no Domain needed
    │   ├── HomePageAction.php
    │   └── RedirectToHomePageAction.php
    │
    └── User/                       # Full module — demonstrates the complete pattern
        ├── Create/                 # One folder per operation
        │   ├── UserCreateAction.php
        │   ├── UserCreateService.php
        │   └── UserCreateRepository.php
        ├── Read/
        │   ├── UserReadPageAction.php
        │   ├── UserReadService.php
        │   └── UserReadRepository.php
        ├── Update/
        │   ├── UserUpdateAction.php
        │   ├── UserUpdateService.php
        │   └── UserUpdateRepository.php
        ├── Delete/
        │   ├── UserDeleteAction.php
        │   ├── UserDeleteService.php
        │   └── UserDeleteRepository.php
        ├── List/
        │   ├── UserListPageAction.php  # Template response
        │   ├── UserListApiAction.php   # JSON response — same Domain, different Responder
        │   ├── UserListService.php
        │   └── UserListRepository.php
        ├── Validation/
        │   └── UserValidator.php       # Shared across Create and Update
        └── Data/
            └── UserData.php            # Plain data object (DTO)
```

### The Rules

**Core/** is framework plumbing. Middleware, responders, database utilities, exceptions. Every module uses it, no module owns it. You'll rarely need to add things here.

**Module/** is where features live. Each module is a self-contained feature area (User, Home, and eventually your content types).

**Inside a module, group by operation.** Each operation folder (Create, Read, Update, Delete, List) contains the Action, Service, and Repository for that operation. Open any folder and you see the full ADR flow.

**Validation lives at the module level**, not inside an operation, because validation rules often apply across operations — Create and Update both need to check if an email is unique.

**Data objects live at the module level** too, because they represent the module's data shape, not a single operation.

**No unnecessary nesting.** If a folder would contain a single file, don't create the folder. Files sit directly in their operation folder — no `Action/` subdirectory containing one Action class.

### Where Does This New Thing Go?

Follow this decision tree:

1. **Is it shared infrastructure used by all modules?** → `Core/`
2. **Is it a feature or business capability?** → `Module/YourModule/`
3. **Does it handle a single HTTP route?** → It's an Action. Put it in the relevant operation folder.
4. **Does it orchestrate business logic?** → It's a Service. Same operation folder.
5. **Does it talk to the database?** → It's a Repository. Same operation folder.
6. **Does it validate data across operations?** → `Module/YourModule/Validation/`
7. **Is it a plain data structure?** → `Module/YourModule/Data/`

If you're unsure, ask: "If I deleted this module entirely, would the rest of the app still work?" If yes, it belongs in the module. If no, it probably belongs in Core.

---

## The Full Picture

The `src/` directory is only the backend logic. Here's where everything else lives:

```
kezuru/
├── config/
│   ├── routes.php              # Every URL your app responds to
│   ├── content/                # Content type definitions (Phase 1)
│   │   ├── page.php
│   │   └── team_member.php
│   ├── container.php           # Dependency injection bindings
│   ├── middleware.php           # Global middleware stack
│   ├── settings.php            # App configuration
│   └── env/                    # Environment-specific settings
│
├── templates/                  # Everything a visitor or admin sees
│   ├── layout.php              # The outer HTML shell (head, body, scripts)
│   ├── front/                  # Public-facing pages
│   │   ├── home.php
│   │   └── components/         # Reusable pieces (header, footer, CTA, etc.)
│   │       ├── header.php
│   │       ├── footer.php
│   │       └── hero.php
│   └── admin/                  # Admin panel screens
│       ├── layout.php          # Admin shell (sidebar, nav, chrome)
│       ├── dashboard.php
│       └── content/            # Auto-generated CRUD screens (Phase 1)
│           ├── list.php
│           └── form.php
│
├── public/                     # Web root — the only directory your server exposes
│   ├── index.php               # Front controller (all requests start here)
│   └── assets/                 # CSS, JS, images, fonts
│
├── storage/                    # Data that isn't code
│   ├── database.sqlite
│   └── uploads/                # User-uploaded files
│
└── src/                        # Backend logic (the ADR structure documented above)
    ├── Core/
    └── Module/
```

### If You're Coming From WordPress

| You're looking for... | In Kezuru, it's at... |
|-----------------------|-----------------------|
| `index.php` / `page.php` / `single.php` | `templates/front/` — one file per page type |
| `header.php` / `footer.php` | `templates/front/components/` |
| `functions.php` | There isn't one. Logic lives in `src/Module/` Services |
| `style.css` / `scripts.js` | `public/assets/` |
| `wp-content/uploads/` | `storage/uploads/` |
| `wp-admin/` | `templates/admin/` — but the logic is in `src/Module/` |
| `wp-config.php` | `config/settings.php` + `config/env/` |

### If You're Coming From Laravel

| You're looking for... | In Kezuru, it's at... |
|-----------------------|-----------------------|
| `resources/views/` | `templates/` |
| `routes/web.php` | `config/routes.php` |
| `app/Http/Controllers/` | `src/Module/*/` — Actions, not controllers |
| `app/Models/` | `src/Module/*/Data/` for DTOs, `*/Repository` for DB access |
| `.env` | `config/env/env.php` — plain PHP, no parser needed |
| `public/` | `public/` — same concept |
| `database/migrations/` | Phinx migrations (same idea, different tool) |
| `app/Http/Middleware/` | `src/Core/Middleware/` |

The key insight: **templates are not logic.** They receive data from the ADR chain and display it. A template should never call the database, validate input, or make business decisions. If you find yourself writing `if` statements with complex conditions in a template, that logic belongs in a Service.

---

## Anatomy of a Request

Here's what happens when a user hits `GET /users` in Kezuru:

```
Browser requests GET /users
        │
        ▼
┌─────────────────┐
│   Router         │  config/routes.php maps URL to Action class
│   (Slim)         │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Middleware      │  CORS, view setup, auth checks run first
│   (Core)         │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Action         │  UserListPageAction receives the Request
│   (Module)       │  Calls the Service. That's it.
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Service        │  UserListService orchestrates:
│   (Module)       │  - Calls Repository to fetch data
│                  │  - Applies any business logic
│                  │  - Returns plain data
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Repository     │  UserListRepository runs the query
│   (Module)       │  Returns an array of UserData objects
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Responder      │  TemplateRenderer renders the PHP template
│   (Core)         │  with the data the Action received from the Service
└────────┬────────┘
         │
         ▼
    HTML Response
```

Now here's the power of ADR: if you want that same data as a JSON API, you create `UserListApiAction.php` in the same folder. It calls the same Service, same Repository — but hands the result to `JsonResponder` instead of `TemplateRenderer`. Zero duplication of business logic.

---

## What a Clean Action Looks Like

```php
<?php

namespace App\Module\User\Create;

use App\Core\Responder\JsonResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class UserCreateAction
{
    public function __construct(
        private UserCreateService $service,
        private JsonResponder $responder,
    ) {}

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $data = $request->getParsedBody();
        $result = $this->service->create($data);

        return $this->responder->encodeAndAddToResponse($response, $result, 201);
    }
}
```

That's the whole class. Three lines of logic: get the data, call the service, return the response. If you're tempted to add an `if` statement with business logic here, stop — it belongs in the Service.

---

## The Discipline

This architecture requires discipline. It's tempting to skip the Service and query the database directly from the Action — "it's just one query, it's quicker." Don't. Six months from now, "just one query" has become twelve, with validation mixed in and two edge cases bolted on, and you've rebuilt the Laravel controller problem in a different folder.

The rules are simple:

- **Actions** receive requests and return responses. Nothing else.
- **Services** contain business logic. They never touch HTTP.
- **Repositories** contain database queries. They never contain business rules.
- **Validators** check data. They don't save it.

If a class is doing two of these jobs, split it. If a file is getting long, you've probably mixed concerns.

The trade-off is more files. A simple CRUD module has around 15 files where Laravel might have 3. But each file is small, single-purpose, and obvious. You'll never open a file and wonder what it does or where to add new logic. That's the trade worth making.

---

## Interaction Model: PHP Renders Everything, HTMX Swaps It

The admin panel uses [HTMX v4](https://four.htmx.org/) (pre-release) as its interaction layer. This is a deliberate architectural decision that affects how every CRUD screen works.

### The Rule

> **The server always returns HTML.** For initial page loads, the server renders full pages. For mutations (create, update, delete), the server renders HTML fragments. HTMX swaps those fragments into the DOM. Custom JavaScript is only for UI polish — dark mode, modal open/close, flash messages.

### Why HTMX, Not JSON + JS

The old approach (which we moved away from) was: server returns JSON → JavaScript builds the DOM. That means writing every UI component twice — once in PHP templates for the server-rendered version, once in JS for the dynamic version. HTMX eliminates this: the server renders the HTML once, and HTMX handles getting it into the page.

This also means the Content Engine (Phase 1) only needs to generate PHP templates with HTMX attributes. Zero generated JavaScript.

### How CRUD Works With HTMX

**List page** — Full server render. PHP queries the database, loops over results, renders a table. The page arrives complete — no skeleton loaders, no loading spinners, no JS data fetching.

**Create** — User clicks "New User" → HTMX fetches a form partial from the server → injects it into a modal container. User submits → HTMX POSTs the form → server validates and either returns a new table row (appended to the table) or the form again with error messages.

**Update** — Edit page renders with pre-populated form. User submits → HTMX PUTs the form → server validates and returns the updated form partial.

**Delete** — User clicks Delete → `hx-confirm` shows a browser confirmation → HTMX sends DELETE → server returns empty body → HTMX removes the row from the DOM.

### Partials: The DRY Piece

Each module has partial templates in `templates/admin/{module}/partials/`. These are small HTML fragments (a single table row, a form) that serve double duty:

1. Included in the full page template during initial render (inside a `foreach` loop, for example)
2. Returned standalone as HTMX responses after mutations

Same partial, two contexts. One source of truth for how a user row or a form looks.

### Validation Error Handling

Admin Actions catch `ValidationException` locally instead of letting it bubble up to the `ValidationExceptionMiddleware`. This is because the middleware returns JSON (for the API), but admin Actions need to return HTML fragments with inline error messages.

HTMX v4's `hx-status:422` attribute controls where validation error responses get swapped — the form declares different targets for success and error responses right in the HTML.

### The Boundary Table

| Concern | Owner | Notes |
|---------|-------|-------|
| Page structure and layout | PHP template | Full page render, always |
| Data rendered into HTML | PHP template | Initial loads AND mutation responses |
| Mutation responses | PHP Action → HTML partial | Not JSON — rendered HTML fragments |
| DOM swapping after mutations | HTMX | Via `hx-target`, `hx-swap`, `hx-status:XXX` |
| Modal open/close | Minimal JS | `admin-htmx.js` listens for `closeModal` event |
| Flash messages | HTMX `HX-Trigger` header + JS listener | Server triggers, JS renders toast |
| Dark mode | Existing JS | `dark-mode.js`, unchanged |
| Page navigation | Standard `<a>` links | No client-side routing |

### HTMX Version Note

We're using HTMX **4.0.0-alpha7** (pre-release). Key v4 features we depend on are flagged with comments in the templates. If v4 causes issues, see `docs/HTMX-REFERENCE.md` for the specific features and their fallback strategies.
