<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\Auth;
use tdt4237\webapp\models\User;
use tdt4237\webapp\validation\Validation;

class AdminController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($this->auth->guest()) {
            $this->app->flash('info', "You must be logged in to view the admin page.");
            $this->app->redirect('/');
        }

        if (! $this->auth->isAdmin()) {
            $this->app->flash('info', "You must be administrator to view the admin page.");
            $this->app->redirect('/');
        }

        $variables = [
            'users' => $this->userRepository->all(),
            'posts' => $this->postRepository->all()
        ];
        $this->render('admin.twig', $variables);
    }

    public function delete($userId)
    {
        if ($this->auth->isAdmin()) {
            $csrfToken = $this->app->request->get("token");
            $validation = new Validation($csrfToken);
            if($validation->isGoodToGo()){
                if ($this->userRepository->findByUserId($userId)->isAdmin()){
                    $this->app->flash('info', "You can not delete an administrator.");
                    $this->app->redirect('/admin');
                    return;
                } else if ($this->userRepository->deleteByUserId($userId)) {
                    $this->app->flash('info', "Sucessfully deleted user.");
                    $this->app->redirect('/admin');
                    return;
                }
            }
        }

        $this->app->flash('info', "An error ocurred. Unable to delete user.");
        $this->app->redirect('/');
    }

    public function deletePost($postId)
    {
        if ($this->auth->isAdmin()) {
            $csrfToken = $this->app->request->get("token");
            $validation = new Validation($csrfToken);
            if($validation->isGoodToGo()){
                if ($this->postRepository->deleteByPostid($postId)) {
                    $this->app->flash('info', "Sucessfully deleted post with id $postId");
                    $this->app->redirect('/admin');
                    return;
                }
            }
        }

        $this->app->flash('info', "An error ocurred. Unable to delete post.");
        $this->app->redirect('/');
    }

    public function makeDoctor($userId)
    {
        if ($this->auth->isAdmin()) {
            $csrfToken = $this->app->request->get("token");
            $validation = new Validation($csrfToken);
            if($validation->isGoodToGo()){
                if ($this->userRepository->makeDoctor($userId)) {
                    $this->app->flash('info', "Sucessfully made a doctor.");
                    $this->app->redirect('/admin');
                    
                    return;
                }
            }
        }

        $this->app->flash('info', "An error ocurred. Unable to make a doctor.");
        $this->app->redirect('/');
    }

    public function removeDoctor($userId)
    {
        if ($this->auth->isAdmin()) {
            $csrfToken = $this->app->request->get("token");
            $validation = new Validation($csrfToken);
            if($validation->isGoodToGo()){
                if ($this->userRepository->removeDoctor($userId)) {
                    $this->app->flash('info', "Sucessfully unmade a doctor.");
                    $this->app->redirect('/admin');
                    return;
                }
            }
        }

        $this->app->flash('info', "An error ocurred. Unable to unmake a doctor.");
        $this->app->redirect('/');
    }
}
