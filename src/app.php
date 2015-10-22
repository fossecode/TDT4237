<?php

use Slim\Slim;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use tdt4237\webapp\Auth;
use tdt4237\webapp\Throttling;
use tdt4237\webapp\Hash;
use tdt4237\webapp\repository\UserRepository;
use tdt4237\webapp\repository\PostRepository;
use tdt4237\webapp\repository\CommentRepository;
use tdt4237\webapp\repository\ThrottleRepository;
use tdt4237\webapp\repository\PaymentRepository;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/webapp/Logger.php';
use \Slim\Logger\DateTimeFileWriter;

require_once dirname('.').'/src/webapp/WAF.php';
use tdt4237\webapp\WAF;

require dirname('.').'/src/webapp/repository/BannedRepository.php';
use tdt4237\webapp\repository\BannedRepository;

chdir(__DIR__ . '/../');
chmod(__DIR__ . '/../web/uploads', 0700);

//Regenerate session id every 20th request to stop session hijacking and session fixation.
if (++$_SESSION['request_counter'] >= 20) {
    $_SESSION['request_counter'] = 0;
    session_regenerate_id(true);
}

$app = new Slim([
    'templates.path' => __DIR__.'/webapp/templates/',
    'debug' => false,
    'view' => new Twig(),
    'log.enabled' => true,
    'log.level' => \Slim\Log::DEBUG,
    'log.writer' => new \Slim\Logger\DateTimeFileWriter(array(
        'path' => realpath(dirname(dirname(__FILE__))) . '/log',
        'name_format' => 'Y-m-d',
        'message_format' => '%label% - %date% - %message%'
    ))
]);

$view = $app->view();
$view->parserExtensions = array(
    new TwigExtension(),
);

$twig = $view->getInstance();
$twig->addFilter(
    new Twig_SimpleFilter('obfuscateAccountNumber', function ($number){
        if(!$number)
            return "";
        return "*******" . substr($number,7);
    })
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

# Fire up the WAF 
$bannedRepository = new BannedRepository($app->db); 
$ip               = $_SERVER['REMOTE_ADDR'];
$request          = urldecode(implode($_POST, ' ') . $_SERVER['REQUEST_URI']);
$waf              = new Waf();

if ($waf->isMalicious($request))
    $bannedRepository->saveNewEntry($ip);

if ($bannedRepository->findByIp($ip))
    die($waf->getBanMessage());

// Wire together dependencies

date_default_timezone_set("Europe/Oslo");

$app->hash = new Hash();
$app->userRepository = new UserRepository($app->db);
$app->postRepository = new PostRepository($app->db, $app->userRepository);
$app->commentRepository = new CommentRepository($app->db, $app->userRepository);
$app->throttleRepository = new ThrottleRepository($app->db);
$app->auth = new Auth($app->userRepository, $app->hash);
$app->throttling = new Throttling($app->throttleRepository);
$app->paymentRepository = new PaymentRepository($app->db);

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
$app->get('/forgot', $ns . 'ForgotPasswordController:forgotPassword');
$app->post('/forgot', $ns . 'ForgotPasswordController:submitEmail');

// Show a user by name
$app->get('/user/:userId', $ns . 'UserController:show')->name('showuser');

// Show all users
//$app->get('/users', $ns . 'UserController:all');

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
$app->get('/admin/delete/:userId', $ns . 'AdminController:delete');
$app->get('/admin/makeDoctor/:userId', $ns . 'AdminController:makeDoctor');
$app->get('/admin/removeDoctor/:userId', $ns . 'AdminController:removeDoctor');

$app->hook('slim.after.router', function () use ($app) {
    $request  = $app->request;
    $response = $app->response;
    try {
        $app->log->debug('IP: '.$request->getIp().' UA: '.$request->getUserAgent().' Path: '.$request->getResourceUri());
    } catch (Exception $e) {

    }
});

// Remove headers that reveal webserver and CGI version 
header_remove("X-Powered-By");
header_remove("Server");

return $app;
