<?php

namespace tdt4237\webapp\validation;

use tdt4237\webapp\models\User;

class RegistrationFormValidation extends Validation
{

    public function __construct($username, $password, $fullname, $address, $postcode, $csrfToken, $userRepository)
    {
        parent::__construct($csrfToken);
        return $this->validate($username, $password, $fullname, $address, $postcode, $userRepository);
    }

    private function validate($username, $password, $fullname, $address, $postcode, $userRepository)
    {
        if (empty($password)) {
            $this->validationErrors[] = 'Password cannot be empty';
        }

        if (strlen($password) < 8 || strlen($password) >= 50) {
            $this->validationErrors[] = 'Password must be between 8 and 50 characters long.';
        }

        if (empty($username)) {
            $this->validationErrors[] = 'Username cannot be empty';
        }

        if ($userRepository->findByUsername($username)){
            $this->validationErrors[] = 'Username already in use';
        }

        if (strlen($username) < 3 || strlen($username) >= 50) {
            $this->validationErrors[] = 'Username must be between 3 and 50 characters long.';
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
