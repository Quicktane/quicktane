<?php

declare(strict_types=1);

namespace App\User\Http\Controllers;

use App\User\Contracts\AuthFacade;
use App\User\Http\Requests\LoginRequest;
use App\User\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthFacade $authFacade,
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authFacade->authenticate(
            $request->validated('email'),
            $request->validated('password'),
        );

        return response()->json([
            'token' => $result['token'],
            'user' => new UserResource($result['user']),
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authFacade->logout($request->user());

        return response()->json([
            'message' => 'Successfully logged out.',
        ], 200);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $this->authFacade->currentUser($request->user());

        return response()->json([
            'user' => new UserResource($user),
        ]);
    }
}
