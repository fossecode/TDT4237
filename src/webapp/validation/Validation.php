<?php

namespace tdt4237\webapp\validation;

class Validation {

	protected $validationErrors = [];

	public function __construct($csrf) {
        return $this->validateCSRF($csrf);
    }

    public function validateCSRF($csrf) {
        if ($csrf !== $_SESSION['CSRF_token'])
        	$this->validationErrors[] = 'Invalid CSRF token.';
    }
}

?>