<?php

namespace App\Http\Requests\Dashboard;

use App\Http\Requests\ApiRequest;

class AdditionRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name_en'       => 'required|between:3,50',
            'name_ar'       => 'required|between:3,50',
            'price'         => 'required|numeric|between:0,999999.99',
            'has_discount'  => 'nullable|in:0,1',
            'discount'      => 'nullable|numeric|between:0,100',
            'active'        => 'nullable|in:0,1',
        ];
    }
}
