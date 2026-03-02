<?php
/**
 * Front Controller — All requests go through here
 */

session_start();

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Load configuration (also loads .env)
$config = require BASE_PATH . '/config/app.php';
define('CONFIG', $config);

// Simple autoloader
spl_autoload_register(function ($class) {
    // Convert namespace to path: App\Core\Database -> app/Core/Database.php
    $prefix = 'App\\';
    if (strncmp($prefix, $class, strlen($prefix)) !== 0)
        return;
    $relativeClass = substr($class, strlen($prefix));
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Get the URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/') ?: '/';

// Initialize and run the router
$app = new App\Core\App();

// =====================
// ROUTES
// =====================

// Auth
$app->get('/', 'AuthController@loginForm');
$app->get('/login', 'AuthController@loginForm');
$app->post('/login', 'AuthController@login');
$app->get('/register', 'AuthController@registerForm');
$app->post('/register', 'AuthController@register');
$app->get('/logout', 'AuthController@logout');

// Dashboard
$app->get('/dashboard', 'DashboardController@index');

// Contacts
$app->get('/contacts', 'ContactController@index');
$app->get('/contacts/create', 'ContactController@create');
$app->post('/contacts/store', 'ContactController@store');
$app->get('/contacts/edit', 'ContactController@edit');
$app->post('/contacts/update', 'ContactController@update');
$app->post('/contacts/delete', 'ContactController@delete');
$app->get('/contacts/import', 'ContactController@importForm');
$app->post('/contacts/import', 'ContactController@import');
$app->get('/contacts/template', 'ContactController@downloadTemplate');
$app->post('/contacts/assign-tags', 'ContactController@assignTags');

// Tags
$app->get('/tags', 'TagController@index');
$app->post('/tags/store', 'TagController@store');
$app->post('/tags/update', 'TagController@update');
$app->post('/tags/delete', 'TagController@delete');

// Messages
$app->get('/messages', 'MessageController@index');
$app->get('/messages/compose', 'MessageController@compose');
$app->post('/messages/store', 'MessageController@store');
$app->post('/messages/preview', 'MessageController@preview');
$app->post('/messages/delete', 'MessageController@delete');

// Dispatch
$app->get('/dispatch', 'DispatchController@index');
$app->post('/dispatch/prepare', 'DispatchController@prepare');
$app->post('/dispatch/send', 'DispatchController@send');
$app->get('/dispatch/logs', 'DispatchController@logs');

// AI
$app->post('/ai/personalize', 'AIController@personalize');

// Settings
$app->get('/settings', 'SettingsController@index');
$app->post('/settings/update', 'SettingsController@update');

// Run
$app->run($uri, $_SERVER['REQUEST_METHOD']);
