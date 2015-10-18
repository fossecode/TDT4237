<?php

namespace tdt4237\webapp\controllers;

class Controller
{
    protected $app;
    
    protected $userRepository;
    protected $auth;
    protected $hash;
    protected $postRepository;

    public function __construct()
    {
        $this->app = \Slim\Slim::getInstance();
        $this->userRepository = $this->app->userRepository;
        $this->postRepository = $this->app->postRepository;
        $this->commentRepository = $this->app->commentRepository;
        $this->paymentRepository = $this->app->paymentRepository;
        $this->auth = $this->app->auth;
        $this->hash = $this->app->hash;
    }

    protected function render($template, $variables = [])
    {
        if ($this->auth->check()) {
            $variables['isLoggedIn'] = true;
            $variables['isAdmin'] = $this->auth->isAdmin();
            $variables['loggedInUsername'] = $_SESSION['user'];
            $user = $this->userRepository->findByUserId($_SESSION['userId']);
            if($user->isDoctor()){
                $variables['balance'] = $this->paymentRepository->getDoctorPayments($_SESSION['userId']);
            } else if($this->paymentRepository->getUserPayments($user->getUserId()) !== 0){
                $variables['balance'] = $this->paymentRepository->getUserPayments($user->getUserId());
            }
        }
        $variables['csrf'] = $_SESSION['CSRF_token'];

        print $this->app->render($template, $variables);
    }
}
