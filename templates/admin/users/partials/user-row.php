<?php
/**
 * Single user table row partial.
 *
 * Used in two places:
 * 1. Inside the foreach loop on the list page (initial render)
 * 2. Returned as an HTMX response after creating a user (appended to table)
 *
 * @var App\Module\User\Data\UserData $user
 * @var Slim\Interfaces\RouteParserInterface $route
 */
?>
<tr id="user-row-<?php echo $user->id; ?>" class="border-b border-default hover:bg-surface-alt/50 transition-colors">
    <td class="px-6 py-4 text-sm"><?php echo html($user->firstName); ?></td>
    <td class="px-6 py-4 text-sm"><?php echo html($user->lastName); ?></td>
    <td class="px-6 py-4 text-sm"><?php echo html($user->email); ?></td>
    <td class="px-6 py-4 text-sm text-muted"><?php echo $user->createdAt?->format('d M Y') ?? '—'; ?></td>
    <td class="px-6 py-4 text-sm text-right space-x-2">
        <a href="<?php echo $route->urlFor('user-read-page', ['user_id' => (string)$user->id]); ?>"
           class="inline-block px-3 py-1 text-xs font-medium rounded border border-primary text-primary hover:bg-primary hover:text-white transition-colors">Edit</a>

        <!-- HTMX v4: hx-swap="delete" removes the target element on success.
             v4 fallback: if "delete" swap isn't supported, use hx-swap="outerHTML"
             and have the server return an empty body. -->
        <button hx-delete="<?php echo $route->urlFor('user-delete-submit', ['user_id' => (string)$user->id]); ?>"
                hx-target="closest tr"
                hx-swap="delete"
                hx-confirm="Are you sure you want to delete this user?"
                class="px-3 py-1 text-xs font-medium rounded border border-danger text-danger hover:bg-danger hover:text-white transition-colors">Delete</button>
    </td>
</tr>
