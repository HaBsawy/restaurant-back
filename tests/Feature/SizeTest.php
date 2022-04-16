<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Size;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SizeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create();
        $this->size = Size::factory()->create();
        $this->url = '/api/dashboard/products/' . $this->product['id'] . '/sizes';
    }

    private function data($data = []) : array
    {
        $default = [
            'name_en'       => 'Normal',
            'name_ar'       => 'وسط',
            'price'         => 100,
            'has_discount'  => 1,
            'discount'      => 10,
            'active'        => 1,
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

    public function test_index_with_non_existing_product()
    {
        $response = $this->actingAs($this->user)->getJson('/api/dashboard/products/1001/sizes');
        $response->assertStatus(404)->assertJson([
            'msg'           => "Not Found",
            'isSuccess'     => false,
            'statusCode'    => 404,
            'payload'       => null
        ]);
    }

    public function test_index_with_existing_product()
    {
        $response = $this->actingAs($this->user)->getJson($this->url);
        $response->assertStatus(200)->assertJson([
            'msg'           => '',
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

    public function test_store_with_non_existing_product()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/dashboard/products/1001/sizes', $this->data());
        $response->assertStatus(404)->assertJson([
            'msg'           => "Not Found",
            'isSuccess'     => false,
            'statusCode'    => 404,
            'payload'       => null
        ]);
    }

    public function test_store_without_parameters()
    {
        $response = $this->actingAs($this->user)->postJson($this->url);
        $response->assertStatus(422)
            ->assertJson($this->validationError('The name en field is required.'));
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

    public function test_store_without_has_discount_parameter()
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->url, $this->data(['has_discount' => '']));
        $response->assertStatus(201)->assertJson([
            'msg'           => "Size is created successfully",
            'isSuccess'     => true,
            'statusCode'    => 201,
//            'payload'       => null
        ]);
        $this->assertDatabaseCount('sizes', 2);
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
            'msg'           => "Size is created successfully",
            'isSuccess'     => true,
            'statusCode'    => 201,
//            'payload'       => null
        ]);
        $this->assertDatabaseCount('sizes', 2);
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
            'msg'           => "Size is created successfully",
            'isSuccess'     => true,
            'statusCode'    => 201,
//            'payload'       => null
        ]);
        $this->assertDatabaseCount('sizes', 2);
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
            'msg'           => "Size is created successfully",
            'isSuccess'     => true,
            'statusCode'    => 201,
//            'payload'       => null
        ]);
        $this->assertDatabaseCount('sizes', 2);
    }

    public function test_update_without_authentication()
    {
        $response = $this->putJson($this->url . '/' . $this->size['id']);
        $response->assertStatus(401)->assertJson($this->unauthenticated());
    }

    public function test_update_with_non_existing_product()
    {
        $response = $this->actingAs($this->user)
            ->putJson('/api/dashboard/products/1001/sizes' . '/' . $this->size['id'],
                $this->data());
        $response->assertStatus(404)->assertJson([
            'msg'           => "Not Found",
            'isSuccess'     => false,
            'statusCode'    => 404,
            'payload'       => null
        ]);
    }

    public function test_update_with_non_existing_size()
    {
        $response = $this->actingAs($this->user)
            ->putJson('/api/dashboard/products/1001/sizes' . '/1001', $this->data());
        $response->assertStatus(404)->assertJson([
            'msg'           => "Not Found",
            'isSuccess'     => false,
            'statusCode'    => 404,
            'payload'       => null
        ]);
    }

    public function test_update_without_parameters()
    {
        $response = $this->actingAs($this->user)->putJson($this->url . '/' . $this->size['id']);
        $response->assertStatus(422)
            ->assertJson($this->validationError('The name en field is required.'));
    }

    public function test_update_without_name_en_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->size['id'], $this->data(['name_en' => '']));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The name en field is required.'));
    }

    public function test_update_with_small_name_en_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->size['id'], $this->data(['name_en' => 'in']));
        $response->assertStatus(422)
            ->assertJson(
                $this->validationError('The name en must be between 3 and 50 characters.')
            );
    }

    public function test_update_with_big_name_en_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->size['id'], $this->data(['name_en' => 'I\'m very Happy to learn DDT to test my code and improve my skills']));
        $response->assertStatus(422)
            ->assertJson(
                $this->validationError('The name en must be between 3 and 50 characters.')
            );
    }

    public function test_update_without_name_ar_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->size['id'], $this->data(['name_ar' => '']));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The name ar field is required.'));
    }

    public function test_update_with_small_name_ar_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->size['id'], $this->data(['name_ar' => 'فط']));
        $response->assertStatus(422)
            ->assertJson(
                $this->validationError('The name ar must be between 3 and 50 characters.')
            );
    }

    public function test_update_with_big_name_ar_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->size['id'], $this->data(['name_ar' => 'I\'m very Happy to learn DDT to test my code and improve my skills']));
        $response->assertStatus(422)
            ->assertJson(
                $this->validationError('The name ar must be between 3 and 50 characters.')
            );
    }

    public function test_update_without_price_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->size['id'], $this->data(['price' => '']));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The price field is required.'));
    }

    public function test_update_with_non_numeric_price_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->size['id'], $this->data(['price' => 'test']));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The price must be a number.'));
    }

    public function test_update_with_negative_price_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->size['id'], $this->data(['price' => -50]));
        $response->assertStatus(422)
            ->assertJson(
                $this->validationError('The price must be between 0 and 999999.99.')
            );
    }

    public function test_update_with_big_price_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->size['id'], $this->data(['price' => 1000000]));
        $response->assertStatus(422)
            ->assertJson(
                $this->validationError('The price must be between 0 and 999999.99.')
            );
    }

    public function test_update_without_has_discount_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->size['id'], $this->data(['has_discount' => '']));
        $response->assertStatus(202)->assertJson([
            'msg'           => "Size is updated successfully",
            'isSuccess'     => true,
            'statusCode'    => 202,
//            'payload'       => null
        ]);
    }

    public function test_update_with_invalid_has_discount_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->size['id'], $this->data(['has_discount' => 'test']));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The selected has discount is invalid.'));
    }

    public function test_update_without_discount_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->size['id'], $this->data(['discount' => '']));
        $response->assertStatus(202)->assertJson([
            'msg'           => "Size is updated successfully",
            'isSuccess'     => true,
            'statusCode'    => 202,
//            'payload'       => null
        ]);
    }

    public function test_update_with_invalid_discount_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->size['id'], $this->data(['discount' => 'test']));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The discount must be a number.'));
    }

    public function test_update_with_negative_discount_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->size['id'], $this->data(['discount' => -5]));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The discount must be between 0 and 100.'));
    }

    public function test_update_with_big_discount_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->size['id'], $this->data(['discount' => 150]));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The discount must be between 0 and 100.'));
    }

    public function test_update_without_active_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->size['id'], $this->data(['active' => '']));
        $response->assertStatus(202)->assertJson([
            'msg'           => "Size is updated successfully",
            'isSuccess'     => true,
            'statusCode'    => 202,
//            'payload'       => null
        ]);
    }

    public function test_update_with_invalid_active_parameter()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->size['id'], $this->data(['active' => 'test']));
        $response->assertStatus(422)
            ->assertJson($this->validationError('The selected active is invalid.'));
    }

    public function test_update_with_all_valid_parameters()
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->url . '/' . $this->size['id'], $this->data());
        $response->assertStatus(202)->assertJson([
            'msg'           => "Size is updated successfully",
            'isSuccess'     => true,
            'statusCode'    => 202,
//            'payload'       => null
        ]);
    }

    public function test_delete_without_authentication()
    {
        $response = $this->deleteJson($this->url . '/' . $this->size['id']);
        $response->assertStatus(401)->assertJson($this->unauthenticated());
    }

    public function test_delete_with_non_existing_product()
    {
        $response = $this->actingAs($this->user)
            ->deleteJson('/api/dashboard/products/1001/sizes/' . $this->size['id']);
        $response->assertStatus(404)->assertJson([
            'msg'           => "Not Found",
            'isSuccess'     => false,
            'statusCode'    => 404,
            'payload'       => null
        ]);
    }

    public function test_delete_existing_size()
    {
        $response = $this->actingAs($this->user)
            ->deleteJson($this->url . '/' . $this->size['id']);
        $response->assertStatus(202)->assertJson([
            'msg'           => "Size is deleted successfully",
            'isSuccess'     => true,
            'statusCode'    => 202,
            'payload'       => true
        ]);
        $this->assertDatabaseCount('sizes', 0);
    }

    public function test_delete_non_existing_size()
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
        $response = $this->putJson($this->url . '/' . $this->size['id'] . '/change-status');
        $response->assertStatus(401)->assertJson($this->unauthenticated());
    }

    public function test_change_status_with_non_existing_product()
    {
        $response = $this->actingAs($this->user)->putJson(
            '/api/dashboard/products/1001/sizes/' . $this->size['id'] . '/change-status'
        );

        $response->assertStatus(404)->assertJson([
            'msg'           => "Not Found",
            'isSuccess'     => false,
            'statusCode'    => 404,
            'payload'       => null
        ]);
    }

    public function test_change_status_existing_size()
    {
        $response = $this->actingAs($this->user)->putJson(
            $this->url . '/' . $this->size['id'] . '/change-status'
        );

        $response->assertStatus(202)->assertJson([
            'msg'           => "Size status is changed successfully",
            'isSuccess'     => true,
            'statusCode'    => 202,
            'payload'       => true
        ]);
    }

    public function test_change_status_non_existing_size()
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
}
