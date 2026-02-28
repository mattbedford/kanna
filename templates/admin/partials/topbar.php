<?php
/**
 * Admin topbar partial.
 *
 * Minimal top bar: page title on the left, user menu on the right.
 * Included by admin/layout.php via $this->fetch().
 *
 * @var Slim\Views\PhpRenderer $this
 * @var string|null $pageTitle Set by the page template before rendering
 * @var array<string, mixed> $config
 */
?>

<header class="bg-surface border-b border-default px-8 py-4 flex items-center justify-between">

    <!-- Page title -->
    <h1 class="text-lg font-semibold text-heading">
        <?php echo html($pageTitle ?? 'Dashboard'); ?>
    </h1>

    <!-- Right side: theme toggle + user menu -->
    <div class="flex items-center gap-4">

        <!-- Dark mode toggle -->
        <button id="theme-toggle"
                class="p-2 rounded-lg text-muted hover:text-heading hover:bg-surface-alt transition-colors"
                aria-label="Toggle dark mode">
            <!-- Sun icon (shown in dark mode) — Ionicons: sunny-outline -->
            <span id="theme-icon-light" class="w-5 h-5 hidden">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="32" d="M256 48v48m0 320v48m147.08-355.08-33.94 33.94M142.86 369.14l-33.94 33.94M464 256h-48m-320 0H48m355.08 147.08-33.94-33.94M142.86 142.86l-33.94-33.94"/><circle cx="256" cy="256" r="80" fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="32"/></svg>
            </span>
            <!-- Moon icon (shown in light mode) — Ionicons: moon-outline -->
            <span id="theme-icon-dark" class="w-5 h-5 hidden">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M160 136c0-30.62 4.51-61.61 16-88C99.57 81.27 48 159.32 48 248c0 119.29 96.71 216 216 216 88.68 0 166.73-51.57 200-128-26.39 11.49-57.38 16-88 16-119.29 0-216-96.71-216-216"/></svg>
            </span>
        </button>

        <!-- User menu -->
        <div class="flex items-center gap-2 cursor-pointer">
            <!-- Avatar placeholder -->
            <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
                <span class="text-inverse text-xs font-semibold">A</span>
            </div>
            <span class="text-sm text-muted hidden sm:inline">Admin</span>
        </div>

    </div>

</header>
