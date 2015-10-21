<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\models\NewsItem;

class IndexController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
//        $request = $this->app->request;
//        $msg = $request->get('msg');

//        $variables = [];

  //      if ($msg) {
    //        $variables['flash']['info'] = $msg;
    //    }
        if(!$this->auth->guest()) {
            $user = $this->userRepository->findByUserId($_SESSION['userId']);

            $this->render('index.twig', [
                'user' => $user
            ]);
        }
        else {
            $this->render('index.twig');
        }
    }
}
