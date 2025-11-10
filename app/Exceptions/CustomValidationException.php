<?php
namespace App\Exceptions;

use Exception;

class CustomValidationException extends Exception
{
    protected $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct('Validation errors occurred.');
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
