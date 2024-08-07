<?php

namespace App\Services;

use App\Models\Hash;

class HashService implements HashServiceInterface
{
    public function createHash(string $data): string
    {
        $dataHash = sha1($data);

        $hash = new Hash();
        $hash->fill(['data' => $data, 'data_hash' => $dataHash]);
        $hash->save();

        return $dataHash;
    }

    public function retrieveData(string $hash): array
    {
        $hashCollection = Hash::where('data_hash', $hash)->get();

        return array_map(fn ($item) => $item->data, $hashCollection->all());
    }
}
