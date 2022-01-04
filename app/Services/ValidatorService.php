<?php

namespace App\Services;

use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ValidatorService
{
    public function validate(array $fields ,array $rules){
        $validator = Validator::make($fields, $rules);

        if ($validator->fails()) {
            throw new CustomException($validator->messages()->first(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

}
