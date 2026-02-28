<?php

/**
 * Configuration loader.
 *
 * Loads defaults, then applies environment overrides from env.php.
 * Copy config/env/env.example.php to config/env/env.php to get started.
 */

$settings = require __DIR__ . '/defaults.php';

if (file_exists(__DIR__ . '/env/env.php')) {
    require __DIR__ . '/env/env.php';
}

return $settings;
