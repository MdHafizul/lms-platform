<?php

namespace App\Support\Exceptions;

use Exception;

class AuthorizationException extends Exception
{
    public function __construct(string $message = 'This action is unauthorized')
    {
        parent::__construct($message);
    }

    public function render()
    {
        return response()->json([
            'success' => false,
            'message' => $this->message,
            'error_code' => 'AUTHORIZATION_ERROR',
        ], 403);
    }
}
