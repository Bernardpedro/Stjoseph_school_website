<?php

use CodeIgniter\Boot;
use Config\Paths;

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin === '' || preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?$#i', $origin)) {
    header('Access-Control-Allow-Origin: ' . ($origin !== '' ? $origin : '*'));
} else {
    header('Access-Control-Allow-Origin: *');
}
header('Vary: Origin');
header('Access-Control-Allow-Headers: Authorization, Content-Type, Accept, Origin, X-Requested-With, X-Locale, X-Nuxt-Locale');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Max-Age: 86400');

if (isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Windows Apache keeps the browser casing in REQUEST_URI (/Beno/...) while
// SCRIPT_NAME uses the real folder (/beno/...). CI4 compares them case-sensitively
// and then treats the whole subdirectory as the route (Failed to fetch / 404).
if (isset($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME'])) {
    $backendDir = str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'])));
    $uri        = $_SERVER['REQUEST_URI'];
    $qPos       = strpos($uri, '?');
    $path       = $qPos === false ? $uri : substr($uri, 0, $qPos);
    $query      = $qPos === false ? '' : substr($uri, $qPos);

    if ($backendDir !== '/' && $backendDir !== '' && stripos($path, $backendDir) === 0) {
        $_SERVER['REQUEST_URI'] = $backendDir . substr($path, strlen($backendDir)) . $query;
    }
}

/*
 *---------------------------------------------------------------
 * CHECK PHP VERSION
 *---------------------------------------------------------------
 */

$minPhpVersion = '8.2'; // If you update this, don't forget to update `spark`.
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION,
    );

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;

    exit(1);
}

/*
 *---------------------------------------------------------------
 * SET THE CURRENT DIRECTORY
 *---------------------------------------------------------------
 */

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// LOAD OUR PATHS CONFIG FILE
// This is the line that might need to be changed, depending on your folder structure.
require FCPATH . '../app/Config/Paths.php';
// ^^^ Change this line if you move your application folder

$paths = new Paths();

// LOAD THE FRAMEWORK BOOTSTRAP FILE
require $paths->systemDirectory . '/Boot.php';

exit(Boot::bootWeb($paths));
