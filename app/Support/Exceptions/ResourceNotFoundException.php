<?php

namespace App\Support\Exceptions;

use Exception;

class ResourceNotFoundException extends Exception
{
    public function __construct(string $resource = 'Resource', string $message = '')
    {
        $msg = $message ?: "{$resource} not found";
        parent::__construct($msg);
    }

    public function render()
    {
        return response()->json([
            'success' => false,
            'message' => $this->message,
            'error_code' => 'RESOURCE_NOT_FOUND',
        ], 404);
    }
}
