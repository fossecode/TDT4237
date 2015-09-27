<?php

namespace tdt4237\webapp\validation;

use tdt4237\webapp\models\Comment;

class CommentValidation extends Validation
{

    public function __construct($user, $text, $isPost, $csrfToken) {
        parent::__construct($csrfToken);
        return $this->validate($user, $text, $isPost);
    }

    public function validate($user, $text, $isPost)
    {
        if ($user == null) {
            $this->validationErrors[] = "Author needed";

        }
        if (! $isPost) {
            $this->validationErrors[] = "Comment must be added to existing post.";
        }

        if ($text == null) {
            $this->validationErrors[] = "Text needed";
        }

        return $this->validationErrors;
    }
}
