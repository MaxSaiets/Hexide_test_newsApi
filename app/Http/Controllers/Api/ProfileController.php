<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

class ProfileController extends Controller
{
    #[OA\Get(
        path: '/api/profile',
        summary: 'Отримати профіль поточного користувача',
        tags: ['Profile'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Дані профілю'),
            new OA\Response(response: 401, description: 'Неавторизований'),
        ]
    )]
    public function show_profile(Request $request)
    {
        return response()->json($request->user());
    }

    #[OA\Post(
        path: '/api/profile',
        summary: 'Оновити профіль (POST з _method=PUT для файлів)',
        tags: ['Profile'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: '_method', type: 'string', example: 'PUT', description: 'Спуфінг методу'),
                        new OA\Property(property: 'name', type: 'string', example: 'Новий Макс'),
                        new OA\Property(property: 'email', type: 'string', format: 'email'),
                        new OA\Property(property: 'bio', type: 'string'),
                        new OA\Property(property: 'avatar', type: 'string', format: 'binary', description: 'Аватар'),
                        new OA\Property(property: 'password', type: 'string', format: 'password'),
                        new OA\Property(property: 'password_confirmation', type: 'string', format: 'password'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Профіль оновлено'),
            new OA\Response(response: 401, description: 'Неавторизований'),
            new OA\Response(response: 422, description: 'Помилка валідації'),
        ]
    )]
    public function update_profile(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $request->user()->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json($user);
    }
}
