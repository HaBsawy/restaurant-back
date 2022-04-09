<?php

namespace App\Http\Controllers\Dashboard;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\CategoryRequest;
use App\Http\Services\CategoryService;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return ResponseHelper::make(CategoryService::index());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CategoryRequest $request
     * @return JsonResponse
     */
    public function store(CategoryRequest $request)
    {
        return ResponseHelper::make(CategoryService::create($request),
            'Category is created successfully', true, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function show(Category $category)
    {
        return ResponseHelper::make($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CategoryRequest $request
     * @param Category $category
     * @return JsonResponse
     */
    public function update(CategoryRequest $request, Category $category)
    {
        return ResponseHelper::make(CategoryService::update($request, $category),
            'Category is updated successfully', true, 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function destroy(Category $category)
    {
        return ResponseHelper::make(CategoryService::delete($category),
            'Category is deleted successfully', true, 202);
    }

    /**
     * Change Status the specified resource in storage.
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function changeStatus(Category $category)
    {
        return ResponseHelper::make($category->changeStatus(),
            'Category status is changed successfully', true, 202);
    }

    /**
     * Display a listing key => value of the resource.
     *
     * @return JsonResponse
     */
    public function selectCategory()
    {
        return ResponseHelper::make(Category::all('id', 'name_en')->toArray());
    }
}
