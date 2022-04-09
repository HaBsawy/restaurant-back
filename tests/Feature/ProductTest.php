<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->product = Product::factory()->create();
        $this->url = '/api/dashboard/products';
    }

    private function data($data = []) : array
    {
        $main_image = UploadedFile::fake()->create('image.png');
        $image1 = UploadedFile::fake()->create('image1.png');
        $image2 = UploadedFile::fake()->create('image2.png');
        $default = [
            'category_id'           => $this->category->id,
            'name_en'               => 'Cocktail Cheese Pie',
            'name_ar'               => 'فطيرة مشكل جبن',
            'description_en'        => 'Cocktail Cheese Pie',
            'description_ar'        => 'فطيرة مشكل جبن',
            'product_main_image'    => $main_image,
            'product_images'        => [$image1, $image2],
            'price'                 => 100,
            'has_discount'          => 1,
            'discount'              => 5,
            'active'                => 1,
        ];

        return array_merge($default, $data);
    }

    private function validationError($msg): array
    {
        return [
            'msg'           => $msg,
            'isSuccess'     => false,
            'statusCode'    => 422,
            'payload'       => null
        ];
    }

    private function unauthenticated(): array
    {
        return [
            'message' => 'Unauthenticated.'
        ];
    }

    public function test_index_without_authentication()
    {
        $response = $this->getJson($this->url);
        $response->assertStatus(401)->assertJson($this->unauthenticated());
    }

    public function test_index()
    {
        $response = $this->actingAs($this->user)->getJson($this->url);
        $response->assertStatus(200)->assertJson([
            'msg'           => "",
            'isSuccess'     => true,
            'statusCode'    => 200,
//            'payload'       => null
        ]);
    }

    public function test_store_without_authentication()
    {
        $response = $this->postJson($this->url);
        $response->assertStatus(401)->assertJson($this->unauthenticated());
    }

    public function test_store_without_parameters()
    {
        $response = $this->actingAs($this->user)->postJson($this->url);
        $response->assertStatus(422)
            ->assertJson($this->validationError('The category id field is required.'));
    }

    public function test_store_without_category_id_parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['category_id' => '']));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The category id field is required.'));
    }

    public function test_store_with_non_existing_category_id_parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['category_id' => 10001]));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The selected category id is invalid.'));
    }

    public function test_store_without_name_en_parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['name_en' => '']));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The name en field is required.'));
    }

    public function test_store_with_small_name_en_parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['name_en' => 'in']));
        $response->assertStatus(422)
            ->assertJson(
                $this->validationError('The name en must be between 3 and 50 characters.')
            );
    }

    public function test_store_with_big_name_en_parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['name_en' => 'I\'m very Happy to learn DDT to test my code and improve my skills']));
        $response->assertStatus(422)
            ->assertJson(
                $this->validationError('The name en must be between 3 and 50 characters.')
            );
    }

    public function test_store_without_name_ar_parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['name_ar' => '']));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The name ar field is required.'));
    }

    public function test_store_with_small_name_ar_parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['name_ar' => 'فط']));
        $response->assertStatus(422)
            ->assertJson(
                $this->validationError('The name ar must be between 3 and 50 characters.')
            );
    }

    public function test_store_with_big_name_ar_parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['name_ar' => 'I\'m very Happy to learn DDT to test my code and improve my skills']));
        $response->assertStatus(422)
            ->assertJson(
                $this->validationError('The name ar must be between 3 and 50 characters.')
            );
    }

    public function test_store_without_description_en_parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['description_en' => '']));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The description en field is required.'));
    }

    public function test_store_with_small_description_en_parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['description_en' => 'in']));
        $response->assertStatus(422)
            ->assertJson(
                $this->validationError('The description en must be at least 3 characters.')
            );
    }

    public function test_store_without_description_ar_parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['description_ar' => '']));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The description ar field is required.'));
    }

    public function test_store_with_small_description_ar_parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['description_ar' => 'فط']));
        $response->assertStatus(422)
            ->assertJson(
                $this->validationError('The description ar must be at least 3 characters.')
            );
    }

    public function test_store_without_price_parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['price' => '']));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The price field is required.'));
    }

    public function test_store_with_non_numeric_price_parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['price' => 'test']));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The price must be a number.'));
    }

    public function test_store_with_negative_price_parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['price' => -50]));
        $response->assertStatus(422)
            ->assertJson(
                $this->validationError('The price must be between 0 and 999999.99.')
            );
    }

    public function test_store_with_big_price_parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['price' => 1000000]));
        $response->assertStatus(422)
            ->assertJson(
                $this->validationError('The price must be between 0 and 999999.99.')
            );
    }

    public function test_store_without_main_image_parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['product_main_image' => '']));
        $response->assertStatus(422)
            ->assertJson(
                $this->validationError('The product main image field is required.')
            );
    }

    public function test_store_with_invalid_main_image_parameter()
    {
        $image = UploadedFile::fake()->create('image.pdf');
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['product_main_image' => $image]));
        $response->assertStatus(422)
            ->assertJson(
                $this->validationError('The product main image must be an image.')
            );
    }

    public function test_store_without_images_parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['product_images' => '']));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The product images field is required.'));
    }

    public function test_store_with_invalid_images_parameter()
    {
        $image = UploadedFile::fake()->create('image.png');
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['product_images' => $image]));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The product images must be an array.'));
    }

    public function test_store_with_invalid_element_images_parameter()
    {
        $image = UploadedFile::fake()->create('image.png');
        $pdf = UploadedFile::fake()->create('image.pdf');
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['product_images' => [$image, $pdf]]));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The product_images.1 must be an image.'));
    }

    public function test_store_without__parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['has_discount' => '']));
        $response->assertStatus(201)->assertJson([
            'msg'           => "Product is created successfully",
            'isSuccess'     => true,
            'statusCode'    => 201,
//            'payload'       => null
        ]);
    }

    public function test_store_with_invalid_has_discount_parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['has_discount' => 'test']));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The selected has discount is invalid.'));
    }

    public function test_store_without_discount_parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['discount' => '']));
        $response->assertStatus(201)->assertJson([
            'msg'           => "Product is created successfully",
            'isSuccess'     => true,
            'statusCode'    => 201,
//            'payload'       => null
        ]);
    }

    public function test_store_with_invalid_discount_parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['discount' => 'test']));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The discount must be a number.'));
    }

    public function test_store_with_negative_discount_parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['discount' => -5]));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The discount must be between 0 and 100.'));
    }

    public function test_store_with_big_discount_parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['discount' => 150]));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The discount must be between 0 and 100.'));
    }

    public function test_store_without_active_parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['active' => '']));
        $response->assertStatus(201)->assertJson([
            'msg'           => "Product is created successfully",
            'isSuccess'     => true,
            'statusCode'    => 201,
//            'payload'       => null
        ]);
    }

    public function test_store_with_invalid_active_parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['active' => 'test']));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The selected active is invalid.'));
    }

    public function test_store_with_all_valid_parameters()
    {
        $response = $this->actingAs($this->user)->postJson($this->url, $this->data());
        $response->assertStatus(201)->assertJson([
            'msg'           => "Product is created successfully",
            'isSuccess'     => true,
            'statusCode'    => 201,
//            'payload'       => null
        ]);
    }

    public function test_show_without_authentication()
    {
        $response = $this->getJson($this->url . '/' . $this->product->id);
        $response->assertStatus(401)->assertJson($this->unauthenticated());
    }

    public function test_show_existing_product()
    {
        $response = $this->actingAs($this->user)
            ->getJson($this->url . '/' . $this->product->id);
        $response->assertStatus(200)->assertJson([
            'msg'           => "",
            'isSuccess'     => true,
            'statusCode'    => 200,
//            'payload'       => null
        ]);
    }

    public function test_show_non_existing_product()
    {
        $response = $this->actingAs($this->user)->getJson('/api/dashboard/products/10001');
        $response->assertStatus(404)->assertJson([
            'msg'           => "Not Found",
            'isSuccess'     => false,
            'statusCode'    => 404,
//            'payload'       => null
        ]);
    }

    public function test_update_without_authentication()
    {
        $response = $this->putJson($this->url . '/' . $this->product->id);
        $response->assertStatus(401)->assertJson($this->unauthenticated());
    }

    public function test_update_without_parameters()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id);
        $response->assertStatus(422)
            ->assertJson($this->validationError('The name en field is required.'));
    }

    public function test_update_without_name_en_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'name_en' => ''
            ]));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The name en field is required.'));
    }

    public function test_update_with_small_name_en_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'name_en' => 'in'
            ]));
        $response->assertStatus(422)
            ->assertJson(
                $this->validationError('The name en must be between 3 and 50 characters.')
            );
    }

    public function test_update_with_big_name_en_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'name_en' => 'I\'m very Happy to learn DDT to test my code and improve my skills'
            ]));
        $response->assertStatus(422)
            ->assertJson(
                $this->validationError('The name en must be between 3 and 50 characters.')
            );
    }

    public function test_update_without_name_ar_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'name_ar' => ''
            ]));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The name ar field is required.'));
    }

    public function test_update_with_small_name_ar_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'name_ar' => 'فط'
            ]));
        $response->assertStatus(422)
            ->assertJson(
                $this->validationError('The name ar must be between 3 and 50 characters.')
            );
    }

    public function test_update_with_big_name_ar_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'name_ar' => 'I\'m very Happy to learn DDT to test my code and improve my skills'
            ]));
        $response->assertStatus(422)
            ->assertJson(
                $this->validationError('The name ar must be between 3 and 50 characters.')
            );
    }

    public function test_update_without_description_en_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'description_en' => ''
            ]));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The description en field is required.'));
    }

    public function test_update_with_small_description_en_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'description_en' => 'in'
            ]));
        $response->assertStatus(422)
            ->assertJson(
                $this->validationError('The description en must be at least 3 characters.')
            );
    }

    public function test_update_without_description_ar_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'description_ar' => ''
            ]));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The description ar field is required.'));
    }

    public function test_update_with_small_description_ar_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'description_ar' => 'فط'
            ]));
        $response->assertStatus(422)
            ->assertJson(
                $this->validationError('The description ar must be at least 3 characters.')
            );
    }

    public function test_update_without_price_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'price' => ''
            ]));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The price field is required.'));
    }

    public function test_update_with_non_numeric_price_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'price' => 'test'
            ]));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The price must be a number.'));
    }

    public function test_update_with_negative_price_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'price' => -50
            ]));
        $response->assertStatus(422)
            ->assertJson(
                $this->validationError('The price must be between 0 and 999999.99.')
            );
    }

    public function test_update_with_big_price_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'price' => 1000000
            ]));
        $response->assertStatus(422)
            ->assertJson(
                $this->validationError('The price must be between 0 and 999999.99.')
            );
    }

    public function test_update_without_main_image_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'product_main_image' => ''
            ]));
        $response->assertStatus(202)->assertJson([
            'msg'           => "Product is updated successfully",
            'isSuccess'     => true,
            'statusCode'    => 202,
            'payload'       => true
        ]);
    }

    public function test_update_with_invalid_main_image_parameter()
    {
        $image = UploadedFile::fake()->create('image.pdf');
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'product_main_image' => $image
            ]));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The product main image must be an image.'));
    }

    public function test_update_without_images_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'product_images' => ''
            ]));
        $response->assertStatus(202)->assertJson([
            'msg'           => "Product is updated successfully",
            'isSuccess'     => true,
            'statusCode'    => 202,
            'payload'       => true
        ]);
    }

    public function test_update_with_invalid_images_parameter()
    {
        $image = UploadedFile::fake()->create('image.png');
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'product_images' => $image
            ]));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The product images must be an array.'));
    }

    public function test_update_with_invalid_element_images_parameter()
    {
        $image = UploadedFile::fake()->create('image.png');
        $pdf = UploadedFile::fake()->create('image.pdf');
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'product_images' => [$image, $pdf]
            ]));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The product_images.1 must be an image.'));
    }

    public function test_update_without_has_discount_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'has_discount' => ''
            ]));
        $response->assertStatus(202)->assertJson([
            'msg'           => "Product is updated successfully",
            'isSuccess'     => true,
            'statusCode'    => 202,
            'payload'       => true
        ]);
    }

    public function test_update_with_invalid_has_discount_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'has_discount' => 'test'
            ]));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The selected has discount is invalid.'));
    }

    public function test_update_without_discount_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'discount' => ''
            ]));
        $response->assertStatus(202)->assertJson([
            'msg'           => "Product is updated successfully",
            'isSuccess'     => true,
            'statusCode'    => 202,
            'payload'       => true
        ]);
    }

    public function test_update_with_invalid_discount_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'discount' => 'test'
            ]));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The discount must be a number.'));
    }

    public function test_update_with_negative_discount_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'discount' => -5
            ]));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The discount must be between 0 and 100.'));
    }

    public function test_update_with_big_discount_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'discount' => 150
            ]));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The discount must be between 0 and 100.'));
    }

    public function test_update_without_active_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'active' => ''
            ]));
        $response->assertStatus(202)->assertJson([
            'msg'           => "Product is updated successfully",
            'isSuccess'     => true,
            'statusCode'    => 202,
            'payload'       => true
        ]);
    }

    public function test_update_with_invalid_active_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data([
                'category_id' => '',
                'active' => 'test'
            ]));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The selected active is invalid.'));
    }

    public function test_update_with_all_valid_parameters()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->product->id, $this->data(['category_id' => '']));
        $response->assertStatus(202)->assertJson([
            'msg'           => "Product is updated successfully",
            'isSuccess'     => true,
            'statusCode'    => 202,
            'payload'       => true
        ]);
    }

    public function test_delete_without_authentication()
    {
        $response = $this->deleteJson($this->url . '/' . $this->product->id);
        $response->assertStatus(401)->assertJson($this->unauthenticated());
    }

    public function test_delete_existing_product()
    {
        $response = $this->actingAs($this->user)
            ->deleteJson($this->url . '/' . $this->product->id);
        $response->assertStatus(202)->assertJson([
            'msg'           => "Product is deleted successfully",
            'isSuccess'     => true,
            'statusCode'    => 202,
            'payload'       => true
        ]);
    }

    public function test_delete_non_existing_product()
    {
        $response = $this->actingAs($this->user)->deleteJson($this->url . '/10001');
        $response->assertStatus(404)->assertJson([
            'msg'           => "Not Found",
            'isSuccess'     => false,
            'statusCode'    => 404,
            'payload'       => null
        ]);
    }

    public function test_change_status_without_authentication()
    {
        $response = $this->putJson($this->url . '/' . $this->product->id . '/change-status');
        $response->assertStatus(401)->assertJson($this->unauthenticated());
    }

    public function test_change_status_existing_product()
    {
        $response = $this->actingAs($this->user)->putJson(
            $this->url . '/' . $this->product->id . '/change-status'
        );

        $response->assertStatus(202)->assertJson([
            'msg'           => "Product status is changed successfully",
            'isSuccess'     => true,
            'statusCode'    => 202,
            'payload'       => true
        ]);
    }

    public function test_change_status_non_existing_product()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/10001/change-status');
        $response->assertStatus(404)->assertJson([
            'msg'           => "Not Found",
            'isSuccess'     => false,
            'statusCode'    => 404,
            'payload'       => null
        ]);
    }

    public function test_delete_image_without_authentication()
    {
        $response = $this->deleteJson($this->url . '/1/images/1');
        $response->assertStatus(401)->assertJson($this->unauthenticated());
    }

    public function test_delete_non_existing_image_of_non_existing_product()
    {
        $response = $this->actingAs($this->user)
            ->deleteJson($this->url . '/100000/images/100000');
        $response->assertStatus(404)->assertJson([
            'msg'           => "Not Found",
            'isSuccess'     => false,
            'statusCode'    => 404,
            'payload'       => null
        ]);
    }

    public function test_delete_non_existing_image_of_existing_product()
    {
        $response = $this->actingAs($this->user)
            ->deleteJson($this->url . '/' . $this->product->id . '/images/100000');
        $response->assertStatus(404)->assertJson([
            'msg'           => "Not Found",
            'isSuccess'     => false,
            'statusCode'    => 404,
            'payload'       => null
        ]);
    }

    public function test_delete_existing_image_of_existing_product()
    {
        $product = Product::factory()->hasImages(3)->create();
        $response = $this->actingAs($this->user)
            ->deleteJson($this->url . '/' . $product->id . '/images/' . $product->images[1]->id);
        $response->assertStatus(202)->assertJson([
            'msg'           => "Image is deleted successfully",
            'isSuccess'     => true,
            'statusCode'    => 202,
            'payload'       => null
        ]);
    }
}
