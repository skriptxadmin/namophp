<?php
use App\Helpers\Validators\Validator;

class ExampleValidation extends Controller{



    public function get(){

        $validator = new Validator();

        $rules = array(
            'username' => 'required|regex:/^[a-zA-Z0-9\-]*$/',
            'email' => 'required|email',
            'name' => 'required|min:3|max:30|regex:/^[a-zA-Z\s\.]*$/',
            'mobile' => 'required|regex:/^[7-9][0-9]{9}$/',
            'password'=>'nullable|min:8|max:15'
        );
    
        $errors = $validator->validate($_POST, $rules);
    
        if ($errors !== true) {
    
            $this->json(['success' => false, 'errors' => $errors], 422);
    
            return;
        }
    
        $validated = $validator->validated;

        $this->json($validated, 422);


    return;
    }
}
