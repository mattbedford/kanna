<?php
/**
 * Admin dashboard page.
 *
 * The landing page for the admin area. No analytics, no charts —
 * just a useful starting point for content management.
 *
 * @var Slim\Views\PhpRenderer $this
 */

$this->setLayout('admin/layout.php');
$pageTitle = 'Dashboard';
?>

<div class="max-w-4xl">

    <p class="text-muted mb-8">Welcome to <?php echo html($config['app_name']); ?>.</p>

    <!-- Quick links — will grow as content collections are added -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        <a href="<?php echo $route->urlFor('user-list-page'); ?>"
           class="block bg-surface border border-default rounded-lg p-6 hover:border-primary transition-colors">
            <h2 class="text-heading font-semibold mb-1">Users</h2>
            <p class="text-muted text-sm">Manage user accounts and permissions.</p>
        </a>

    </div>

</div>
