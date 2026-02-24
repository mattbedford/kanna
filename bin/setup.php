#!/usr/bin/env php
<?php

/**
 * Kanna Setup Script
 *
 * Run once after cloning the project to prepare the development environment.
 * Usage: php bin/setup.php
 */

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

/**
 * Print a status message to the terminal.
 *
 * We use a simple convention:
 *   ✓  = created / completed
 *   –  = already exists, skipped
 *   ✗  = something went wrong
 */
function info(string $message): void
{
    echo "  ✓  {$message}\n";
}

function skip(string $message): void
{
    echo "  –  {$message}\n";
}

function fail(string $message): void
{
    echo "  ✗  {$message}\n";
}

// ---------------------------------------------------------------------------
// Resolve project root (one level up from bin/)
// ---------------------------------------------------------------------------

$root = dirname(__DIR__);

echo "\n";
echo "  Kanna – Project Setup\n";
echo "  =====================\n\n";

// ---------------------------------------------------------------------------
// Step 1 — Create directories
// ---------------------------------------------------------------------------

echo "  Directories\n";
echo "  -----------\n";

$directories = [
    'storage',
    'storage/uploads',
    'logs',
];

foreach ($directories as $dir) {
    $path = $root . '/' . $dir;

    if (is_dir($path)) {
        skip("{$dir}/ already exists");
    } elseif (mkdir($path, 0755, true)) {
        info("{$dir}/ created");
    } else {
        fail("Could not create {$dir}/");
    }
}

echo "\n";

// ---------------------------------------------------------------------------
// Step 2 — SQLite database
// ---------------------------------------------------------------------------

echo "  Database\n";
echo "  --------\n";

$dbPath = $root . '/storage/database.sqlite';

if (file_exists($dbPath)) {
    skip("storage/database.sqlite already exists");
} elseif (touch($dbPath)) {
    info("storage/database.sqlite created");
} else {
    fail("Could not create storage/database.sqlite");
}

echo "\n";

// ---------------------------------------------------------------------------
// Step 3 — Tailwind CSS standalone CLI
// ---------------------------------------------------------------------------

echo "  Tailwind CSS CLI\n";
echo "  ----------------\n";

$tailwindPath = $root . '/bin/tailwindcss';

if (file_exists($tailwindPath)) {
    skip("bin/tailwindcss already exists");
} else {
    // Detect platform and architecture
    $os = PHP_OS_FAMILY; // 'Linux', 'Darwin' (macOS), 'Windows'
    $arch = php_uname('m');  // 'x86_64', 'aarch64', 'arm64'

    // Map to Tailwind's release naming convention
    $platformMap = [
        'Linux'  => 'linux',
        'Darwin' => 'macos',
    ];

    $archMap = [
        'x86_64'  => 'x64',
        'amd64'   => 'x64',
        'aarch64' => 'arm64',
        'arm64'   => 'arm64',
    ];

    $platform = $platformMap[$os] ?? null;
    $archLabel = $archMap[$arch] ?? null;

    if ($platform === null || $archLabel === null) {
        fail("Unsupported platform: {$os} {$arch}");
        fail("Download manually from https://github.com/tailwindlabs/tailwindcss/releases/latest");
    } else {
        $filename = "tailwindcss-{$platform}-{$archLabel}";
        $url = "https://github.com/tailwindlabs/tailwindcss/releases/latest/download/{$filename}";

        info("Detected platform: {$platform}-{$archLabel}");
        echo "  …  Downloading from GitHub (this may take a moment)\n";

        // Use curl — available on virtually all Linux/Mac systems
        $command = sprintf(
            'curl -sL -o %s %s 2>&1',
            escapeshellarg($tailwindPath),
            escapeshellarg($url)
        );

        exec($command, $output, $exitCode);

        if ($exitCode !== 0 || !file_exists($tailwindPath) || filesize($tailwindPath) < 1000) {
            // Clean up a failed/empty download
            if (file_exists($tailwindPath)) {
                unlink($tailwindPath);
            }
            fail("Download failed (exit code: {$exitCode})");
            fail("Try manually: curl -sLo bin/tailwindcss {$url} && chmod +x bin/tailwindcss");
        } else {
            chmod($tailwindPath, 0755);
            $sizeMB = round(filesize($tailwindPath) / 1024 / 1024, 1);
            info("bin/tailwindcss downloaded ({$sizeMB} MB)");
            info("bin/tailwindcss made executable");
        }
    }
}

echo "\n";

// ---------------------------------------------------------------------------
// Step 4 — Initial Tailwind CSS build
// ---------------------------------------------------------------------------

echo "  Tailwind CSS Build\n";
echo "  ------------------\n";

$inputCss = $root . '/resources/css/app.css';
$outputCss = $root . '/public/assets/css/app.css';

if (!file_exists($tailwindPath)) {
    skip("Tailwind CLI not available — skipping build");
} elseif (!file_exists($inputCss)) {
    skip("resources/css/app.css not found — skipping build");
} else {
    // Ensure the output directory exists
    $outputDir = dirname($outputCss);
    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0755, true);
    }

    echo "  …  Compiling CSS (first build may take a moment)\n";

    $command = sprintf(
        '%s -i %s -o %s --minify 2>&1',
        escapeshellarg($tailwindPath),
        escapeshellarg($inputCss),
        escapeshellarg($outputCss)
    );

    exec($command, $output, $exitCode);

    if ($exitCode !== 0) {
        fail("Tailwind build failed (exit code: {$exitCode})");
        if (!empty($output)) {
            fail(implode("\n      ", $output));
        }
    } else {
        $sizeKB = round(filesize($outputCss) / 1024, 1);
        info("public/assets/css/app.css compiled ({$sizeKB} KB)");
    }
}

echo "\n";

// ---------------------------------------------------------------------------
// Done
// ---------------------------------------------------------------------------

echo "  Setup complete. Happy building.\n\n";