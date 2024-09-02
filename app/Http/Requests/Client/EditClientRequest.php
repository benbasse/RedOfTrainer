<?php

namespace App\Http\Requests\Client;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EditClientRequest extends FormRequest
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
            'name' => 'required|string',
            'phone_number' => 'required|regex:/^[0-9]+$/|unique:clients,phone_number',
            'email' => 'required|unique:clients,email|email',
            'address' => 'required|string',
            'type' => 'required'
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
            'name.required' => 'le nom du client est obligatoire',
            'name.string' => 'le nom doit contenir que des lettres',

            'phone_number.required' => 'Le numéro de téléphone est obligatoire.',
            'phone_number.regex' => 'Le numéro de téléphone ne peut contenir que des chiffres.',
            'phone_number.unique' => 'Ce numéro de téléphone est déjà utilisé.',

            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.unique' => 'Cette adresse e-mail est déjà utilisée.',
            'email.email' => 'L\'adresse e-mail doit être une adresse e-mail valide.',

            'address.required' => "l'addresse est obligatoire",

            'type.required' => "le type de client est obligatoire"
        ];
    }
}
