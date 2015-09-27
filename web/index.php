<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 'on');

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

if (preg_match('/\.(?:png|jpg|jpeg|gif|txt|css|js)$/', $_SERVER["REQUEST_URI"]))
    return false; // serve the requested resource as-is.
else {
    $app = require __DIR__ . '/../src/app.php';
    $app->run();
}
