<?php

namespace Modules\Domains\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DomainDestroyRecordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':

                return [
                    'id_record' => 'required|string|max:255',
                    'id_domain' => 'required|string|max:255',
                ];

                break;
            default:

                break;
        }
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
