<?php

namespace App\Exceptions;

use Exception;

class BadRequestException extends Exception
{
    protected $message = 'Bad Request';
    protected $code = 400;

    public function __construct($message = null, $code = 400, Exception $previous = null)
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
            'error' => 'Bad Request'
        ], $this->code);
    }
}
