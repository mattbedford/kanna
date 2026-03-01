<?php
/**
 * Admin layout.
 *
 * The outer shell for all admin pages. Provides the sidebar, topbar,
 * and content area grid. Page templates render into $content.
 *
 * @var Slim\Views\PhpRenderer $this
 * @var string $content Page content injected by PhpRenderer
 * @var string $basePath
 * @var Slim\Interfaces\RouteParserInterface $route
 * @var string $currRouteName
 * @var Psr\Http\Message\UriInterface $uri
 * @var array<string, mixed> $config
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?php echo html($basePath); ?>/"/>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>

    <?php
    $adminCss = [
        'assets/css/app.css',
    ];
    $adminJs = [
        'assets/general/dark-mode/dark-mode.js',
    ];
    $adminJsModules = [];

    echo $this->fetch('shared/assets.php', [
        'stylesheets' => array_merge($adminCss, $css ?? []),
        'scripts' => array_merge($adminJs, $js ?? []),
        'jsModules' => array_merge($adminJsModules, $jsModules ?? []),
    ]);
    ?>

    <title><?php echo html($config['app_name']); ?> — Admin</title>

    <script>
        document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') ?? 'light');
    </script>
</head>
<body class="bg-surface-alt text-body min-h-screen">

    <div class="grid grid-cols-[260px_1fr] min-h-screen">

        <!-- Sidebar -->
        <?php echo $this->fetch('admin/partials/sidebar.php'); ?>

        <!-- Main area: topbar + content -->
        <div class="grid grid-rows-[auto_1fr] min-h-screen">

            <!-- Topbar -->
            <?php echo $this->fetch('admin/partials/topbar.php'); ?>

            <!-- Content area -->
            <main class="p-8 overflow-y-auto">
                <?php echo $content; ?>
            </main>

        </div>

    </div>

</body>
</html>
