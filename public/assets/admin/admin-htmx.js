/**
 * Admin HTMX event listeners.
 *
 * Handles custom events triggered by HTMX response headers (HX-Trigger).
 * The server sends HX-Trigger headers with event names and data,
 * and HTMX fires them as DOM events on the document body.
 *
 * Two events are handled:
 * 1. closeModal   — removes the modal from the DOM after a successful create
 * 2. showFlashMessage — displays a toast notification (success, error, etc.)
 *
 * HTMX v4 note: event names from HX-Trigger headers fire directly as DOM events
 * on the body element. This is the same in v2 and v4.
 */


// --- Close Modal ---
// Fired by the server after a successful create (via HX-Trigger header).
// Removes the modal element so the user sees the updated table underneath.
document.body.addEventListener('closeModal', function () {
    var modal = document.getElementById('user-modal');
    if (modal) {
        modal.remove();
    }
});


// --- Flash Messages ---
// Fired by the server after any successful mutation (create, update, delete).
// The HX-Trigger header sends: { "showFlashMessage": { "type": "success", "message": "..." } }
// HTMX passes the value as event.detail.
document.body.addEventListener('showFlashMessage', function (event) {
    // event.detail contains the object from HX-Trigger: { type, message }
    var detail = event.detail || {};
    var type = detail.type || 'info';
    var message = detail.message || '';

    if (!message) {
        return;
    }

    showFlash(message, type);
});

/**
 * Create and display a flash message toast.
 *
 * Renders a small notification bar at the top-right of the viewport.
 * Auto-dismisses after 4 seconds. Click to dismiss early.
 *
 * @param {string} message - The text to display
 * @param {string} type    - 'success', 'error', or 'info' — controls the colour
 */
function showFlash(message, type) {
    // Find or create the flash container (anchored top-right)
    var container = document.getElementById('flash-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'flash-container';
        container.style.cssText = 'position:fixed;top:1rem;right:1rem;z-index:50;display:flex;flex-direction:column;gap:0.5rem;';
        document.body.appendChild(container);
    }

    // Pick colour based on type
    var bgClass = 'bg-primary';
    if (type === 'success') {
        bgClass = 'bg-green-600';
    } else if (type === 'error') {
        bgClass = 'bg-red-600';
    }

    // Build the toast element
    var toast = document.createElement('div');
    toast.className = bgClass + ' text-white px-4 py-3 rounded-lg shadow-lg text-sm cursor-pointer transition-opacity duration-300';
    toast.textContent = message;
    toast.style.opacity = '0';

    // Click to dismiss
    toast.addEventListener('click', function () {
        dismissToast(toast);
    });

    // Add to container and fade in
    container.appendChild(toast);

    // Force reflow before setting opacity so the transition fires
    toast.offsetHeight; // eslint-disable-line no-unused-expressions
    toast.style.opacity = '1';

    // Auto-dismiss after 4 seconds
    setTimeout(function () {
        dismissToast(toast);
    }, 4000);
}

/**
 * Fade out and remove a toast element.
 *
 * @param {HTMLElement} toast - The toast element to remove
 */
function dismissToast(toast) {
    toast.style.opacity = '0';
    // Wait for the CSS transition to finish before removing from DOM
    setTimeout(function () {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 300);
}
