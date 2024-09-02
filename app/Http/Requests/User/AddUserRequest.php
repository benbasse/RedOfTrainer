<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddUserRequest extends FormRequest
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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'address' => 'required',
            'phone_number' => 'required|regex:/^[0-9]+$/|unique:users,phone_number',
            'email' => 'required|unique:users,email|email',
            'password' => 'required|regex:/^(?=.*[0-9])(?=.*[a-zA-Z])[a-zA-Z0-9]{8,}$/',

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

    public function messages(): array
    {
        return [
            'first_name.required' => 'Le prénom est obligatoire.',
            'first_name.string' => 'Le prénom doit être une chaîne de caractères.',

            'last_name.required' => 'Le nom de famille est obligatoire.',
            'last_name.string' => 'Le nom de famille doit être une chaîne de caractères.',

            'address.required' => "l'addresse est obligatoire",
            
            'phone_number.required' => 'Le numéro de téléphone est obligatoire.',
            'phone_number.regex' => 'Le numéro de téléphone ne peut contenir que des chiffres.',
            'phone_number.unique' => 'Ce numéro de téléphone est déjà utilisé.',

            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.unique' => 'Cette adresse e-mail est déjà utilisée.',
            'email.email' => 'L\'adresse e-mail doit être une adresse e-mail valide.',

            'password.required' => 'Le mot de passe est obligatoire.',
            'password.regex' => 'Le mot de passe doit contenir au moins 8 caractères, avec des lettres et des chiffres sans caractères spéciaux.',
        ];
    }
}
