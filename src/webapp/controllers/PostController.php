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
        if($this->auth->guest()){
            $this->app->redirect("/");
            return;
        }

        // Doctor should not see non-paid posts
        if($this->auth->isDoctor() and !$this->postRepository->find($postId)->isPaidQuestion()){
            $this->app->redirect("/posts");
            return;
        }

        $post = $this->postRepository->find($postId);
        $comments = $this->commentRepository->findByPostId($postId);
        $request = $this->app->request;
        $user = $this->userRepository->findByUserId($_SESSION['userId']);

        $variables['success'] = $request->get('success') == 'true';

        $this->render('showpost.twig', [
            'post' => $post,
            'comments' => $comments,
            'flash' => $variables,
            'user' => $user
        ]);

    }

    public function addComment($postId)
    {

        // Guests must login
        if($this->auth->guest()) {
            $this->app->redirect('/login');
            $this->app->flash('info', 'You must log in to do that');
        }
        // Doctors should not be able to comment on not-paid questions
        if($this->auth->isDoctor() and !$this->postRepository->find($postId)->isPaidQuestion()){
            $this->app->redirect("/posts");
            return;
        }

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
        else{

            $this->show($postId, ['errors' => $validation->getValidationErrors()]);
        }

    }

    public function showNewPostForm()
    {
        if($this->auth->isDoctor()){
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
            $doctorQuestion = $request->post('doctor');
            $csrfToken = $request->post('csrf');
            $user = $this->userRepository->findByUserId($_SESSION['userId']);
            $date = date("Y-m-d H:i:s");

            $validation = new PostValidation($title, $user, $content, $csrfToken, $doctorQuestion);
            if ($validation->isGoodToGo()) {
                $post = new Post();
                $post->setUser($user);
                $post->setTitle($title);
                $post->setContent($content);
                $post->setDate($date);
                $post->setPaidQuestion($doctorQuestion === 'true');
                $savedPost = $this->postRepository->save($post);
                $this->app->redirect('/posts/' . $savedPost . '?success=true');
            }
        }
            $this->app->flashNow('errors', $validation->getValidationErrors());
            $this->render('createpost.twig');
            // RENDER HERE

    }
}

