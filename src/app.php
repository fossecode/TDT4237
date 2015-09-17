<?php

use Slim\Slim;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use tdt4237\webapp\Auth;
use tdt4237\webapp\Hash;
use tdt4237\webapp\repository\UserRepository;
use tdt4237\webapp\repository\PostRepository;
use tdt4237\webapp\repository\CommentRepository;

require_once __DIR__ . '/../vendor/autoload.php';

chdir(__DIR__ . '/../');
chmod(__DIR__ . '/../web/uploads', 0700);

$app = new Slim([
    'templates.path' => __DIR__.'/webapp/templates/',
    'debug' => true,
    'view' => new Twig()

]);

$view = $app->view();
$view->parserExtensions = array(
    new TwigExtension(),
);

try {
    // Create (connect to) SQLite database in file
    $app->db = new PDO('sqlite:app.db');
    // Set errormode to exceptions
    $app->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo $e->getMessage();
    exit();
}

// Wire together dependencies

date_default_timezone_set("Europe/Oslo");

$app->hash = new Hash();
$app->userRepository = new UserRepository($app->db);
$app->postRepository = new PostRepository($app->db);
$app->commentRepository = new CommentRepository($app->db);
$app->auth = new Auth($app->userRepository, $app->hash);

$ns ='tdt4237\\webapp\\controllers\\';

// Home page at http://localhost:8080/
$app->get('/', $ns . 'IndexController:index');

// Login form
$app->get('/login', $ns . 'LoginController:index');
$app->post('/login', $ns . 'LoginController:login');

// New user
$app->get('/user/new', $ns . 'UserController:index')->name('newuser');
$app->post('/user/new', $ns . 'UserController:create');

// Edit logged in user
$app->get('/user/edit', $ns . 'UserController:showUserEditForm')->name('editprofile');
$app->post('/user/edit', $ns . 'UserController:receiveUserEditForm');

// Forgot password
$app->get('/forgot/:username', $ns . 'ForgotPasswordController:confirmForm');
$app->get('/forgot', $ns . 'ForgotPasswordController:forgotPassword');

$app->post('/forgot/:username', $ns . 'ForgotPasswordController:confirm');
$app->post('/forgot', $ns . 'ForgotPasswordController:submitName');

// Show a user by name
$app->get('/user/:username', $ns . 'UserController:show')->name('showuser');

// Show all users
$app->get('/users', $ns . 'UserController:all');

// Posts
$app->get('/posts/new', $ns . 'PostController:showNewPostForm')->name('createpost');
$app->post('/posts/new', $ns . 'PostController:create');

$app->get('/posts', $ns . 'PostController:index')->name('showposts');

$app->get('/posts/:postid', $ns . 'PostController:show');
$app->post('/posts/:postid', $ns . 'PostController:addComment');

// Log out
$app->get('/logout', $ns . 'UserController:logout')->name('logout');

// Admin restricted area
$app->get('/admin', $ns . 'AdminController:index')->name('admin');
$app->get('/admin/delete/post/:postid', $ns . 'AdminController:deletepost');
$app->get('/admin/delete/:username', $ns . 'AdminController:delete');


return $app;
