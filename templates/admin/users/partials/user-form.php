<?php
/**
 * User create form partial (rendered inside a modal).
 *
 * Fetched via HTMX into #modal-container when "New User" is clicked.
 *
 * On successful create (201): the server returns a user-row.php partial,
 * which gets appended to #user-table-body.
 *
 * On validation error (422): the server returns this form again with
 * error messages, which replaces the modal content.
 *
 * @var Slim\Views\PhpRenderer $this
 * @var Slim\Interfaces\RouteParserInterface $route
 * @var App\Module\User\Data\UserData|null $user
 * @var array<string, array<string>>|null $errors Validation errors keyed by field name
 */

$errors = $errors ?? [];
?>

<!-- Modal backdrop -->
<div class="fixed inset-0 bg-black/50 z-40 flex items-start justify-center pt-24"
     id="user-modal"
     onclick="if(event.target===this)this.remove()">

    <div class="bg-surface border border-default rounded-lg shadow-xl w-full max-w-lg mx-4"
         onclick="event.stopPropagation()">

        <!-- Modal header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-default">
            <h2 class="text-lg font-semibold">New User</h2>
            <button onclick="document.getElementById('user-modal').remove()"
                    class="text-muted hover:text-body transition-colors text-xl leading-none"
                    type="button">&times;</button>
        </div>

        <!--
            HTMX v4 feature: hx-status:422 overrides target + swap on validation error.
            On 201 → appends new <tr> to #user-table-body (hx-target + hx-swap).
            On 422 → swaps form-with-errors into #modal-container (hx-status:422).
            v4 fallback: without hx-status, 422 wouldn't swap at all (v4 skips non-2xx by default).
        -->
        <form hx-post="<?php echo $route->urlFor('user-create-submit'); ?>"
              hx-target="#user-table-body"
              hx-swap="append"
              hx-status:422="target:#modal-container swap:innerHTML"
              class="p-6 space-y-4">

            <div>
                <label for="first_name" class="block text-sm font-medium mb-1">First Name</label>
                <input type="text" name="first_name" id="first_name"
                       value="<?php echo html($user->firstName ?? ''); ?>"
                       class="w-full px-3 py-2 bg-surface border border-default rounded-lg text-sm focus:outline-none focus:border-primary <?php echo isset($errors['first_name']) ? 'border-danger' : ''; ?>"
                       required>
                <?php if (isset($errors['first_name'])): ?>
                    <p class="text-danger text-xs mt-1"><?php echo html($errors['first_name'][0]); ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="last_name" class="block text-sm font-medium mb-1">Last Name</label>
                <input type="text" name="last_name" id="last_name"
                       value="<?php echo html($user->lastName ?? ''); ?>"
                       class="w-full px-3 py-2 bg-surface border border-default rounded-lg text-sm focus:outline-none focus:border-primary <?php echo isset($errors['last_name']) ? 'border-danger' : ''; ?>"
                       required>
                <?php if (isset($errors['last_name'])): ?>
                    <p class="text-danger text-xs mt-1"><?php echo html($errors['last_name'][0]); ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" id="email"
                       value="<?php echo html($user->email ?? ''); ?>"
                       class="w-full px-3 py-2 bg-surface border border-default rounded-lg text-sm focus:outline-none focus:border-primary <?php echo isset($errors['email']) ? 'border-danger' : ''; ?>"
                       required>
                <?php if (isset($errors['email'])): ?>
                    <p class="text-danger text-xs mt-1"><?php echo html($errors['email'][0]); ?></p>
                <?php endif; ?>
            </div>

            <!-- Footer -->
            <div class="flex justify-end gap-3 pt-2">
                <button type="button"
                        onclick="document.getElementById('user-modal').remove()"
                        class="px-4 py-2 text-sm text-muted hover:text-body transition-colors">Cancel</button>
                <button type="submit"
                        class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                    Create
                </button>
            </div>
        </form>

    </div>
</div>
