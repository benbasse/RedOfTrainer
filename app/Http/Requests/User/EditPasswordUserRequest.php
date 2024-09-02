<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EditPasswordUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => 'required|string',
            'new_password' => 'required|regex:/^(?=.*[0-9])(?=.*[a-zA-Z])[a-zA-Z0-9]{8,}$/',
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

    public function messages(){
        return[
            'current_password.required' => 'le mot de passe coutant est requis',
            'new_password.required' => 'le nouveau mot de passe est requis',
            'new_password.regex' => 'Le mot de passe doit contenir au moins 8 caractères, avec des lettres et des chiffres sans caractères spéciaux.',
        ];
    }

    
}
