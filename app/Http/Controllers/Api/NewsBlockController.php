<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\NewsBlock;
use App\Http\Requests\StoreNewsBlockRequest;
use App\Http\Requests\UpdateNewsBlockRequest;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;
use App\Http\Resources\NewsBlockResource;

class NewsBlockController extends Controller
{
    #[OA\Post(
        path: '/api/new_blocks',
        summary: 'Створити блок контенту для новини',
        tags: ['News Blocks'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['news_id', 'content[type]'],
                    properties: [
                        new OA\Property(property: 'news_id', type: 'integer', example: 1),
                        new OA\Property(property: 'content[type]', type: 'string', enum: ['text', 'image', 'text_image_right', 'text_image_left'], example: 'text'),
                        new OA\Property(property: 'content[text]', type: 'string', example: 'Текст блоку'),
                        new OA\Property(property: 'content[image]', type: 'string', format: 'binary', description: 'Картинка блоку'),
                        new OA\Property(property: 'position', type: 'integer', example: 0),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Блок створено'),
            new OA\Response(response: 401, description: 'Неавторизований'),
            new OA\Response(response: 403, description: 'Заборонено'),
            new OA\Response(response: 422, description: 'Помилка валідації'),
        ]
    )]
    public function store(StoreNewsBlockRequest $request, $userNewsId)
    {
        $news = $request->user()->news()->findOrFail($userNewsId);
        
        $data = $request->validated();
     
        if ($request->hasFile('image_path')) {
            $data['image_path'] = $request->file('image_path')->store('news_blocks', 'public');
        }

        $news_block = $news->blocks()->create($data);

        return (new NewsBlockResource($news_block))->response()->setStatusCode(201);
    }

    #[OA\Post(
        path: '/api/new_blocks/{id}',
        summary: 'Оновити блок контенту (POST з _method=PUT для файлів)',
        tags: ['News Blocks'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'ID блоку', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: '_method', type: 'string', example: 'PUT', description: 'Спуфінг методу'),
                        new OA\Property(property: 'content[type]', type: 'string', enum: ['text', 'image', 'text_image_right', 'text_image_left']),
                        new OA\Property(property: 'content[text]', type: 'string'),
                        new OA\Property(property: 'content[image]', type: 'string', format: 'binary'),
                        new OA\Property(property: 'position', type: 'integer'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Блок оновлено'),
            new OA\Response(response: 401, description: 'Неавторизований'),
            new OA\Response(response: 403, description: 'Заборонено'),
            new OA\Response(response: 422, description: 'Помилка валідації'),
        ]
    )]
    public function update(UpdateNewsBlockRequest $request, $id)
    {
        $news_block = NewsBlock::findOrFail($id);
        Gate::authorize('update', $news_block->news);

        $data = $request->validated();

        if ($request->hasFile('image_path')) {
            if ($news_block->image_path) {
                Storage::disk('public')->delete($news_block->image_path);
            }
            $data['image_path'] = $request->file('image_path')->store('news_blocks', 'public');
        }

        $news_block->update($data);

        return new NewsBlockResource($news_block->fresh());
    }

    #[OA\Delete(
        path: '/api/new_blocks/{id}',
        summary: 'Видалити блок контенту',
        tags: ['News Blocks'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'ID блоку', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Блок видалено'),
            new OA\Response(response: 401, description: 'Неавторизований'),
            new OA\Response(response: 403, description: 'Заборонено'),
        ]
    )]
    public function destroy(Request $request, $id)
    {
        $news_block = NewsBlock::findOrFail($id);
        Gate::authorize('delete', $news_block->news);

        if ($news_block->image_path) {
            Storage::disk('public')->delete($news_block->image_path);
        }

        $news_block->delete();

        return response()->json([
            'message' => 'News block deleted',
        ]);
    }
}
