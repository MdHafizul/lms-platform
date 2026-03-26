<?php

namespace App\Support\Exceptions;

use Exception;

class AuthenticationException extends Exception
{
    public function __construct(string $message = 'Authentication failed')
    {
        parent::__construct($message);
    }

    public function render()
    {
        return response()->json([
            'success' => false,
            'message' => $this->message,
            'error_code' => 'AUTH_ERROR',
        ], 401);
    }
}
