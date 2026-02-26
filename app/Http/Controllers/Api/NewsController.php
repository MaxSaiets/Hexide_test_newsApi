<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;

class NewsController extends Controller
{
    #[OA\Get(
        path: '/api/user_news',
        summary: 'Отримати список власних новин',
        tags: ['User News'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, description: 'Пошук', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'is_published', in: 'query', required: false, description: 'Фільтр за статусом', schema: new OA\Schema(type: 'boolean')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, description: 'Кількість на сторінку', schema: new OA\Schema(type: 'integer', default: 15)),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Номер сторінки', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Список новин користувача'),
            new OA\Response(response: 401, description: 'Неавторизований'),
        ]
    )]
    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = min($request->query('per_page', default: 15), 50);
        $query = $request->user()->news()->search($search);

        if ($request->has('is_published')) {
            $query->where('is_published', $request->is_published);
        }

        return NewsResource::collection($query->latest()->paginate($perPage));
    }

    #[OA\Post(
        path: '/api/user_news',
        summary: 'Створити новину',
        tags: ['User News'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['title'],
                    properties: [
                        new OA\Property(property: 'title', type: 'string', example: 'Назва новини'),
                        new OA\Property(property: 'short_description', type: 'string', example: 'Короткий опис'),
                        new OA\Property(property: 'image', type: 'string', format: 'binary', description: 'Картинка новини'),
                        new OA\Property(property: 'is_published', type: 'boolean', example: true),
                        new OA\Property(property: 'published_at', type: 'string', format: 'date-time'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Новину створено'),
            new OA\Response(response: 401, description: 'Неавторизований'),
            new OA\Response(response: 422, description: 'Помилка валідації'),
        ]
    )]
    public function store(StoreNewsRequest $request)
    {
        $news = DB::transaction(function () use ($request) {
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('news_images', 'public');
            }

            return $request->user()->news()->create($data);
        });

        return (new NewsResource($news))->response()->setStatusCode(201);
    }

    #[OA\Get(
        path: '/api/user_news/{id}',
        summary: 'Отримати конкретну новину користувача',
        tags: ['User News'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'ID новини', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Дані новини'),
            new OA\Response(response: 401, description: 'Неавторизований'),
            new OA\Response(response: 404, description: 'Не знайдено'),
        ]
    )]
    public function show(Request $request, $id)
    {
        $news = News::findOrFail($id);
        Gate::authorize('view', $news);

        return new NewsResource($news);
    }

    #[OA\Post(
        path: '/api/user_news/{id}',
        summary: 'Оновити новину (POST з _method=PUT для файлів)',
        tags: ['User News'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'ID новини', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: '_method', type: 'string', example: 'PUT', description: 'Спуфінг методу'),
                        new OA\Property(property: 'title', type: 'string', example: 'Оновлена назва'),
                        new OA\Property(property: 'short_description', type: 'string'),
                        new OA\Property(property: 'image', type: 'string', format: 'binary'),
                        new OA\Property(property: 'is_published', type: 'boolean'),
                        new OA\Property(property: 'published_at', type: 'string', format: 'date-time'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Новину оновлено'),
            new OA\Response(response: 401, description: 'Неавторизований'),
            new OA\Response(response: 422, description: 'Помилка валідації'),
        ]
    )]
    public function update(UpdateNewsRequest $request, $id)
    {
        $news = News::findOrFail($id);
        Gate::authorize('update', $news);

        DB::transaction(function () use ($news, $request) {
            $data = $request->validated();

            if ($request->hasFile('image')) {
                if ($news->image) {
                    Storage::disk('public')->delete($news->image);
                }
                $data['image'] = $request->file('image')->store('news_images', 'public');
            }

            $news->update($data);
        });

        return new NewsResource($news);
    }

    #[OA\Delete(
        path: '/api/user_news/{id}',
        summary: 'Видалити новину',
        tags: ['User News'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'ID новини', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Новину видалено'),
            new OA\Response(response: 401, description: 'Неавторизований'),
            new OA\Response(response: 404, description: 'Не знайдено'),
        ]
    )]
    public function destroy(Request $request, $id)
    {
        $news = News::findOrFail($id);
        Gate::authorize('delete', $news);

        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }

        $news->delete();

        return response()->json([
            'message' => 'News deleted',
        ]);
    }
}
