<?php

namespace App\Helpers\Validators;

use Rakit\Validation\Validator as RakitValidator;


class Validator{

    public $validated = NULL;

    public function validate($data, $rules){

        $validator = new RakitValidator;

        $validation = $validator->make($data, $rules);

        $validation->validate();

        if ($validation->fails()) {

            $errors = $validation->errors();

            return $errors->all();
        }

        $this->validated = $validation->getValidatedData();

        
        return true;

    }

}