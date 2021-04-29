<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeaveStoreRequest extends FormRequest
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
            'user_id' => 'required_if:machine_id,==,'.null,
            'description' => 'required',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ];
    }

    public function messages(){
        return[
            'user_id.required_if' => 'Employees or Machines user is required.',
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
