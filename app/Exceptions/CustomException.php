<?php

namespace App\Exceptions;

use App\Http\Resources\ExceptionWrapperResource;
use App\Models\ExceptionWrapper;
use Exception;

class CustomException extends Exception
{
    public function render(){
        return response(new ExceptionWrapperResource(
            new ExceptionWrapper($this->getMessage())), $this->getCode());
    }
}
