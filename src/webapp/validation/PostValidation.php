<?php

namespace tdt4237\webapp\validation;

use tdt4237\webapp\models\Post;

class PostValidation extends Validation
{
    protected $app;
    protected $userRepository;

    public function __construct($title, $user, $content, $csrfToken, $doctorQuestion) {
        parent::__construct($csrfToken);
        return $this->validate($user, $title, $content, $doctorQuestion);
    }

    public function validate($user, $title, $content, $doctorQuestion)
    {   
        if ($user->getUsername() == null) {
            $this->validationErrors[] = "Author needed";

        }
        if ($title == null) {
            $this->validationErrors[] = "Title needed";
        }

        if ($content == null) {
            $this->validationErrors[] = "Text needed";
        }
        if ($doctorQuestion == 'true' && $user->hasAccountNumber() == false){
            $this->validationErrors[] = "User account number needed";
        }


        return $this->validationErrors;
    }
}