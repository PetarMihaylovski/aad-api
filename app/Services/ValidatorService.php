<?php

namespace App\Services;

use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ValidatorService
{
    /**
     * validates a set of fields. according to a ruleset provided in the arguments
     *
     * @param array $fields the fields to be verified
     * @param array $rules the rules to verify against
     * @return void if successful (continue with the flow)
     * @throws CustomException exception in case of bad request
     */
    public function validate(array $fields ,array $rules){
        $validator = Validator::make($fields, $rules);

        if ($validator->fails()) {
            throw new CustomException($validator->messages()->first(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

}
