<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\Hash;

class HashController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->json()->get('data');

        if (!$data) {
            return Response::json(
                [
                    'errors' => [
                        '"data" field is absent in request data.'
                    ]
                ],
                400
            );
        }

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
        if (!preg_match('/^[a-f0-9]{40}$/i', $hash)) {
            return Response::json([
                'errors' => sprintf(
                    "Invalid hash requested: %s. Hash format is /^[a-f0-9]{40}$/i.",
                    $hash
                ),
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
