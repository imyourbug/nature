<?php

namespace App\Exceptions;

use Exception;

class UnauthorizedException extends Exception
{
    protected $message = 'Unauthorized';
    protected $code = 401;

    public function __construct($message = null, $code = 401, Exception $previous = null)
    {
        $this->message = $message ?? $this->message;
        $this->code = $code;
        parent::__construct($this->message, $this->code, $previous);
    }

    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->message,
            'error' => 'Unauthorized'
        ], $this->code);
    }
}
