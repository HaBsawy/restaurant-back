<?php


namespace App\Http\Services;


use App\Http\Requests\Dashboard\AdditionRequest;
use App\Http\Requests\Dashboard\RemoveRequest;
use App\Http\Requests\Dashboard\SizeRequest;
use App\Models\Addition;
use App\Models\Category;
use App\Models\Product;
use App\Models\Remove;
use App\Models\Size;

class RemoveService
{
    public static function index(Category $category)
    {
        return 123;
//        $perPage = request('per-page', 10);
//        return Product::when(request('search'), function ($query) {
//            $query->where(function ($query) {
//                $query->where('name_en', 'LIKE', '%' . request('search') . '%')
//                    ->orWhere('name_ar', 'LIKE', '%' . request('search') . '%')
//                    ->orWhere('description_en', 'LIKE', '%' . request('search') . '%')
//                    ->orWhere('description_ar', 'LIKE', '%' . request('search') . '%')
//                    ->orWhere('id', 'LIKE', '%' . request('search') . '%');
//            });
//        })->when(request('order-id'), function ($query) {
//            $query->orderBy('id', request('order-id'));
//        })->when(request('order-name_en'), function ($query) {
//            $query->orderBy('name_en', request('order-name_en'));
//        })->when(request('order-name_ar'), function ($query) {
//            $query->orderBy('name_ar', request('order-name_ar'));
//        })->when(request('order-description_en'), function ($query) {
//            $query->orderBy('description_en', request('order-description_en'));
//        })->when(request('order-description_ar'), function ($query) {
//            $query->orderBy('description_ar', request('order-description_ar'));
//        })->when(request('order-active'), function ($query) {
//            $query->orderBy('active', request('order-active'));
//        })->paginate($perPage);
    }

    public static function create(Category $category, RemoveRequest $request)
    {
        return $category->removes()->create([
            'name_en'   => $request->get('name_en'),
            'name_ar'   => $request->get('name_ar'),
            'active'    => $request->get('active') ?? 0,
        ]);
    }

    public static function update(Remove $remove, RemoveRequest $request)
    {
        return $remove->update([
            'name_en'   => $request->get('name_en'),
            'name_ar'   => $request->get('name_ar'),
            'active'    => $request->get('active') ?? 0,
        ]);
    }

    public static function delete(Remove $remove)
    {
        return $remove->delete();
    }
}
