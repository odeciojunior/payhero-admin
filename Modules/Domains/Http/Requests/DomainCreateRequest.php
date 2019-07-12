<?php

namespace Modules\Domains\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DomainCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'GET':

                return [
                    'project_id' => 'required|string|max:255',
                    'domain_ip'  => 'nullable',
                ];

                break;
            default:

                break;
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
