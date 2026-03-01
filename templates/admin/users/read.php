<?php
/**
 * Admin user edit page.
 *
 * Full page wrapper for the edit form. Sets the admin layout and page title,
 * then includes the edit form partial. The partial is what HTMX swaps on save.
 *
 * @var Slim\Views\PhpRenderer $this
 * @var App\Module\User\Data\UserData $user
 * @var Slim\Interfaces\RouteParserInterface $route
 * @var array<string, array<string>>|null $errors
 */

$this->setLayout('admin/layout.php');
$pageTitle = html($user->firstName . ' ' . $user->lastName);
?>

<div class="max-w-2xl">

    <!-- Back link -->
    <a href="<?php echo $route->urlFor('user-list-page'); ?>"
       class="text-muted hover:text-body text-sm mb-6 inline-flex items-center gap-1 transition-colors">
        &larr; Back to users
    </a>

    <!-- Edit form (partial — also returned standalone by the update Action) -->
    <?php echo $this->fetch('admin/users/partials/user-edit-form.php', [
        'user' => $user,
        'errors' => $errors ?? [],
    ]); ?>

</div>
