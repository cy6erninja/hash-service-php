<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\Hash;
use Illuminate\Support\Facades\Validator;

class HashController extends Controller
{
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

        $data = $request->json()->get('data');

        $dataHash = sha1($data);

        $hash = new Hash();
        $hash->fill(['data' => $data, 'data_hash' => $dataHash]);
        $hash->save();

        return Response::json([
            'hash' => $dataHash
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

        $hashCollection = Hash::where('data_hash', $hash)->get();
        $dataArray = array_map(fn ($item) => $item->data, $hashCollection->all());

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
