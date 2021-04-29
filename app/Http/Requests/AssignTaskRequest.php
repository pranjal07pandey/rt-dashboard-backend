<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignTaskRequest extends FormRequest
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
            'name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ];
    }

    public function messages(){
        return[
            'end_date.after' => 'End date should be greater then start date .',
        ];
    }

    public function withValidator($validator)
    {
        if ($validator->fails()) {
            \Session::flash('route_name', request()->route()->getName());
        } else {

        }
    }
}
