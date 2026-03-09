<?php

namespace App\Http\Controllers\Web;

use App\Models\News;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $news = News::with('user:id,name,avatar')
            ->where('is_published', true)
            ->search($search)
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('news.index', compact('news', 'search'));
    }

    public function show($slug)
    {
        $news = News::with(['blocks', 'user'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('news.show', compact('news'));
    }
}
