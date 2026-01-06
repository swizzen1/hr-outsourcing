<?php

namespace App\Http\Controllers\Api;

use App\Actions\Auth\Autorization;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function login(LoginRequest $request, Autorization $authorization): JsonResponse
    {
        $data = $request->validated();

        $token = $authorization->getBearerToken($data);

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
}
