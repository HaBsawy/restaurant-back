<?php

namespace App\Http\Requests\Dashboard;

use App\Http\Requests\ApiRequest;

class ProductRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->method() === 'POST' ? [
            'category_id'           => 'required|exists:categories,id',
            'name_en'               => 'required|between:3,50',
            'name_ar'               => 'required|between:3,50',
            'description_en'        => 'required|min:3',
            'description_ar'        => 'required|min:3',
            'price'                 => 'required|numeric|between:0,999999.99',
            'has_discount'          => 'nullable|in:0,1',
            'discount'              => 'nullable|numeric|between:0,100',
            'product_main_image'    => 'required|image',
            'product_images'        => 'required|array',
            'product_images.*'      => 'image',
            'active'                => 'nullable|in:0,1',
        ] : [
            'name_en'               => 'required|between:3,50',
            'name_ar'               => 'required|between:3,50',
            'description_en'        => 'required|min:3',
            'description_ar'        => 'required|min:3',
            'price'                 => 'required|numeric|between:0,999999.99',
            'has_discount'          => 'nullable|in:0,1',
            'discount'              => 'nullable|numeric|between:0,100',
            'product_main_image'    => 'nullable|image',
            'product_images'        => 'nullable|array',
            'product_images.*'      => 'image',
            'active'                => 'nullable|in:0,1',
        ];
    }
}
