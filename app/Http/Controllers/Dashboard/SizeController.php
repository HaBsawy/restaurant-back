<?php

namespace App\Http\Controllers\Dashboard;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SizeRequest;
use App\Http\Services\SizeService;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\JsonResponse;

class SizeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $product_id
     * @return JsonResponse
     */
    public function index($product_id): JsonResponse
    {
        $product = Product::find($product_id);
        if (!$product) {
            return ResponseHelper::notFound();
        }
        return ResponseHelper::make(SizeService::index($product));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SizeRequest $request
     * @param $product_id
     * @return JsonResponse
     */
    public function store(SizeRequest $request, $product_id): JsonResponse
    {
        $product = Product::find($product_id);
        if (!$product) {
            return ResponseHelper::notFound();
        }
        return ResponseHelper::make(SizeService::create($product, $request),
            'Size is created successfully', true, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SizeRequest $request
     * @param $product_id
     * @param $size_id
     * @return JsonResponse
     */
    public function update(SizeRequest $request, $product_id, $size_id): JsonResponse
    {
        $size = Size::where('product_id', $product_id)->find($size_id);
        if (!$size) {
            return ResponseHelper::notFound();
        }

        if (SizeService::update($request, $size)) {
            return ResponseHelper::make(true,
                'Size is updated successfully', true, 202);
        }

        return ResponseHelper::make(false,
            'Something went wrong', false, 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $product_id
     * @param $size_id
     * @return JsonResponse
     */
    public function destroy($product_id, $size_id): JsonResponse
    {
        $size = Size::where('product_id', $product_id)->find($size_id);
        if (!$size) {
            return ResponseHelper::notFound();
        }
        if (SizeService::delete($size)) {
            return ResponseHelper::make(true,
                'Size is deleted successfully', true, 202);
        }

        return ResponseHelper::make(false,
            'Something went wrong', false, 500);
    }

    /**
     * Change Status the specified resource in storage.
     *
     * @param $product_id
     * @param $size_id
     * @return JsonResponse
     */
    public function changeStatus($product_id, $size_id): JsonResponse
    {
        $size = Size::where('product_id', $product_id)->find($size_id);
        if (!$size) {
            return ResponseHelper::notFound();
        }
        return ResponseHelper::make($size->changeStatus(),
            'Size status is changed successfully', true, 202);
    }
}
