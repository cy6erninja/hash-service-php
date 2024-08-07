<?php

namespace App\Services;

interface HashServiceInterface
{
    public function createHash(string $data): string;

    public function retrieveData(string $hash): array;
}
