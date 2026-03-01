<?php
/**
 * User edit form partial.
 *
 * The form card returned by the update Action for HTMX swap.
 * Also included by the full read.php page template via fetch().
 *
 * On success: server returns this partial with updated data → HTMX swaps it in.
 * On 422: server returns this partial with errors → HTMX swaps it in (same target).
 *
 * @var Slim\Views\PhpRenderer $this
 * @var App\Module\User\Data\UserData $user
 * @var Slim\Interfaces\RouteParserInterface $route
 * @var array<string, array<string>>|null $errors
 */

$errors = $errors ?? [];
?>
<div id="user-form" class="bg-surface border border-default rounded-lg p-6 mt-4">
    <!--
        HTMX v4 feature: hx-status:422 enables swapping on validation error.
        Without it, v4 skips non-2xx responses by default.
        Both success and error swap into the same target (#user-form outerHTML).
        v4 fallback: without hx-status:422, validation errors would silently fail to swap.
    -->
    <form hx-put="<?php echo $route->urlFor('user-update-submit', ['user_id' => (string)$user->id]); ?>"
          hx-target="#user-form"
          hx-swap="outerHTML"
          hx-status:422="target:#user-form swap:outerHTML"
          class="space-y-4">

        <div>
            <label for="first_name" class="block text-sm font-medium mb-1">First Name</label>
            <input type="text" name="first_name" id="first_name"
                   value="<?php echo html($user->firstName ?? ''); ?>"
                   class="w-full px-3 py-2 bg-surface border border-default rounded-lg text-sm focus:outline-none focus:border-primary <?php echo isset($errors['first_name']) ? 'border-danger' : ''; ?>">
            <?php if (isset($errors['first_name'])): ?>
                <p class="text-danger text-xs mt-1"><?php echo html($errors['first_name'][0]); ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="last_name" class="block text-sm font-medium mb-1">Last Name</label>
            <input type="text" name="last_name" id="last_name"
                   value="<?php echo html($user->lastName ?? ''); ?>"
                   class="w-full px-3 py-2 bg-surface border border-default rounded-lg text-sm focus:outline-none focus:border-primary <?php echo isset($errors['last_name']) ? 'border-danger' : ''; ?>">
            <?php if (isset($errors['last_name'])): ?>
                <p class="text-danger text-xs mt-1"><?php echo html($errors['last_name'][0]); ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="email" class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" id="email"
                   value="<?php echo html($user->email ?? ''); ?>"
                   class="w-full px-3 py-2 bg-surface border border-default rounded-lg text-sm focus:outline-none focus:border-primary <?php echo isset($errors['email']) ? 'border-danger' : ''; ?>">
            <?php if (isset($errors['email'])): ?>
                <p class="text-danger text-xs mt-1"><?php echo html($errors['email'][0]); ?></p>
            <?php endif; ?>
        </div>

        <div class="text-muted text-xs pt-2">
            Created <?php echo $user->createdAt?->format('d M Y, H:i') ?? '—'; ?>
            · Updated <?php echo $user->updatedAt?->format('d M Y, H:i') ?? '—'; ?>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-default">
            <button hx-delete="<?php echo $route->urlFor('user-delete-submit', ['user_id' => (string)$user->id]); ?>"
                    hx-confirm="Are you sure you want to delete this user?"
                    hx-headers='{"X-Delete-Context": "edit-page"}'
                    hx-target="body"
                    class="text-danger text-sm hover:underline"
                    type="button">Delete user</button>

            <button type="submit"
                    class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                Save Changes
            </button>
        </div>
    </form>
</div>
