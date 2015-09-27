<?php

namespace tdt4237\webapp\validation;

class EditUserFormValidation extends Validation
{
    
    public function __construct($email, $bio, $age, $fullname, $address, $postcode, $csrfToken)
    {
        parent::__construct($csrfToken);
        $this->validate($email, $bio, $age, $fullname, $address, $postcode);
    }

    private function validate($email, $bio, $age, $fullname, $address, $postcode)
    {
        $this->validateEmail($email);
        $this->validateAge($age);
        $this->validateBio($bio);
        $this->validateFullname($fullname);
        $this->validateAddress($address);
        $this->validatePostcode($postcode);
    }
    
    private function validateEmail($email)
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->validationErrors[] = "Invalid email format on email";
        }
    }
    
    private function validateAge($age)
    {
        if (! is_numeric($age) or $age < 0 or $age > 130) {
            $this->validationErrors[] = 'Age must be between 0 and 130.';
        }
    }

    private function validateBio($bio)
    {
        if (empty($bio)) {
            $this->validationErrors[] = 'Bio cannot be empty';
        }
    }

    private function validateFullname($fullname)
    {
        if(empty($fullname)) {
            $this->validationErrors[] = "Please write in your full name";
        }
    }

    private function validateAddress($address)
    {
        if(empty($address)) {
            $this->validationErrors[] = "Please write in your address";
        }
    }

     private function validatePostcode($postcode)
    {
        if(empty($postcode)) {
            $this->validationErrors[] = "Please write in your post code";
        }

        if(! is_numeric($postcode)){
            $this->validationErrors[] = "Postcode must be digits only";
        }

        if (strlen($postcode) != 4) {
            $this->validationErrors[] = "Post code must be exactly four digits";
        }
    }     
}
