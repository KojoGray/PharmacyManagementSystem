<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class SalesRequest extends FormRequest
{
    /**\
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
            'pharmacist_email' => ['required'],
            'total_quantity' => ['required'],
            'total_price' => ['required'],
            'medications' => ['required'],
            'customer_email' => ['required'],
            'status' => ['required'],
            'amount_paid' => ['required']
            //  'unit_price' =>['required']


        ];
    }



    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(['error' => $validator->errors()], 400)
        );
    }
}
