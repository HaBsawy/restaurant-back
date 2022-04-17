<?php

namespace App\Http\Controllers\Dashboard;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\AdditionRequest;
use App\Http\Services\AdditionService;
use App\Models\Addition;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class AdditionController extends Controller
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
        return ResponseHelper::make(AdditionService::index($category));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AdditionRequest $request
     * @param $category_id
     * @return JsonResponse
     */
    public function store(AdditionRequest $request, $category_id): JsonResponse
    {
        $category = Category::find($category_id);
        if (!$category) {
            return ResponseHelper::notFound();
        }
        return ResponseHelper::make(AdditionService::create($category, $request),
            'Addition is created successfully', true, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AdditionRequest $request
     * @param $category_id
     * @param $addition_id
     * @return JsonResponse
     */
    public function update(AdditionRequest $request, $category_id, $addition_id): JsonResponse
    {
        $addition = Addition::where('category_id', $category_id)->find($addition_id);
        if (!$addition) {
            return ResponseHelper::notFound();
        }

        if (AdditionService::update($addition, $request)) {
            return ResponseHelper::make(true,
                'Addition is updated successfully', true, 202);
        }

        return ResponseHelper::make(false,
            'Something went wrong', false, 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $category_id
     * @param $addition_id
     * @return JsonResponse
     */
    public function destroy($category_id, $addition_id): JsonResponse
    {
        $addition = Addition::where('category_id', $category_id)->find($addition_id);
        if (!$addition) {
            return ResponseHelper::notFound();
        }
        if (AdditionService::delete($addition)) {
            return ResponseHelper::make(true,
                'Addition is deleted successfully', true, 202);
        }

        return ResponseHelper::make(false,
            'Something went wrong', false, 500);
    }

    /**
     * Change Status the specified resource in storage.
     *
     * @param $category_id
     * @param $addition_id
     * @return JsonResponse
     */
    public function changeStatus($category_id, $addition_id): JsonResponse
    {
        $addition = Addition::where('category_id', $category_id)->find($addition_id);
        if (!$addition) {
            return ResponseHelper::notFound();
        }
        return ResponseHelper::make($addition->changeStatus(),
            'Addition status is changed successfully', true, 202);
    }
}
