<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 'off');
ini_set('session.cookie_httponly', 1);

require __DIR__ . '/../src/webapp/HarmlessPathFilter.php';
use tdt4237\webapp\HarmlessPathFilter;

if (! extension_loaded('openssl')) {
    die('You must enable the openssl extension.');
}

session_cache_limiter(false);
session_start();

if (! isset($_SESSION['request_counter']))
    $_SESSION['request_counter'] = 0;

if (! isset($_SESSION['CSRF_token']) || $_SESSION['CSRF_token'] == null){
	$_SESSION['CSRF_token'] = md5(uniqid(rand(), true));
}

# Disallow embedding. All iframes etc. will be blank, or contain a browser specific error page.
header("Content-Security-Policy: frame-ancestors 'none'");

$path = new HarmlessPathFilter($_SERVER["REQUEST_URI"]);
if ($path->isHarmless())
    return false;
else {
    $app = require __DIR__ . '/../src/app.php';
    $app->run();
}
