<?php

namespace App\Models;

class ExceptionWrapper
{
    public $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}
