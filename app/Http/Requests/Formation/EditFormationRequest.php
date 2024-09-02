<?php

namespace App\Http\Requests\Formation;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EditFormationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'description' => 'string'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'status_code' => 422,
            'error' => true,
            'message' => 'erreur de validation',
            'errorList' => $validator->errors()
        ]));
    }

    public function messages()
    {
        return [
            'title.required' => 'le titre de la formation est obligatoire',
            'title.string' => 'le titre de la formation doit contenir que des lettres',
            'description.string' => 'la description doit contenir que des lettres'
        ];
    }
}
