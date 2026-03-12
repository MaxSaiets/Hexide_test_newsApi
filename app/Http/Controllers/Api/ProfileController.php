<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;
use App\Http\Resources\UserResource;
use App\Http\Requests\UpdateProfileRequest;

class ProfileController extends Controller
{
    #[OA\Get(
        path: '/profile',
        summary: 'Отримати профіль поточного користувача',
        tags: ['Profile'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Дані профілю'),
            new OA\Response(response: 401, description: 'Неавторизований'),
        ]
    )]
    public function show(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        return new UserResource($user->load('profile'));
    }

    #[OA\Post(
        path: '/profile',
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
    public function update(UpdateProfileRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $data = $request->validated();

        $userFields = array_filter([
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
        ]);

        if(!empty($data['password'])) {
            $userFields['password'] = Hash::make($data['password']);
        }
        if(!empty($userFields)) {
            $user->update($userFields);
        }

        $profileData = [];
        
        if(isset($data['bio'])) {
            $profileData['bio'] = $data['bio'];
        }
    
        if ($request->hasFile('avatar')) {
            if ($user->profile?->avatar) {
                Storage::disk('public')->delete($user->profile->avatar);
            }
            $profileData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if (!empty($profileData)) {
            $user->profile()->updateOrCreate(['user_id'=> $user->id], $profileData);
        }

        return new UserResource($user->fresh()->load('profile'));
    }
}
