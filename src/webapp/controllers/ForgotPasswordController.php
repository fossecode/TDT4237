<?php
namespace tdt4237\webapp\controllers;


class ForgotPasswordController extends Controller {

    public function __construct() {
        parent::__construct();
    }


    function forgotPassword() {
        $this->render('forgotPassword.twig');
    }

    function submitEmail() {
        $email = $this->app->request->post('email');
        if($email != "") {
            $this->app->flash('info','Thank you! The password was sent to your email, if the email exists');
            $this->app->redirect('/');
            // Code that sends an email if email exists
        }
        else {
            $this->render('forgotPassword.twig');
            $this->app->flash("errors", ["Please input a email"]);
        }

    }

    function deny() {

    }
} 