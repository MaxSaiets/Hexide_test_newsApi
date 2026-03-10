<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/register',
        summary: 'Реєстрація нового користувача',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['name', 'email', 'password', 'password_confirmation'],
                    properties: [
                        new OA\Property(property: 'name', type: 'string', example: 'Макс'),
                        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'max@example.com'),
                        new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123'),
                        new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'password123'),
                        new OA\Property(property: 'avatar', type: 'string', format: 'binary', description: 'Аватар (файл)'),
                        new OA\Property(property: 'bio', type: 'string', example: 'Про мене'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Успішна реєстрація'),
            new OA\Response(response: 422, description: 'Помилка валідації'),
        ]
    )]
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bio' => 'nullable|string',
        ]);

        $data['password'] = Hash::make($data['password']);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create($data);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ], 201);
    }

    #[OA\Post(
        path: '/api/login',
        summary: 'Авторизація користувача',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'max@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Успішний вхід'),
            new OA\Response(response: 401, description: 'Невірні облікові дані'),
        ]
    )]
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        if (!Auth::attempt($data)) {
            return response()->json([
                'message' => 'Credentials invalid'
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    #[OA\Post(
        path: '/api/logout',
        summary: 'Вихід з акаунта',
        tags: ['Auth'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Успішний вихід'),
        ]
    )]
    public function logout(Request $request)
    {
        if ($request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'Logged out',
        ]);
    }
}
