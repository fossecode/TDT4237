<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\repository\UserRepository;

class LoginController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($this->auth->check()) {
            $username = $this->auth->user()->getUsername();
            $this->app->flash('info', 'You are already logged in as ' . $username);
            $this->app->redirect('/');
            return;
        }

        $this->render('login.twig', []);
    }

    public function login()
    {
        $request = $this->app->request;
        $ip      = $request->getIp();
        $user    = $request->post('user');
        $pass    = $request->post('pass');

        if ($this->auth->checkCredentials($user, $pass)) {
            $_SESSION['user'] = $user;
            
            if ($this->auth->user()->isAdmin()) {
                $_SESSION['isadmin'] = "yes";
            }

            $this->app->flash('info', "You are now successfully logged in as $user.");
            $this->app->redirect('/');
            return;
        } else {

            # Throttle failed login attempts
            $attemptedUser = $this->userRepository->findByUsername($user);

            if ($attemptedUser !== false) {
                $userId = $attemptedUser->getUserId();
                $this->app->throttling->registerEntry($userId, $ip);
                $this->app->throttling->delay($ip);
            }

        }
        
        $this->app->flashNow('error', 'Incorrect user/pass combination.');
        $this->render('login.twig', []);
    }
}
