<?php

namespace App\Http\Controllers;

use App\Models\blogs;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function __invoke()
    {
        // Latest posts rarely change; cache to avoid DB + eager loads on every hit.
        $latestArticles = Cache::remember('home.latest-articles', now()->addMinutes(10), function () {
            return blogs::with(['user', 'categories', 'tags'])
                ->published()
                ->take(6)
                ->get()
                ->map(fn ($post) => $post->getDataArray())
                ->all();
        });

        return view('home', ['latestArticles' => $latestArticles]);
    }
}
