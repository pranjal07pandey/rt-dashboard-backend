<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TimerCommentRequest extends FormRequest
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
        return [
            'timer_id' => 'required', 
            'time' => 'required',
            'message'=>'required',
            'location'=>'required',
            'latitude'=>'required',
            'longitude'=>'required'
        ];
    }
}
