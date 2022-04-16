<?php


namespace App\Http\Services;


use App\Http\Requests\Dashboard\ProductRequest;
use App\Models\Product;

class ProductService
{
    public static function index()
    {
        $perPage = request('per-page', 10);
        return Product::when(request('search'), function ($query) {
            $query->where(function ($query) {
                $query->where('name_en', 'LIKE', '%' . request('search') . '%')
                    ->orWhere('name_ar', 'LIKE', '%' . request('search') . '%')
                    ->orWhere('description_en', 'LIKE', '%' . request('search') . '%')
                    ->orWhere('description_ar', 'LIKE', '%' . request('search') . '%')
                    ->orWhere('id', 'LIKE', '%' . request('search') . '%');
            });
        })->when(request('order-id'), function ($query) {
            $query->orderBy('id', request('order-id'));
        })->when(request('order-name_en'), function ($query) {
            $query->orderBy('name_en', request('order-name_en'));
        })->when(request('order-name_ar'), function ($query) {
            $query->orderBy('name_ar', request('order-name_ar'));
        })->when(request('order-description_en'), function ($query) {
            $query->orderBy('description_en', request('order-description_en'));
        })->when(request('order-description_ar'), function ($query) {
            $query->orderBy('description_ar', request('order-description_ar'));
        })->when(request('order-active'), function ($query) {
            $query->orderBy('active', request('order-active'));
        })->paginate($perPage);
    }

    public static function create(ProductRequest $request)
    {
        return Product::create([
            'category_id'       => $request->get('category_id'),
            'name_en'           => $request->get('name_en'),
            'name_ar'           => $request->get('name_ar'),
            'description_en'    => $request->get('description_en'),
            'description_ar'    => $request->get('description_ar'),
            'active'            => $request->get('active') ?? 0
        ]);
    }

    public static function update(ProductRequest $request, Product $product)
    {
        return $product->update([
            'product_id'        => $request->get('product_id'),
            'name_en'           => $request->get('name_en'),
            'name_ar'           => $request->get('name_ar'),
            'description_en'    => $request->get('description_en'),
            'description_ar'    => $request->get('description_ar'),
            'active'            => $request->get('active') ?? 0
        ]);
    }

    public static function delete(Product $product)
    {
        return $product->delete();
    }
}
