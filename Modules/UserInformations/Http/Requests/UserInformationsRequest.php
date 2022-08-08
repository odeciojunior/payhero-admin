<?php

namespace Modules\UserInformations\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserInformationsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "document" => "required",
        ];
    }

    public function messages()
    {
        return [
            "email.required" => "O email é obrigatório",
            "name.required" => "O nome é obrigatório",
            "document.required" => "O documento é obrigatório",
            "phone.required" => "O telefone é obrigatório",
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
