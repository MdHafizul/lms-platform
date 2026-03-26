<?php

namespace App\Support\Exceptions;

use Exception;

class ValidationException extends Exception
{
    protected $errors;

    public function __construct(array $errors = [])
    {
        $this->errors = $errors;
        parent::__construct('Validation failed');
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function render()
    {
        return response()->json([
            'success' => false,
            'message' => $this->message,
            'errors' => $this->errors,
            'error_code' => 'VALIDATION_ERROR',
        ], 422);
    }
}
