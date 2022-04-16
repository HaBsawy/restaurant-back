<?php

namespace App\Http\Controllers\Dashboard;

use App\Helper\ResponseHelper;
use App\Helper\UploadHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ProductRequest;
use App\Http\Services\ProductService;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return ResponseHelper::make(ProductService::index());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductRequest $request
     * @return JsonResponse
     */
    public function store(ProductRequest $request): JsonResponse
    {
        $product = ProductService::create($request);
        $directory = 'products/' . $product['id'];

        $this->uploadMainImages(
            $product,
            $directory,
            $request->file('product_main_image'),
            $request->get('name_en')
        );
        $this->uploadImages($product, $directory, $request->file('product_images'));

        return ResponseHelper::make($product,
            'Product is created successfully', true, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Product $product
     * @return JsonResponse
     */
    public function show(Product $product): JsonResponse
    {
        return ResponseHelper::make($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProductRequest $request
     * @param Product $product
     * @return JsonResponse
     */
    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        if (ProductService::update($request, $product)) {
            $directory = 'products/' . $product['id'];

            if ($request->file('product_main_image')) {
                if ($product['main_image']) {
                    UploadHelper::delete($product['main_image']->getAttributes()['path']);
                }
                $product->main_image()->delete();

                $this->uploadMainImages(
                    $product,
                    $directory,
                    $request->file('product_main_image'),
                    $request->get('name_en')
                );
            }
            if ($request->file('product_images')) {
                $this->uploadImages($product, $directory, $request->file('product_images'));
            }

            return ResponseHelper::make(true,
                'Product is updated successfully', true, 202);
        }

        return ResponseHelper::make(false,
            'Something went wrong', false, 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Product $product
     * @return JsonResponse
     */
    public function destroy(Product $product): JsonResponse
    {
        if (ProductService::delete($product)) {
            $product->main_image()->delete();
            $product->images()->delete();
            UploadHelper::deleteDirectory('products/' . $product['id']);

            return ResponseHelper::make(true,
                'Product is deleted successfully', true, 202);
        }

        return ResponseHelper::make(false,
            'Something went wrong', false, 500);
    }

    /**
     * Change Status the specified resource in storage.
     *
     * @param Product $product
     * @return JsonResponse
     */
    public function changeStatus(Product $product): JsonResponse
    {
        return ResponseHelper::make($product->changeStatus(),
            'Product status is changed successfully', true, 202);
    }

    /**
     * Delete the specified image from the storage.
     *
     * @param Product $product
     * @param Image $image
     * @return JsonResponse
     */
    public function destroyImage(Product $product, Image $image): JsonResponse
    {
        UploadHelper::delete($image->getAttributes()['path']);
        $image->delete();
        return ResponseHelper::make(null,
            'Image is deleted successfully', true, 202);
    }

    /**
     * Upload main Image of specified product
     *
     * @param Product $product
     * @param $directory
     * @param $mainImage
     * @param $name
     */
    private function uploadMainImages(Product $product, $directory, $mainImage, $name)
    {
        $mainImagePath = UploadHelper::upload(
            $directory,
            $mainImage,
            Str::slug($name)
        );
        if ($mainImagePath) {
            $product->main_image()->create([
                'path' => $mainImagePath,
                'type' => 'main'
            ]);
        }
    }

    /**
     * Upload images of specified product
     *
     * @param Product $product
     * @param $directory
     * @param $images
     */
    private function uploadImages(Product $product, $directory, $images)
    {
        foreach ($images as $image) {
            $imagePath = UploadHelper::upload(
                $directory,
                $image
            );
            if ($imagePath) {
                $product->images()->create([ 'path' => $imagePath ]);
            }
        }
    }
}
