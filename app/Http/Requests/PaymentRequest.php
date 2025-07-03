<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
{
    public function rules(): array
    {

        if(($this->card_id && $this->card_id == 'new')) {
            $expiry_month = date('m');
            $expiry_year = date('Y');
    
            if ( isset($this->expiry_year) ) {
                $expiry_year = $this->expiry_year;
            }
            if ( isset($this->expiry_month) ) {
                $expiry_month = $this->expiry_month;
            }
            $this->merge(['expiry' => $expiry_month . '/31/' . '20'.$expiry_year]);
            $rules =  [
                'card_no'          => ['required',
                    Rule::unique('card_details')
                        ->where(function ($query) {
                            return $query->where('user_id', loginUser()->id)->where('card_no', '<>', null);
                        }), 'digits:16'],
                'card_holder_name' => 'required|regex:/^[a-zA-Z0-9 ]+$/',
                'expiry_month'     => ['required', 'numeric', 'min:01', 'max:12'],
                'expiry_year'      => ['required', 'numeric', 'digits:2', 'max:2099'],
                'cvv'              => ['required', 'digits:3',  'not_in:000'],
                'plan_id' => ['required', Rule::exists('plans', 'id')],
                'amount'    =>  ['required','numeric','gt:0'],
            ];
    
            if(!$this->expiry_year && !$this->expiry_month) {
                unset($rules['expiry_year']);
            }
    
            if($this->expiry_year) {
                $rules['expiry'] = 'after:'.date('m/d/Y');
            }
        } else {
            $rules['card_id'] = ['required', 'exists:card_details,id'];
        }

        return $rules;
    }

    public function messages()
    {
        $messages =  [
            "card_id.required"    => "Please select exisitng card or new card",
            "cvv.required"          => "CVC field is required",
            "cvv.not_in"          => "The selected CVC is invalid.",
            "cvv.digits"            => "CVC field must be 3 digits",
            "expiry"                =>  "The expiry year must be valid"
        ];

        if(!$this->expiry_year && !$this->expiry_month) {
            $messages['expiry_month'] = 'Expiration must be a valid month/year';
        }

        return $messages;
    }
}
