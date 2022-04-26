<?php


namespace App\Http\Services;


use App\Http\Requests\Dashboard\SizeRequest;
use App\Models\Product;
use App\Models\Size;

class SizeService
{
    public static function index(Product $product)
    {
        return Size::where('product_id', $product->id)->when(request('search'), function ($query) {
            $query->where(function ($query) {
                $query->where('name_en', 'LIKE', '%' . request('search') . '%')
                    ->orWhere('name_ar', 'LIKE', '%' . request('search') . '%')
                    ->orWhere('id', 'LIKE', '%' . request('search') . '%');
            });
        })->when(request('order-id'), function ($query) {
            $query->orderBy('id', request('order-id'));
        })->when(request('order-name_en'), function ($query) {
            $query->orderBy('name_en', request('order-name_en'));
        })->when(request('order-name_ar'), function ($query) {
            $query->orderBy('name_ar', request('order-name_ar'));
        })->when(request('order-active'), function ($query) {
            $query->orderBy('active', request('order-active'));
        })->get();
    }

    public static function create(Product $product, SizeRequest $request)
    {
        return $product->sizes()->create([
            'name_en'   => $request->get('name_en'),
            'name_ar'   => $request->get('name_ar'),
            'price'     => $request->get('price'),
            'active'    => $request->get('active') ?? 0,
            'discount'  => $request->get('has_discount') ?? 0 ?
                    $request->get('discount') ?? 0 : 0,
        ]);
    }

    public static function update(SizeRequest $request, Size $size)
    {
        return $size->update([
            'name_en'   => $request->get('name_en'),
            'name_ar'   => $request->get('name_ar'),
            'price'     => $request->get('price'),
            'active'    => $request->get('active') ?? 0,
            'discount'  => $request->get('has_discount') ?? 0 ?
                    $request->get('discount') ?? 0 : 0,
        ]);
    }

    public static function delete(Size $size)
    {
        return $size->delete();
    }
}
