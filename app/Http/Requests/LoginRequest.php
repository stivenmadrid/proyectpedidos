<?php
namespace App\Http\Requests;

use App\Http\Helpers\Helpers; // Corrige la importación
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;



class LoginRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email', // Corrige la coma extra
            'password' => 'required'
        ];
    }

    public function failedValidation(Validator $validator) {
        Helpers::sendError('Validation error', $validator->errors());
    }
}
