<?php
/**
 * Admin user list page.
 *
 * Server-rendered table of all users. No JS data fetching —
 * PHP renders every row on the server before the page arrives.
 *
 * HTMX handles mutations (create, delete) without full page reloads.
 *
 * @var Slim\Views\PhpRenderer $this
 * @var App\Module\User\Data\UserData[] $users
 * @var Slim\Interfaces\RouteParserInterface $route
 */

$this->setLayout('admin/layout.php');
$pageTitle = 'Users';
?>

<div class="max-w-5xl">

    <!-- Header row: create button -->
    <div class="flex items-center justify-end mb-6">
        <button hx-get="<?php echo $route->urlFor('user-create-form'); ?>"
                hx-target="#modal-container"
                hx-swap="innerHTML"
                class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
            New User
        </button>
    </div>

    <!-- User table -->
    <div class="bg-surface border border-default rounded-lg overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-default bg-surface-alt/50">
                    <th class="px-6 py-4 text-left text-xs font-medium text-muted uppercase tracking-wider">First Name</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-muted uppercase tracking-wider">Last Name</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-muted uppercase tracking-wider">Email</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-muted uppercase tracking-wider">Created</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-muted uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody id="user-table-body">
                <?php foreach ($users as $user): ?>
                    <?php echo $this->fetch('admin/users/partials/user-row.php', ['user' => $user]); ?>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($users)): ?>
            <div class="px-4 py-12 text-center text-muted">
                <p>No users yet.</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- Modal container — HTMX injects form HTML here -->
<div id="modal-container"></div>
