<?php

namespace tdt4237\webapp\validation;

use tdt4237\webapp\models\User;

class RegistrationFormValidation extends Validation
{
    const MIN_USER_LENGTH = 3;
    
    public function __construct($username, $password, $fullname, $address, $postcode, $csrfToken)
    {
        parent::__construct($csrfToken);
        return $this->validate($username, $password, $fullname, $address, $postcode);
    }

    private function validate($username, $password, $fullname, $address, $postcode)
    {
        if (empty($password)) {
            $this->validationErrors[] = 'Password cannot be empty';
        }

        if(empty($fullname)) {
            $this->validationErrors[] = "Please write in your full name";
        }

        if(empty($address)) {
            $this->validationErrors[] = "Please write in your address";
        }

        if(empty($postcode)) {
            $this->validationErrors[] = "Please write in your post code";
        }

        if (strlen($postcode) != 4) {
            $this->validationErrors[] = "Post code must be exactly four digits";
        }

        if(! is_numeric($postcode)){
            $this->validationErrors[] = "Postcode must be digits only";
        }

        if (preg_match('/^[A-Za-z0-9_]+$/', $username) === 0) {
            $this->validationErrors[] = 'Username can only contain letters and numbers';
        }
    }
}
