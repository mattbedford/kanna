# HTMX v4 Quick Reference

A cheat sheet for HTMX 4.0.0 as used in Kezuru's admin panel. This covers the attributes, headers, events, and patterns we actually use or are likely to use — not a rewrite of the full docs.

Full docs: https://four.htmx.org/docs/
Reference: https://four.htmx.org/reference/
Migration from v2: https://four.htmx.org/whats-new-in-htmx-4/

**Version in use:** 4.0.0-alpha7 (local file at `public/assets/vendor/htmx/htmx.min.js`)

---

## Core Attributes

These go on any HTML element to make it issue HTTP requests and swap content.

### Request Attributes

| Attribute | What it does | Example |
|-----------|-------------|---------|
| `hx-get` | Issues a GET request | `hx-get="/users/create"` |
| `hx-post` | Issues a POST request | `hx-post="/users"` |
| `hx-put` | Issues a PUT request | `hx-put="/users/5"` |
| `hx-delete` | Issues a DELETE request | `hx-delete="/users/5"` |
| `hx-patch` | Issues a PATCH request | `hx-patch="/users/5"` |

Any element can issue requests — not just forms and links. A `<button>`, a `<tr>`, a `<div>`, whatever makes sense.

### Target and Swap

| Attribute | What it does | Default |
|-----------|-------------|---------|
| `hx-target` | CSS selector for the element that receives the response | The element that issued the request |
| `hx-swap` | How the response gets inserted into the target | `innerHTML` |

**Swap values:**

| Value | Effect | v4 alias |
|-------|--------|----------|
| `innerHTML` | Replace target's children | — |
| `outerHTML` | Replace the target element entirely | — |
| `beforebegin` | Insert before the target | `before` |
| `afterbegin` | Insert as first child | `prepend` |
| `beforeend` | Insert as last child | `append` |
| `afterend` | Insert after the target | `after` |
| `delete` | Remove the target element | — |
| `none` | Don't insert anything | — |
| `innerMorph` | Intelligently merge children (preserves state) | — |
| `outerMorph` | Intelligently merge target (preserves state) | — |

**Swap modifiers** (append after the swap value with a space):

```html
hx-swap="outerHTML swap:300ms"       <!-- delay before swap -->
hx-swap="innerHTML transition:true"  <!-- enable view transitions -->
hx-swap="innerHTML scroll:top"       <!-- scroll target to top after swap -->
hx-swap="innerHTML show:top"         <!-- scroll page to show target -->
```

### Extended Target Selectors

Beyond standard CSS selectors, HTMX adds these:

| Selector | Meaning |
|----------|---------|
| `this` | The element itself |
| `closest tr` | Nearest ancestor (or self) matching `tr` |
| `find .error` | First child/descendant matching `.error` |
| `next .sibling` | Next sibling matching `.sibling` |
| `previous .sibling` | Previous sibling matching `.sibling` |

These are powerful for table rows: `hx-target="closest tr"` means "find the `<tr>` I'm inside" without needing an ID.

---

## Status-Code Conditional Behaviour (v4 Feature)

**This is a v4 feature we depend on.** It lets you declare different targets/swaps based on the HTTP response status.

```html
<form hx-post="/users"
      hx-target="#user-table-body"
      hx-swap="append"
      hx-status:422="target:#modal-container swap:innerHTML">
```

How this reads:
- **On 2xx:** append the response to `#user-table-body` (the normal `hx-target` + `hx-swap`)
- **On 422:** swap the response into `#modal-container` using `innerHTML` (the override)

**Important v4 behaviour:** non-2xx responses don't swap by default. You must use `hx-status:XXX` to opt in to swapping error responses. Without it, a 422 would silently do nothing.

**Patterns:**

| Syntax | Meaning |
|--------|---------|
| `hx-status:422="target:#el swap:innerHTML"` | Override target and swap |
| `hx-status:422="target:#el"` | Override target only (swap stays default) |
| `hx-status:404="none"` | Don't swap at all on 404 |
| `hx-status:5xx="target:#error"` | Wildcard — any 5xx status |

**v2 fallback:** If reverting to HTMX v2, replace `hx-status:422` with server-side `HX-Retarget` and `HX-Reswap` response headers. v2 swaps all 2xx responses by default and ignores others unless you handle it in `htmx:beforeSwap` event.

---

## Other Useful Attributes

| Attribute | What it does | Example |
|-----------|-------------|---------|
| `hx-trigger` | What event triggers the request | `hx-trigger="click"`, `hx-trigger="change"`, `hx-trigger="keyup delay:500ms"` |
| `hx-confirm` | Shows a confirmation dialog before sending | `hx-confirm="Are you sure?"` |
| `hx-boost` | Converts normal links/forms to HTMX requests | `hx-boost="true"` on a `<nav>` upgrades all `<a>` inside it |
| `hx-include` | Include additional inputs in the request | `hx-include="#search-field"` |
| `hx-vals` | Add extra values to the request | `hx-vals='{"key": "value"}'` |
| `hx-headers` | Add extra HTTP headers | `hx-headers='{"X-Custom": "value"}'` |
| `hx-push-url` | Push URL into browser history | `hx-push-url="true"` |
| `hx-select` | Select a fragment from the response | `hx-select="#just-this-part"` |
| `hx-disable` | Disable HTMX processing on an element | `hx-disable` |
| `hx-validate` | Force HTML5 form validation before request | `hx-validate="true"` |

### Attribute Inheritance (v4 Change)

In v4, attributes do **not** inherit from parent elements by default. Use the `:inherited` modifier to opt in:

```html
<div hx-confirm:inherited="Are you sure?" hx-target:inherited="#result">
    <!-- All buttons inside inherit the confirm and target -->
    <button hx-delete="/thing/1">Delete A</button>
    <button hx-delete="/thing/2">Delete B</button>
</div>
```

**v2 difference:** In v2, most attributes inherited automatically. If migrating back to v2, remove `:inherited` modifiers.

---

## Response Headers (Server → HTMX)

These are HTTP headers your PHP Actions can set on the response to control HTMX behaviour.

| Header | What it does | Example |
|--------|-------------|---------|
| `HX-Trigger` | Fire custom events on the body element | `HX-Trigger: closeModal` |
| `HX-Trigger` (with data) | Fire events with payload | `HX-Trigger: {"showFlashMessage": {"type": "success", "message": "Done"}}` |
| `HX-Redirect` | Full page redirect | `HX-Redirect: /users/list` |
| `HX-Location` | Client-side redirect (no full reload) | `HX-Location: /users/list` |
| `HX-Retarget` | Override the target element | `HX-Retarget: #other-element` |
| `HX-Reswap` | Override the swap method | `HX-Reswap: outerHTML` |
| `HX-Push-Url` | Push a URL into browser history | `HX-Push-Url: /users/5` |
| `HX-Refresh` | Full page refresh | `HX-Refresh: true` |

### HX-Trigger In Detail

This is the header we use most. It fires DOM events that JavaScript can listen to.

**Simple event (no data):**
```php
$response->withHeader('HX-Trigger', 'closeModal');
```

**Event with data:**
```php
$response->withHeader('HX-Trigger', json_encode([
    'showFlashMessage' => [
        'type' => 'success',
        'message' => 'User created.',
    ],
]));
```

**Multiple events in one header:**
```php
$response->withHeader('HX-Trigger', json_encode([
    'showFlashMessage' => ['type' => 'success', 'message' => 'Done.'],
    'closeModal' => true,
]));
```

**Listening in JS:**
```javascript
document.body.addEventListener('showFlashMessage', function(event) {
    // event.detail contains the data from HX-Trigger
    console.log(event.detail.message);
});
```

---

## Request Headers (HTMX → Server)

Headers that HTMX sends automatically on every request. Useful for detecting HTMX requests server-side.

| Header | Value | Use case |
|--------|-------|----------|
| `HX-Request` | `"true"` | Detect if a request came from HTMX |
| `HX-Target` | Element ID | Know what element HTMX intends to swap |
| `HX-Current-URL` | Full URL | Know what page the user is on |

**PHP example — detect HTMX request:**
```php
$isHtmx = $request->getHeaderLine('HX-Request') === 'true';
```

---

## CSS Classes

HTMX adds these classes during the request lifecycle. Useful for loading indicators.

| Class | When it's applied |
|-------|-------------------|
| `htmx-request` | While the request is in flight |
| `htmx-swapping` | Just before the content swap |
| `htmx-settling` | After swap, during the settle phase |
| `htmx-added` | Applied to new content before swap |

**Loading indicator example:**
```css
.htmx-request .my-spinner { display: inline-block; }
.my-spinner { display: none; }
```

```html
<button hx-get="/data" hx-target="#result">
    Load <span class="my-spinner">...</span>
</button>
```

---

## Events

HTMX v4 uses a `htmx:phase:action` naming pattern (different from v2).

| Event | When it fires |
|-------|---------------|
| `htmx:before:request` | Before a request is sent. `preventDefault()` cancels it. |
| `htmx:after:request` | After a request completes (success or error) |
| `htmx:config:request` | After request is configured, before it's sent. Modify headers, URL, etc. |
| `htmx:before:swap` | Before content is swapped in. `preventDefault()` cancels the swap. |
| `htmx:after:swap` | After content is swapped in |
| `htmx:after:process` | After HTMX processes new elements (like `onLoad` — use for init) |
| `htmx:error` | On connection errors |
| `htmx:confirm` | Before `hx-confirm` dialog. Can override with custom confirm UI. |

**v2 name mapping** (if reverting):

| v4 | v2 |
|----|----|
| `htmx:before:request` | `htmx:beforeRequest` |
| `htmx:after:request` | `htmx:afterRequest` |
| `htmx:before:swap` | `htmx:beforeSwap` |
| `htmx:after:swap` | `htmx:afterSwap` |
| `htmx:config:request` | `htmx:configRequest` |

---

## Out-of-Band Swaps

For cases where a single server response needs to update multiple parts of the page.

### hx-swap-oob (On Response Elements)

```html
<!-- Main response — swapped into the target as normal -->
<tr id="user-row-5">...</tr>

<!-- Out-of-band — swapped by matching ID, regardless of target -->
<div id="user-count" hx-swap-oob="true">42 users</div>
```

The second element gets swapped into the existing `#user-count` element anywhere on the page.

### hx-partial (v4 Feature)

A cleaner alternative to OOB for multi-target responses:

```html
<hx-partial hx-target="#messages" hx-swap="append">
    <div>New message</div>
</hx-partial>

<hx-partial hx-target="#notification-count" hx-swap="innerHTML">
    <span>5</span>
</hx-partial>
```

Each `<hx-partial>` declares its own target and swap. More explicit than OOB.

---

## Morphing (v4 Feature)

Morphing intelligently merges new HTML into existing DOM, preserving:
- Focus state (cursor stays in the input you're typing in)
- Scroll position
- Video/audio playback
- Form values that haven't been submitted
- Event listeners

Use it when swapping large sections where you want to preserve user state:

```html
<form hx-put="/users/5" hx-target="#user-form" hx-swap="outerMorph">
```

**When to use morph vs regular swap:**
- Use `outerHTML`/`innerHTML` for simple replacements (table rows, form reloads after error)
- Use `outerMorph`/`innerMorph` for complex UI where preserving state matters

---

## Extensions (v4 Change)

Extensions no longer use the `hx-ext` attribute. Load them via script tag and approve via meta:

```html
<meta name="htmx-config" content='{"extensions": "preload,sse"}'>
<script src="path/to/ext/preload.js"></script>
```

**Bundled extensions:**
- `alpine-compat` — Alpine.js integration
- `browser-indicator` — native browser loading indicator
- `head-support` — merge `<head>` tag content
- `htmx-2-compat` — backwards compatibility with v2 behaviour
- `optimistic` — show expected content before server responds
- `preload` — prefetch on hover/focus
- `sse` — Server-Sent Events
- `upsert` — update-or-insert swap strategy
- `ws` — WebSocket support

---

## Patterns Used in Kezuru

### Create (Modal Form → Table Row)

```
User clicks "New User"
    → hx-get fetches form partial into #modal-container
    → User fills form, submits
    → hx-post sends form data
    → Server validates:
        201: returns <tr> partial   → hx-target="#user-table-body" hx-swap="append"
        422: returns form + errors  → hx-status:422="target:#modal-container swap:innerHTML"
    → Server sends HX-Trigger: closeModal + showFlashMessage
```

### Update (Form → Same Form)

```
Edit page renders with pre-populated form
    → User edits, submits
    → hx-put sends form data
    → Server validates:
        200: returns updated form partial → hx-target="#user-form" hx-swap="outerHTML"
        422: returns form + errors        → hx-status:422 (same target)
    → Server sends HX-Trigger: showFlashMessage
```

### Delete (Row Removal)

```
From list page:
    → hx-delete with hx-confirm
    → Server returns empty body
    → hx-target="closest tr" hx-swap="delete" removes the row

From edit page:
    → hx-delete with hx-headers for context
    → Server returns HX-Redirect → full page navigation to list
```
