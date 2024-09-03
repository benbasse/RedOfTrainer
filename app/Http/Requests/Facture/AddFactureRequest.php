<?php

namespace App\Http\Requests\Facture;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddFactureRequest extends FormRequest
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
            'client_id' => 'required',
            'status' => 'required',
            'due_date' => 'required',
            'payment_method' => 'required',
            'internal_notes' => 'required|string',
            'auto_reminder' => 'required',
            'line_items.*.title' => 'required|string',
            'line_items.*.date' => 'required|date',
            'line_items.*.discount' => 'required|integer',
            'line_items.*.description' => 'required|string',
            'line_items.*.unit_price_ht' => 'required|integer',
            'line_items.*.vat' => 'required|integer',
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
            'client_id.required' => 'Le champ client est requis.',
            'status.required' => 'Le champ statut est requis.',
            'due_date.required' => 'Le champ date d\'échéance est requis.',
            'payment_method.required' => 'Le champ méthode de paiement est requis.',
            'internal_notes.required' => 'Le champ notes internes est requis.',
            'internal_notes.string' => 'Le champ notes internes doit être une chaîne de caractères.',
            'auto_reminder.required' => 'Le champ relance automatique est requis.',
            'line_items.*.title.required' => 'Le champ titre pour chaque élément de la facture est requis.',
            'line_items.*.title.string' => 'Le champ titre pour chaque élément de la facture doit être une chaîne de caractères.',
            'line_items.*.date.required' => 'Le champ date pour chaque élément de la facture est requis.',
            'line_items.*.date.date' => 'Le champ date pour chaque élément de la facture doit être une date valide.',
            'line_items.*.discount.required' => 'Le champ remise pour chaque élément de la facture est requis.',
            'line_items.*.discount.integer' => 'Le champ remise pour chaque élément de la facture doit être un nombre entier.',
            'line_items.*.description.required' => 'Le champ description pour chaque élément de la facture est requis.',
            'line_items.*.description.string' => 'Le champ description pour chaque élément de la facture doit être une chaîne de caractères.',
            'line_items.*.unit_price_ht.required' => 'Le champ prix unitaire HT pour chaque élément de la facture est requis.',
            'line_items.*.unit_price_ht.integer' => 'Le champ prix unitaire HT pour chaque élément de la facture doit être un nombre entier.',
            'line_items.*.vat.required' => 'Le champ TVA pour chaque élément de la facture est requis.',
            'line_items.*.vat.integer' => 'Le champ TVA pour chaque élément de la facture doit être un nombre entier.',
        ];
    }

}
