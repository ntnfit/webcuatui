<?php

namespace App\Http\Controllers;

use App\Models\blogs;
use App\Models\Product;
use Illuminate\Http\Response;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    /** Dynamic sitemap: static pages, marketplace addons, shop products and published posts. */
    public function show(): Response
    {
        return response($this->build()->render(), 200, ['Content-Type' => 'application/xml']);
    }

    /** Writes the same sitemap to public/sitemap.xml for deployments that serve the static file. */
    public function generateSitemap(): void
    {
        $this->build()->writeToFile(public_path('sitemap.xml'));
    }

    private function build(): Sitemap
    {
        $sitemap = Sitemap::create();

        foreach (['home', 'marketplace.index', 'tools.index', 'blogs.index', 'shop.index'] as $name) {
            $sitemap->add(Url::create(route($name)));
        }

        Product::active()->addons()->get(['slug', 'updated_at'])->each(
            fn (Product $addon) => $sitemap->add(
                Url::create(route('marketplace.show', $addon->slug))->setLastModificationDate($addon->updated_at)
            )
        );

        Product::active()->where('type', Product::TYPE_PHYSICAL)->get(['slug', 'updated_at'])->each(
            fn (Product $product) => $sitemap->add(
                Url::create(route('shop.show', $product->slug))->setLastModificationDate($product->updated_at)
            )
        );

        blogs::published()->get(['slug', 'updated_at'])->each(
            fn (blogs $post) => $sitemap->add(
                Url::create(route('blogs.show', $post->slug))->setLastModificationDate($post->updated_at)
            )
        );

        return $sitemap;
    }
}
