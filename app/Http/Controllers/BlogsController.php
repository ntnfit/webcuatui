<?php

namespace App\Http\Controllers;

use App\Models\blogs;
use Illuminate\Http\Request;
use Inertia\Inertia;
class BlogsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Blogs::with(['user', 'categories', 'tags'])
            ->published();
    
        // Filter theo type
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
    
        // Filter theo category (hỗ trợ nhiều category)
        if ($request->filled('category')) {
            $categories = explode(',', $request->input('category'));
            if (!empty($categories)) {
                $query->whereHas('categories', function ($q) use ($categories) {
                    $q->whereIn('slug', $categories);
                });
            }
        }
    
        // Tìm kiếm tiêu đề hoặc sub_title
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('sub_title', 'like', "%$search%");
            });
        }
    
        // Phân trang
        $blogs = $query->latest()->paginate(6)->withQueryString();
    
        // Định dạng lại dữ liệu bằng getDataArray()
        $posts = $blogs->through(fn($post) => $post->getDataArray())->toArray();
        
        // Lấy danh sách categories từ database
        $categories = \App\Models\Category::pluck('name')->toArray();
        
        // Thêm "Tất cả" vào đầu danh sách nếu chưa có
        if (!in_array('Tất cả', $categories)) {
            array_unshift($categories, 'Tất cả');
        }
    
        return Inertia::render('Blogs', [
            'posts' => $posts['data'], // lấy mảng các bài viết
            'pagination' => [
                'currentPage' => $blogs->currentPage(),
                'lastPage' => $blogs->lastPage(),
                'perPage' => $blogs->perPage(),
                'total' => $blogs->total(),
            ],
            'filters' => [
                'search' => $request->input('search'),
                'type' => $request->input('type'),
                'category' => $request->input('category'),
            ],
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        try {
            // Lấy bài viết chính theo slug
            $blog = Blogs::with(['categories', 'tags', 'user'])
                        ->where('slug', $slug)
                        ->firstOrFail();
            // Tăng lượt xem
            $blog->increment('view');
            
            // Lấy 5 bài viết mới nhất khác
            $latestBlogs = Blogs::where('id', '!=', $blog->id)
                            ->latest()
                            ->take(5)
                            ->get();
            
            // Lấy các bài viết cùng chuyên mục
            $relatedBlogs = Blogs::whereHas('categories', function($query) use ($blog) {
                            $query->whereIn('categories.id', $blog->categories->pluck('id'));
                        })
                        ->where('id', '!=', $blog->id)
                        ->inRandomOrder()
                        ->take(3)
                        ->get();
            
            // Lấy bài viết trước và sau theo thứ tự created_at thay vì id
            $previousBlog = Blogs::where('created_at', '<', $blog->created_at)
                               ->orderBy('created_at', 'desc')
                               ->first();
            $nextBlog = Blogs::where('created_at', '>', $blog->created_at)
                           ->orderBy('created_at', 'asc')
                           ->first();
            
            return Inertia::render('BlogDetail', [
                'blog' => [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'slug' => $blog->slug,
                    'body' => $this->adjustInlineStylesForDarkMode($blog->body),
                    'excerpt' => $blog->excerpt,
                    'featured_image' => $blog->featured_image,
                    'thumbnail_url' => asset('storage/' . $blog->cover_photo_path),
                    'categories' => $blog->categories->pluck('name'),
                    'tags' => $blog->tags->pluck('name'),
                    'type' => $blog->type,
                    'author' => [
                        'name' => $blog->user->name,
                        'avatar' => $blog->user->avatar ?? 'https://github.com/shadcn.png',
                        'bio' => $blog->user->bio ?? null,
                    ],
                    'date' => $blog->created_at->format('d/m/Y'),
                    'reading_time' => $this->calculateReadingTime($blog->body),
                    'stars' => $blog->stars ?? 0,
                    'views' => $blog->view ?? 0,
                ],
                'latestBlogs' => $latestBlogs->map(function ($blog) {
                    return [
                        'id' => $blog->id,
                        'slug' => $blog->slug,
                        'title' => $blog->title,
                        'excerpt' => $blog->excerpt,
                        'thumbnail_url' => $blog->thumbnail_url,
                        'date' => $blog->created_at->format('d/m/Y'),
                    ];
                }),
                'relatedBlogs' => $relatedBlogs->map(function ($blog) {
                    return [
                        'id' => $blog->id,
                        'slug' => $blog->slug,
                        'title' => $blog->title,
                        'excerpt' => $blog->excerpt,
                        'thumbnail_url' => $blog->thumbnail_url,
                        'date' => $blog->created_at->format('d/m/Y'),
                    ];
                }),
                'navigation' => [
                    'previous' => $previousBlog ? [
                        'id' => $previousBlog->id,
                        'slug' => $previousBlog->slug,
                        'title' => $previousBlog->title,
                    ] : null,
                    'next' => $nextBlog ? [
                        'id' => $nextBlog->id,
                        'slug' => $nextBlog->slug, 
                        'title' => $nextBlog->title,
                    ] : null,
                ],
            ]);
        } catch (\Exception $e) {
            // Xử lý lỗi và trả về trang lỗi
            return Inertia::render('BlogDetail', [
                'error' => 'Không thể tìm thấy bài viết này. ' . $e->getMessage()
            ]);
        }
    }
    function adjustInlineStylesForDarkMode(string $html): string
    {
        // Chuyển màu đen sang trắng nếu có
        $html = preg_replace('/color:\\s*#000000/i', 'color: #ffffff', $html);

        // Tuỳ chọn: chuyển một số màu khác để rõ hơn trong dark mode
        $html = preg_replace('/color:\\s*#222222/i', 'color: #dddddd', $html);

        // Tuỳ chọn: xử lý background nếu cần
        $html = preg_replace('/background-color:\\s*#ffffff/i', 'background-color: #1e1e1e', $html);

        return $html;
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(blogs $blogs)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, blogs $blogs)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(blogs $blogs)
    {
        //
    }

    // Phương thức tính thời gian đọc
    private function calculateReadingTime($content)
    {
        // Tính số từ (trung bình 200 từ/phút)
        $wordCount = str_word_count(strip_tags($content));
        $minutes = ceil($wordCount / 200);
        
        return $minutes;
    }
}
