<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\models\Age;
use tdt4237\webapp\models\Email;
use tdt4237\webapp\models\User;
use tdt4237\webapp\validation\EditUserFormValidation;
use tdt4237\webapp\validation\RegistrationFormValidation;

class UserController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($this->auth->guest()) {
            return $this->render('newUserForm.twig');
        }

        $username = $this->auth->user()->getUserName();
        $this->app->flash('info', 'You are already logged in as ' . $username);
        $this->app->redirect('/');
    }

    public function create()
    {
        $request  = $this->app->request;
        $username = $request->post('user');
        $password = $request->post('pass');
        $fullname = $request->post('fullname');
        $address = $request->post('address');
        $postcode = $request->post('postcode');
        $csrfToken = $request->post('csrf');

        $validation = new RegistrationFormValidation($username, $password, $fullname, $address, $postcode, $csrfToken, $this->userRepository);

        if ($validation->isGoodToGo()) {
            $password = $password;
            $password = $this->hash->make($password);
            $user = new User($username, $password, $fullname, $address, $postcode);
            $this->userRepository->save($user);

            $this->app->flash('info', 'Thanks for creating a user. Now log in.');
            return $this->app->redirect('/login');
        }

        $errors = $validation->getValidationErrors();
        $this->app->flashNow('errors', $errors);
        $this->render('newUserForm.twig', [
            'username' => $username
        ]);
    }

    public function all()
    {
        $this->render('users.twig', [
            'users' => $this->userRepository->all()
        ]);
    }

    public function logout()
    {
        $this->auth->logout();
        $this->app->redirect("/");
    }

    public function show($userId)
    {
        if ($this->auth->guest()) {
            $this->app->flash("info", "You must be logged in to do that");
            $this->app->redirect("/login");
            return;
        }
        
        $user = $this->userRepository->findByUserId($userId);

        if($user == false){
            $this->app->redirect("/user/edit");
            return;
        }
            
        //Does the following check enough?
        if ($user != false && $user->getUsername() == $this->auth->getUsername()) {

            $this->render('showuser.twig', [
                'foundUser' => $user,
                'username' => $user->getUsername()
            ]);
        } else if ($this->auth->check()) {

            $this->render('showuserlite.twig', [
                'foundUser' => $user,
                'username' => $user->getUsername()
            ]);
        }

    }

    public function showUserEditForm()
    {
        $this->makeSureUserIsAuthenticated();

        $this->render('edituser.twig', [
            'user' => $this->auth->user()
        ]);
    }


    public function receiveUserEditForm()
    {
        $this->makeSureUserIsAuthenticated();
        $user = $this->auth->user();

        $request = $this->app->request;
        $oldPassword = $request->post('old_password');
        $newPassword = $request->post('new_password');
        $email   = $request->post('email');
        $bio     = $request->post('bio');
        $age     = $request->post('age');
        $fullname = $request->post('fullname');
        $address = $request->post('address');
        $postcode = $request->post('postcode');
        $csrfToken = $request->post('csrf');
        $accountNumber = str_replace(".", "", $request->post('accountNumber'));

        $updateAccountNumber = (substr($accountNumber,0,6) != "******") && (! empty($accountNumber));
        $updatePassword = !empty($newPassword);

        $validation = new EditUserFormValidation($email, $bio, $age, $fullname, $address, $postcode, $csrfToken, $accountNumber, $updateAccountNumber);

        if ($updatePassword && $this->auth->checkCredentials($user->getUsername(), $oldPassword)) {
            if (strlen($newPassword) < 8 || strlen($newPassword) >= 50) {
                $validation->validationErrors[] = 'Password must be between 8 and 50 characters long.';
            } else {
                $user->setHash($this->hash->make($newPassword));
            }
        }

        if ($validation->isGoodToGo()) {
            $user->setEmail(new Email($email));
            $user->setBio($bio);
            $user->setAge(new Age($age));
            $user->setFullname($fullname);
            $user->setAddress($address);
            $user->setPostcode($postcode);

            if ($updateAccountNumber)
                $user->setAccountNumber($accountNumber);

            $this->userRepository->save($user);
            $this->auth->user = $user;
            $this->app->flashNow('info', 'Your profile was successfully saved.');
            return $this->render('edituser.twig',  [
                'user' => $user
            ]);
        }

        $this->app->flashNow('errors', $validation->getValidationErrors());
        $this->render('edituser.twig', [
            'user' => $user
        ]);
    }

    public function makeSureUserIsAuthenticated()
    {
        if ($this->auth->guest()) {
            $this->app->flash('info', 'You must be logged in to edit your profile.');
            $this->app->redirect('/login');
        }
    }

}
