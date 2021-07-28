<?php


namespace Modules\Melhorenvio\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class MelhorenvioStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'client_id' => 'required',
            'client_secret' => 'required',
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return [
            'name.required' => 'O campo nome é obrigatório',
            'client_id.required' => 'O campo Client ID é obrigatório',
            'client_secret.required' => 'O campo Client Secret é obrigatório',
        ];
    }
}
