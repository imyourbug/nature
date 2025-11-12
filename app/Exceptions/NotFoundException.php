<?php

namespace App\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    protected $message = 'Not Found';
    protected $code = 404;

    public function __construct($message = null, $code = 404, Exception $previous = null)
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
            'error' => 'Not Found'
        ], $this->code);
    }
}
