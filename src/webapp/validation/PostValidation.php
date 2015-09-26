<?php

namespace tdt4237\webapp\validation;

use tdt4237\webapp\models\Post;

class PostValidation {

    private $validationErrors = [];

    public function __construct($user, $title, $content) {
        return $this->validate($user, $title, $content);
    }

    public function isGoodToGo()
    {
        return \count($this->validationErrors) ===0;
    }

    public function getValidationErrors()
    {
    return $this->validationErrors;
    }

    public function validate($user, $title, $content)
    {
        if ($user == null) {
            $this->validationErrors[] = "Author needed";

        }
        if ($title == null) {
            $this->validationErrors[] = "Title needed";
        }

        if ($content == null) {
            $this->validationErrors[] = "Text needed";
        }

        return $this->validationErrors;
    }


}
