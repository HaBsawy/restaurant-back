<?php

namespace App\Http\Requests\Dashboard;

use App\Http\Requests\ApiRequest;

class RemoveRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name_en'   => 'required|between:3,50',
            'name_ar'   => 'required|between:3,50',
            'active'    => 'nullable|in:0,1',
        ];
    }
}
