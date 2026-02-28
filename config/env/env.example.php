<?php

/**
 * Environment configuration.
 *
 * Copy this file to env.php and adjust for your environment.
 * env.php is gitignored — never commit secrets.
 */

// Environment: 'dev' or 'prod'
$settings['env'] = 'dev';

// Dev mode: show error details, enable JS cache busting
$settings['dev'] = true;
$settings['error']['display_error_details'] = true;
$settings['deployment']['update_js_imports_version'] = true;

// Database (SQLite is the default — override here for MariaDB/PostgreSQL)
// $settings['db']['driver'] = Cake\Database\Driver\Mysql::class;
// $settings['db']['host'] = 'localhost';
// $settings['db']['username'] = 'root';
// $settings['db']['password'] = '';
// $settings['db']['database'] = 'kezuru';

// CORS: allowed origin for API requests
$settings['api']['allowed_origin'] = 'http://localhost';
