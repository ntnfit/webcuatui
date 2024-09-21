<?php

namespace App\Http\Controllers\Articles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\blogs;
use App\Models\Category as ArticleCategory;

class ListArticlesController extends Controller
{
    public function __invoke()
    {

        // clear cache
         cache()->forget('articles');
        seo()
            ->title('bài viết')
            ->description('chia sẻ về các bài viết công nghệ, lập trình, ERP, SAP B1, NETSUITE, Misa.')
            ->image('https://previewlinks.io/generate/templates/1055/meta?url=' . url()->current())
            ->keywords('SAP Business One, SAP S4HANA, Webapp, ERP,
        Oracle Netsuite, SAP B1, SAP BTP, SAP ABAP, SAP Fiori, SAP UI5, SAP HANA,
        Dịch vụ vận hành SAP B1, Quản trị hệ thống SAP B1, Dịch vụ tư vấn SAP B1,
        Dịch vụ tư vấn SAP S4HANA, Dịch vụ tư vấn Webapp,
        Dịch vụ tư vấn ERP, Dịch vụ tư vấn Oracle Netsuite, Dịch vụ tư vấn SAP BTP, Dịch vụ tư vấn SAP ABAP,
        Gia hạn license SAP B1,Dịch vụ tư vấn SAP HANA')
            ->tag('previewlinks:overline', 'SAP B1')
            ->tag('previewlinks:title', 'Bài viết')
            ->tag('previewlinks:subtitle', 'chia sẻ về các bài viết công nghệ, lập trình, ERP, SAP B1, NETSUITE, Misa.')
            ->tag('previewlinks:image', 'https://filamentphp.com/images/icon.png')
            ->tag('previewlinks:repository', 'harrydev/sap');

        return view('blogs', [
            'articles' => cache()->remember(
                'articles',
                now()->addMinutes(15),
                function (): array {
                    // $stars = Star::query()
                    //     ->toBase()
                    //     ->where('starrable_type', 'article')
                    //     ->groupBy('starrable_id')
                    //     ->selectRaw('count(id) as count, starrable_id')
                    //     ->get()
                    //     ->pluck('count', 'starrable_id');

                    return blogs::query()
                        ->published()
                        ->orderByDesc('published_at')
                        ->with(['user'])
                        ->get()
                        ->map(fn (blogs $blogs): array => [
                            ...$blogs->getDataArray(),
                            'stars_count' =>  0, //$stars[$article->getKey()] ?? 0,
                        ])
                        ->all();
                },
            ),
            'articlesCount' => blogs::query()
            ->where('is_published', true)
                ->count(),
            'authorsCount' => 100,
            'categories' => ArticleCategory::query()->orderBy('name')->get()->keyBy('slug'),
            'types' => collect([
                'article' => [
                    'slug' => 'article',
                    'name' => 'Article',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 28 28">
                                    <path fill="currentColor" d="M8 10.25a.75.75 0 0 1 .75-.75h10a.75.75 0 0 1 0 1.5h-10a.75.75 0 0 1-.75-.75Zm0 4.5a.75.75 0 0 1 .75-.75h10a.75.75 0 0 1 0 1.5h-10a.75.75 0 0 1-.75-.75Zm.75 3.75a.75.75 0 0 0 0 1.5h4.5a.75.75 0 0 0 0-1.5h-4.5ZM14 2a.75.75 0 0 1 .75.75V4h3.75V2.75a.75.75 0 0 1 1.5 0V4h.75A2.25 2.25 0 0 1 23 6.25v12.996a.75.75 0 0 1-.22.53l-5.504 5.504a.75.75 0 0 1-.53.22H6.75a2.25 2.25 0 0 1-2.25-2.25v-17A2.25 2.25 0 0 1 6.75 4H8V2.75a.75.75 0 0 1 1.5 0V4h3.75V2.75A.75.75 0 0 1 14 2ZM6 6.25v17c0 .414.336.75.75.75h9.246v-3.254a2.25 2.25 0 0 1 2.25-2.25H21.5V6.25a.75.75 0 0 0-.75-.75h-14a.75.75 0 0 0-.75.75Zm12.246 13.746a.75.75 0 0 0-.75.75v2.193l2.943-2.943h-2.193Z"></path>
                                </svg>
                            ',
                    'color' => 'amber',
                ],
                'news' => [
                    'slug' => 'news',
                    'name' => 'News',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><g fill="none" fill-rule="evenodd"><path d="M24 0v24H0V0h24ZM12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427c-.002-.01-.009-.017-.017-.018Zm.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093c.012.004.023 0 .029-.008l.004-.014l-.034-.614c-.003-.012-.01-.02-.02-.022Zm-.715.002a.023.023 0 0 0-.027.006l-.006.014l-.034.614c0 .012.007.02.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01l-.184-.092Z"></path><path fill="currentColor" d="M19 4.741V8a3 3 0 1 1 0 6v3c0 1.648-1.881 2.589-3.2 1.6l-2.06-1.546A8.658 8.658 0 0 0 10 15.446v2.844a2.71 2.71 0 0 1-5.316.744l-1.57-5.496a4.7 4.7 0 0 1 3.326-7.73l3.018-.168a9.344 9.344 0 0 0 4.19-1.259l2.344-1.368C17.326 2.236 19 3.197 19 4.741ZM5.634 15.078l.973 3.407A.71.71 0 0 0 8 18.29v-3.01l-1.56-.087a4.723 4.723 0 0 1-.806-.115ZM17 4.741L14.655 6.11A11.343 11.343 0 0 1 10 7.604v5.819c1.787.246 3.488.943 4.94 2.031L17 17V4.741ZM8 7.724l-1.45.08a2.7 2.7 0 0 0-.17 5.377l.17.015l1.45.08V7.724ZM19 10v2a1 1 0 0 0 .117-1.993L19 10Z">
                                 </path></g>
                             </svg>
                            ', // Thay thế bằng biểu tượng SVG thực tế
                    'color' => 'blue',
                ],
                'trick' => [
                    'slug' => 'trick',
                    'name' => 'Trick',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="m9 4l2.5 5.5L17 12l-5.5 2.5L9 20l-2.5-5.5L1 12l5.5-2.5L9 4m0 4.83L8 11l-2.17 1L8 13l1 2.17L10 13l2.17-1L10 11L9 8.83M19 9l-1.26-2.74L15 5l2.74-1.25L19 1l1.25 2.75L23 5l-2.75 1.26L19 9m0 14l-1.26-2.74L15 19l2.74-1.25L19 15l1.25 2.75L23 19l-2.75 1.26L19 23Z"></path>
                                </svg>
                            ',
                    'color' => 'violet',
                ],
            ]),

        ]);
    }
}
