<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum')->only('logout');
        $this->middleware('guest:sanctum')->except('logout');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email|max:100',
            'password' => 'required|string|max:100',
            'name' => 'required|string|max:100'
        ]);

        $validated['password'] = bcrypt($validated['password']);
        /** @var User $user */
        $user = User::query()->create($validated);

        $token = $user->createToken('user');

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token->plainTextToken,
            'type' => 'Bearer'
        ]);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'string|required',
            'password' => 'string|required'
        ]);
        $attempt = Auth::attempt($validated);

        if ($attempt === false) {
            return response()->json([
                'message' => 'Пользователь не найден'
            ]);
        }

        /** @var User $user */
        $user = $request->user();

        $token = $user->createToken('user');

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token->plainTextToken,
            'type' => 'Bearer'
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Unauthenticated'
        ]);
    }
}
