<?php


namespace App\Http\Services;


use App\Http\Requests\Dashboard\CategoryRequest;
use App\Models\Category;

class CategoryService
{
    public static function index()
    {
        $perPage = request('per-page', 10);
        return Category::when(request('search'), function ($query) {
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
        })->paginate($perPage);
    }

    public static function create(CategoryRequest $request)
    {
        return Category::create([
            'name_en'   => $request->get('name_en'),
            'name_ar'   => $request->get('name_ar'),
            'active'    => $request->get('active') ?? 0,
        ]);
    }

    public static function update(CategoryRequest $request, Category $category)
    {
        return $category->update([
            'name_en'   => $request->get('name_en'),
            'name_ar'   => $request->get('name_ar'),
            'active'    => $request->get('active') ?? 0,
        ]);
    }

    public static function delete(Category $category)
    {
        return $category->delete();
    }
}
