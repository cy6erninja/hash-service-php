<?php

namespace Database\Factories;

use App\Models\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;
use Random\Randomizer;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hash>
 */
class HashFactory extends Factory
{
    protected $model = Hash::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $randomizer = new Randomizer();
        $data = $randomizer->getBytesFromString("abcdefghijklmnopqrstuvwxyz0123456789", 32);

        return [
            'data' => $data,
            'data_hash' => sha1($data)
        ];
    }
}
