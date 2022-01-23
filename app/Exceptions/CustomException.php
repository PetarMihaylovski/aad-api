<?php

namespace App\Exceptions;

use App\Http\Resources\ExceptionWrapperResource;
use App\Models\ExceptionWrapper;
use Exception;

class CustomException extends Exception
{
    /*
     * returns a response based on the provided error message and status code
     */
    public function render(){
        return response(new ExceptionWrapperResource(
            new ExceptionWrapper($this->getMessage())), $this->getCode());
    }
}
