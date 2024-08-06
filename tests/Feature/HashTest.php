<?php

namespace Tests\Feature;

use Tests\TestCase;

class HashTest extends TestCase
{
    protected const string STORE_URL = '/store';
    protected const string READ_URL = '/hash';

    protected string $data;
    protected string $dataHash;

    protected function setUp(): void
    {
        parent::setUp();

        $this->data = 'teststring';
        $this->dataHash = sha1($this->data);
    }

    public function test_hash_is_stored_successfully_without_collisions(): void
    {
        $expectedResponse = [
            'hash' => $this->dataHash
        ];

        $response = $this->post(static::STORE_URL, [
            'data' => $this->data
        ]);

        $response->assertExactJson($expectedResponse);
        $response->assertStatus(200);
    }

    public function test_hash_is_stored_successfully_with_collisions(): void {
        // Create fixtures to have collisions.
        $expectedResponse = [
            'hash' => $this->dataHash
        ];

        $response = $this->post(static::STORE_URL, [
            'data' => $this->data
        ]);

        $response->assertExactJson($expectedResponse);
        $response->assertStatus(200);
    }

    public function test_hash_is_read_successfully_without_collisions(): void
    {
        $expectedResponse = ['item' => $this->dataHash];

        $response = $this->get(static::READ_URL . '/' . $this->dataHash);

        $response->assertStatus(200);
        $response->assertExactJson($expectedResponse);
    }

    public function test_hash_is_read_successfully_with_collisions(): void
    {
        $collisionData = 'collisiondata';
        // Create hash ficture to have collision.

        $expectedResponse = [
            'item' => $this->data,
            'collisions' => [$collisionData]
        ];
        $response = $this->get(static::READ_URL . '/' . $this->dataHash);
        $response->assertStatus(200);
        $response->assertJson($expectedResponse);
    }

    public function test_hash_is_not_stored_without_data_field_provided(): void
    {
        $expectedResponse = ['errors' => [
            '"data" field is absent in request data.'
        ]];
        $response = $this->post(static::STORE_URL, []);

        $response->assertStatus(400);
        $response->assertExactJson($expectedResponse);
    }

    public function test_404_error_if_hash_is_not_found(): void
    {
        $expectedResponse = ['errors' => ['Hash ' . $this->dataHash . " is not found."]];
        $response = $this->get(static::READ_URL . '/' . $this->dataHash);

        $response->assertStatus(404);
        $response->assertExactJson($expectedResponse);
    }
}
