<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Sitemap\SitemapGenerator;
class SitemapController extends Controller
{
    public function generateSitemap()
    {
        SitemapGenerator::create('https://toilamerp.com')->writeToFile(public_path('sitemap.xml'));
    }
}
