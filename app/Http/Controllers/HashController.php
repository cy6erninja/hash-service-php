<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Services\HashServiceInterface as ServicesHashServiceInterface;
use Illuminate\Support\Facades\Validator;

class HashController extends Controller
{
    public function __construct(
        protected ServicesHashServiceInterface $hashService
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
                    "Invalid hash requested: %s. Hash format is $regexp.",
                    $hash
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
                    sprintf('Hash %s is not found.', $hash)
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
