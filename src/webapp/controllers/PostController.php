<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\models\Post;
use tdt4237\webapp\controllers\UserController;
use tdt4237\webapp\models\Comment;
use tdt4237\webapp\validation\PostValidation;
use tdt4237\webapp\validation\CommentValidation;

class PostController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }


    public function index()
    {
        if($this->auth->guest()){
            $this->app->redirect('/');
        }
        else{
            $posts = $this->postRepository->all();
            $posts->sortByDate();
            $user = $this->userRepository->findByUserId($_SESSION['userId']);
            $this->render('posts.twig', [
                'posts' => $posts,
                'user' => $user
            ]); 
        }

    }

    public function show($postId, $variables = [])
    {
        $post = $this->postRepository->find($postId);
        $comments = $this->commentRepository->findByPostId($postId);
        $request = $this->app->request;
        $message = $request->get('msg');
        $user = $this->userRepository->findByUserId($_SESSION['userId']);

        if($message) {
            $variables['msg'] = $message;
        }

        $this->render('showpost.twig', [
            'post' => $post,
            'comments' => $comments,
            'flash' => $variables,
            'user' => $user
        ]);

    }

    public function addComment($postId)
    {

        if(!$this->auth->guest()) {

            $user = $this->userRepository->findByUserId($_SESSION['userId']);
            $text = $this->app->request->post("text");
            $csrfToken = $this->app->request->post("csrf");
            $isPost = $this->postRepository->isPost($postId);

            $validation = new CommentValidation($user, $text, $isPost, $csrfToken);
            
            if ($validation->isGoodToGo()) {

                $comment = new Comment();
                $comment->setUser($user);
                $comment->setText($text);
                $comment->setDate(date ("Y-m-d H:i:s"));
                $comment->setPost($postId);
                $this->commentRepository->save($comment);

                if($user->isDoctor()){
                    $this->paymentRepository->insertPayment($user->getUserId(), $postId);
                }
                
                $this->app->redirect('/posts/' . $postId);
            }

            $this->show($postId, ['errors' => $validation->getValidationErrors()]);
        }
        else {
            $this->app->redirect('/login');
            $this->app->flash('info', 'You must log in to do that');
        }

    }

    public function showNewPostForm()
    {
        if($this->auth->idDoctor()){
            $this->app->redirect("/");
            return;
        }

        if ($this->auth->check()) {
            $user = $this->userRepository->findByUserId($_SESSION['userId']);
            $this->render('createpost.twig', [
                'user' => $user
            ]);
        } else {

            $this->app->flash('errors', ["You need to be logged in to create a post"]);
            $this->app->redirect("/");
        }

    }

    public function create()
    {
        if ($this->auth->guest()) {
            $this->app->flash("info", "You must be logged on to create a post");
            $this->app->redirect("/login");
        } else {
            $request = $this->app->request;
            $title = $request->post('title');
            $content = $request->post('content');
            $csrfToken = $request->post('csrf');
            $user = $this->userRepository->findByUserId($_SESSION['userId']);
            $date = date("Y-m-d H:i:s");

            $validation = new PostValidation($title, $user->getUsername(), $content, $csrfToken);
            if ($validation->isGoodToGo()) {
                $post = new Post();
                $post->setUser($user);
                $post->setTitle($title);
                $post->setContent($content);
                $post->setDate($date);
                $savedPost = $this->postRepository->save($post);
                $this->app->redirect('/posts/' . $savedPost . '?msg=Post succesfully posted');
            }
        }

            $this->app->flashNow('errors', $validation->getValidationErrors());
            $this->app->render('createpost.twig');
            // RENDER HERE

    }
}

