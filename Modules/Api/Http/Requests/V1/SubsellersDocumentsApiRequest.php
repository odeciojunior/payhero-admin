<?php

namespace Modules\Api\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class SubsellersDocumentsApiRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // required|max:10240|mimes:jpeg,jpg,png,pdf

        return [
            'file_to_uploads' => 'required|array',
            'file_to_uploads.*.type' => 'required|in:USUARIO_DOCUMENTO,USUARIO_RESIDENCIA',
            'file_to_uploads.*.file' => 'required|max:10240|mimes:jpeg,jpg,png,pdf'
        ];
    }

    public function messages()
    {
        return [
            'file_to_uploads.required' => 'Arquivos para upload precisa ser um array.',
            'file_to_uploads.*.type.required' => 'Precisamos saber o tipo do documento.',
            'file_to_uploads.*.type.in' => 'Tipo de documento inválido.',
            'file_to_uploads.*.file.required' => 'Precisamos do arquivo para continuar.',
            'file_to_uploads.*.file.mimes' => 'O arquivo está com formato inválido.',
            'file_to_uploads.*.file.max' => 'Arquivo excede do tamanho de 10MB.',
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
