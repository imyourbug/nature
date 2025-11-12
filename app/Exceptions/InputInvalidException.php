<?php

namespace App\Exceptions;

use Exception;

class InputInvalidException extends Exception
{
    protected $message = 'Validation Error';
    protected $errors = [];
    protected $code = 400;

    public function __construct($errors = [], $message = null, $code = 400, Exception $previous = null)
    {
        $this->message = $message ?? $this->message;
        $this->errors = $errors;
        $this->code = $code;
        parent::__construct($this->message, $this->code, $previous);
    }

    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->message,
            'errors' => $this->errors,
        ], $this->code);
    }
}
