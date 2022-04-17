<?php

namespace App\Http\Controllers\Dashboard;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\RemoveRequest;
use App\Http\Services\RemoveService;
use App\Models\Addition;
use App\Models\Category;
use App\Models\Remove;
use Illuminate\Http\JsonResponse;

class RemoveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $category_id
     * @return JsonResponse
     */
    public function index($category_id): JsonResponse
    {
        $category = Category::find($category_id);
        if (!$category) {
            return ResponseHelper::notFound();
        }
        return ResponseHelper::make(RemoveService::index($category));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param RemoveRequest $request
     * @param $category_id
     * @return JsonResponse
     */
    public function store(RemoveRequest $request, $category_id): JsonResponse
    {
        $category = Category::find($category_id);
        if (!$category) {
            return ResponseHelper::notFound();
        }
        return ResponseHelper::make(RemoveService::create($category, $request),
            'Remove is created successfully', true, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param RemoveRequest $request
     * @param $category_id
     * @param $remove_id
     * @return JsonResponse
     */
    public function update(RemoveRequest $request, $category_id, $remove_id): JsonResponse
    {
        $remove = Remove::where('category_id', $category_id)->find($remove_id);
        if (!$remove) {
            return ResponseHelper::notFound();
        }

        if (RemoveService::update($remove, $request)) {
            return ResponseHelper::make(true,
                'Remove is updated successfully', true, 202);
        }

        return ResponseHelper::make(false,
            'Something went wrong', false, 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $category_id
     * @param $remove_id
     * @return JsonResponse
     */
    public function destroy($category_id, $remove_id): JsonResponse
    {
        $remove = Remove::where('category_id', $category_id)->find($remove_id);
        if (!$remove) {
            return ResponseHelper::notFound();
        }
        if (RemoveService::delete($remove)) {
            return ResponseHelper::make(true,
                'Remove is deleted successfully', true, 202);
        }

        return ResponseHelper::make(false,
            'Something went wrong', false, 500);
    }

    /**
     * Change Status the specified resource in storage.
     *
     * @param $category_id
     * @param $remove_id
     * @return JsonResponse
     */
    public function changeStatus($category_id, $remove_id): JsonResponse
    {
        $remove = Remove::where('category_id', $category_id)->find($remove_id);
        if (!$remove) {
            return ResponseHelper::notFound();
        }
        return ResponseHelper::make($remove->changeStatus(),
            'Remove status is changed successfully', true, 202);
    }
}
