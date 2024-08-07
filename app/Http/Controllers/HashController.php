<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Services\HashServiceInterface;
use Illuminate\Support\Facades\Validator;

class HashController extends Controller
{
    public const string MESSAGE_COLLISION_DETECTED = "Warning: collision detected for provided data.";
    public const string TEMPLATE_INVALID_HASH_REQUESTED = "Invalid hash requested: %s. Hash format is %s.";
    public const string TEMPLATE_HASH_IS_NOT_FOUND = "Hash %s is not found.";

    public function __construct(
        protected HashServiceInterface $hashService
    ) {
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required'
        ]);

        if ($validator->fails()) {
            return Response::json(
                [
                    'errors' => $validator->errors()
                ],
                400
            );
        }

        $hash = $this->hashService->createHash($request->json()->get('data'));
        $collisions = $this->hashService->retrieveData($hash);

        if (count($collisions) > 1) {
            return Response::json([
                'hash' => $hash,
                'additional_message' => static::MESSAGE_COLLISION_DETECTED
            ]);
        }

        return Response::json([
            'hash' => $hash
        ]);
    }

    public function read(string $hash)
    {
        $regexp = '/^[a-f0-9]{40}$/i';
        $validator = Validator::make(
            ['hash' => $hash],
            ['hash' => "regex:$regexp"],
            [
                'regex' => sprintf(
                    static::TEMPLATE_INVALID_HASH_REQUESTED,
                    $hash,
                    $regexp
                ),
            ]
        );

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->errors()
            ], 400);
        }

        $dataArray = $this->hashService->retrieveData($hash);

        if (count($dataArray) === 0) {
            return Response::json([
                'errors' => [
                    sprintf(static::TEMPLATE_HASH_IS_NOT_FOUND, $hash)
                ]
            ], 404);
        }

        if (count($dataArray) === 1) {
            return Response::json([
                'item' => array_shift($dataArray),
            ]);
        }

        return Response::json([
            'item' => array_shift($dataArray),
            'collisions' => $dataArray
        ]);
    }
}
