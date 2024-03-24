<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

use Illuminate\Foundation\Http\FormRequest;

class customerRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    
    {
        return [
            'customerFirstName' => ['required'],
             'customerLastName' =>['required',],
             'customerEmail' => ['required','email'],
              'customerGender' => ['required'],
              'customerBirthDate' => ['required'],
              'customerContact' =>['required']
          
            
        ];
    }

    protected function failedValidation(Validator $validator){
           throw new HttpResponseException(
             response()->json(['error'=>$validator->errors()], 400)
           );
    }
}
