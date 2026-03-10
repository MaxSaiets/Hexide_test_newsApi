<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use App\Http\Resources\NewsResource;
use OpenApi\Attributes as OA;

class NewsController extends Controller
{
    #[OA\Get(
        path: '/api/news',
        summary: 'Отримати список опублікованих новин',
        tags: ['Public News'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, description: 'Пошук по назві або опису', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, description: 'Кількість на сторінку (макс 50)', schema: new OA\Schema(type: 'integer', default: 15)),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Номер сторінки', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Список новин'),
        ]
    )]
    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = min($request->query('per_page', default: 15), 50);

        $news = News::with('user:id,name,avatar,email')
            ->where('is_published', true)
            ->search($search)
            ->latest()
            ->paginate($perPage);

        if ($news->isEmpty()) {
            return response()->json([
                'message' => 'No news found',
            ], 200);
        }
        return NewsResource::collection($news);
    }

    #[OA\Get(
        path: '/api/news/{slug}',
        summary: 'Отримати новину за slug (з блоками контенту)',
        tags: ['Public News'],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, description: 'Slug новини', schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Дані новини з блоками'),
            new OA\Response(response: 404, description: 'Новину не знайдено'),
        ]
    )]
    public function get_new_by_slug($slug)
    {
        $news = News::with(['blocks', 'user:id,name,avatar,email'])->where(['slug' => $slug, 'is_published' => true])->first();

        if (!$news) {
            return response()->json([
                'message' => 'News not found',
            ], 404);
        }

        return new NewsResource($news);
    }
}
