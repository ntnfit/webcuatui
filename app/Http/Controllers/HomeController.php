<?php

namespace App\Http\Controllers;

use App\Models\blogs;
use App\Models\Product;
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

        // Same cache window for the addon showcase.
        $featuredAddons = Cache::remember('home.featured-addons', now()->addMinutes(10), function () {
            return Product::active()->addons()->orderBy('name')->take(6)->get();
        });

        return view('home', [
            'latestArticles' => $latestArticles,
            'featuredAddons' => $featuredAddons,
        ]);
    }
}
