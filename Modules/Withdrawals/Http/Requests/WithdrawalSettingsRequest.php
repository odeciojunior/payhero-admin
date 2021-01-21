<?php

namespace Modules\Withdrawals\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Entities\WithdrawalSettings;

class WithdrawalSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'company_id' => 'required|string',
            'rule'       => 'required|string',
            'frequency'  => 'required_if:rule,==,' . WithdrawalSettings::RULE_PERIOD . '|string',
            'weekday'    => 'required_if:frequency,==,' . WithdrawalSettings::FREQUENCY_WEEKLY . '|string',
            'day'        => 'required_if:frequency,==,' . WithdrawalSettings::FREQUENCY_MONTHLY . '|string',
            'amount'     => 'required_if:rule,==,' . WithdrawalSettings::RULE_AMOUNT . '|string',
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return [
            'company_id.required' => 'É obrigatório selecionar uma empresa'
        ];
    }
}
