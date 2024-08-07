<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Hash;

class HashTest extends TestCase
{
    use RefreshDatabase;

    protected const string HASH_URL = '/hash';

    protected string $data;
    protected string $collisionData;
    protected string $dataHash;

    protected function setUp(): void
    {
        parent::setUp();
        $this->data = 'teststring';
        $this->collisionData = 'collisiondata';
        $this->dataHash = sha1($this->data);
    }

    public function test_hash_is_stored_successfully_without_collisions(): void
    {
        $expectedResponse = [
            'hash' => $this->dataHash
        ];

        $response = $this->postJson(static::HASH_URL, [
            'data' => $this->data
        ]);

        $response->assertStatus(200);
        $response->assertExactJson($expectedResponse);
    }

    public function test_hash_is_stored_successfully_with_collisions(): void
    {
        Hash::factory()->create([
            'data' => $this->collisionData,
            'data_hash' => $this->dataHash
        ]);
        $expectedResponse = [
            'hash' => $this->dataHash,
            'additional_message' => 'Warning: collision detected for provided data.'
        ];

        $response = $this->postJson(static::HASH_URL, [
            'data' => $this->data
        ]);

        $response->assertStatus(200);
        $response->assertExactJson($expectedResponse);
    }

    public function test_hash_is_read_successfully_without_collisions(): void
    {
        Hash::factory()->create([
            'data' => $this->data,
            'data_hash' => $this->dataHash
        ]);
        $expectedResponse = ['item' => $this->data];

        $response = $this->get(static::HASH_URL . '/' . $this->dataHash);

        $response->assertStatus(200);
        $response->assertExactJson($expectedResponse);
    }

    public function test_hash_is_read_successfully_with_collisions(): void
    {

        Hash::factory()->create([
            'data' => $this->data,
            'data_hash' => $this->dataHash
        ]);
        Hash::factory()->create([
            'data' => $this->collisionData,
            'data_hash' => $this->dataHash
        ]);

        // Create hash ficture to have collision.
        $expectedResponse = [
            'item' => $this->data,
            'collisions' => [$this->collisionData]
        ];

        $response = $this->get(static::HASH_URL . '/' . $this->dataHash);

        $response->assertStatus(200);
        $response->assertExactJson($expectedResponse);
    }

    public function test_hash_is_not_stored_without_data_field_provided(): void
    {
        $expectedResponse = ['errors' => ['data' => ["The data field is required."]]];

        $response = $this->post(static::HASH_URL, []);

        $response->assertStatus(400);
        $response->assertExactJson($expectedResponse);
    }

    public function test_404_error_if_hash_is_not_found(): void
    {
        $expectedResponse = ['errors' => ['Hash ' . $this->dataHash . " is not found."]];

        $response = $this->get(static::HASH_URL . '/' . $this->dataHash);

        $response->assertStatus(404);
        $response->assertExactJson($expectedResponse);
    }

    public function test_invalid_hash_requested(): void
    {
        $hash = 'invalidhash';
        $expectedResponse = [
            'errors' => ['hash' => ["Invalid hash requested: $hash. Hash format is /^[a-f0-9]{40}$/i."]]
        ];

        $response = $this->get(static::HASH_URL . '/' . $hash);

        $response->assertStatus(400);
        $response->assertExactJson($expectedResponse);
    }
}
