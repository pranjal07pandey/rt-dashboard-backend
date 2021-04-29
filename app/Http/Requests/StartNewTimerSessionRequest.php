<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartNewTimerSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'location' =>     'required',
            'longitude'  =>  'required', 
            'latitude' => 'required', 
            'time_started' => 'required'
        ];
        if(request()->has('clients')){
            $rules['user_type'] = 'required';
        }
        return $rules;
    }
}
