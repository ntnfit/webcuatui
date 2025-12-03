<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBlogRequest;
use App\Http\Requests\Api\UpdateBlogRequest;
use App\Http\Resources\BlogCollection;
use App\Http\Resources\BlogResource;
use App\Models\blogs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogApiController extends Controller
{
    /**
     * Display a listing of published blogs.
     */
    public function index(Request $request): BlogCollection
    {
        $query = blogs::with(['categories', 'user'])
            ->where('status', 'published')
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%")
                  ->orWhere('sub_title', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        // Filter by date
        if ($request->has('year')) {
            $query->whereYear('created_at', $request->input('year'));
        }

        if ($request->has('month')) {
            $query->whereMonth('created_at', $request->input('month'));
        }

        $perPage = $request->input('per_page', 15);
        $blogs = $query->paginate($perPage);

        return new BlogCollection($blogs);
    }

    /**
     * Display the specified blog by slug.
     */
    public function show(string $slug): BlogResource|JsonResponse
    {
        $blog = blogs::with(['categories', 'user'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (!$blog) {
            return response()->json([
                'message' => 'Blog not found'
            ], 404);
        }

        return new BlogResource($blog);
    }

    /**
     * Store a newly created blog.
     */
    public function store(StoreBlogRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        // Map API fields to Model fields
        $data = [
            'title' => $validated['title'],
            'body' => $validated['content'],
            'sub_title' => $validated['excerpt'] ?? Str::limit(strip_tags($validated['content']), 200),
            'status' => $validated['status'] ?? 'draft',
            'published_at' => $validated['published_at'] ?? now(),
            'user_id' => 1, // Default admin
            'photo_alt_text' => $validated['title'], // Default alt text
        ];

        // Generate slug from title
        $data['slug'] = Str::slug($data['title']);
        
        // Ensure unique slug
        $originalSlug = $data['slug'];
        $count = 1;
        while (blogs::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $count;
            $count++;
        }

        // Handle image upload (File or Base64)
        if ($request->has('image')) {
            $image = $request->input('image');
            
            // Check if it's a Base64 string
            if (is_string($image) && preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
                $image = substr($image, strpos($image, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif
                
                if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png', 'webp'])) {
                    return response()->json(['message' => 'Invalid image type'], 422);
                }

                $image = str_replace(' ', '+', $image);
                $imageName = 'blog-feature-images/' . Str::random(40) . '.' . $type;
                
                Storage::disk('public')->put($imageName, base64_decode($image));
                $data['cover_photo_path'] = $imageName;
            } 
            // Check if it's a file upload
            elseif ($request->hasFile('image')) {
                $data['cover_photo_path'] = $request->file('image')->store('blog-feature-images', 'public');
            }
        }

        $blog = blogs::create($data);

        // Attach category
        if (isset($validated['category_id'])) {
            $blog->categories()->attach($validated['category_id']);
        }

        return response()->json([
            'message' => 'Blog created successfully',
            'data' => new BlogResource($blog->load(['categories', 'user']))
        ], 201);
    }

    /**
     * Update the specified blog.
     */
    public function update(UpdateBlogRequest $request, int $id): JsonResponse
    {
        $blog = blogs::find($id);

        if (!$blog) {
            return response()->json([
                'message' => 'Blog not found'
            ], 404);
        }

        // Check if user owns the blog
        if ($blog->user_id !== auth()->id() && auth()->id() !== 1) {
            return response()->json([
                'message' => 'Unauthorized to update this blog'
            ], 403);
        }

        $validated = $request->validated();
        $data = [];

        if (isset($validated['title'])) $data['title'] = $validated['title'];
        if (isset($validated['content'])) $data['body'] = $validated['content'];
        if (isset($validated['excerpt'])) $data['sub_title'] = $validated['excerpt'];
        if (isset($validated['status'])) $data['status'] = $validated['status'];
        if (isset($validated['published_at'])) $data['published_at'] = $validated['published_at'];

        // Update slug if title changed
        if (isset($data['title']) && $data['title'] !== $blog->title) {
            $data['slug'] = Str::slug($data['title']);
            
            // Ensure unique slug
            $originalSlug = $data['slug'];
            $count = 1;
            while (blogs::where('slug', $data['slug'])->where('id', '!=', $id)->exists()) {
                $data['slug'] = $originalSlug . '-' . $count;
                $count++;
            }
        }

        // Handle image upload (File or Base64)
        if ($request->has('image')) {
            $image = $request->input('image');
            
            // Check if it's a Base64 string
            if (is_string($image) && preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
                // Delete old image
                if ($blog->cover_photo_path) {
                    Storage::disk('public')->delete($blog->cover_photo_path);
                }

                $image = substr($image, strpos($image, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif
                
                if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png', 'webp'])) {
                    return response()->json(['message' => 'Invalid image type'], 422);
                }

                $image = str_replace(' ', '+', $image);
                $imageName = 'blog-feature-images/' . Str::random(40) . '.' . $type;
                
                Storage::disk('public')->put($imageName, base64_decode($image));
                $data['cover_photo_path'] = $imageName;
            } 
            // Check if it's a file upload
            elseif ($request->hasFile('image')) {
                // Delete old image
                if ($blog->cover_photo_path) {
                    Storage::disk('public')->delete($blog->cover_photo_path);
                }
                $data['cover_photo_path'] = $request->file('image')->store('blog-feature-images', 'public');
            }
        }

        // Update excerpt if content changed and excerpt not provided
        if (isset($data['body']) && !isset($data['sub_title'])) {
            $data['sub_title'] = Str::limit(strip_tags($data['body']), 200);
        }

        $blog->update($data);

        // Sync categories
        if (isset($validated['category_id'])) {
            $blog->categories()->sync([$validated['category_id']]);
        }

        return response()->json([
            'message' => 'Blog updated successfully',
            'data' => new BlogResource($blog->load(['categories', 'user']))
        ]);
    }

    /**
     * Remove the specified blog.
     */
    public function destroy(int $id): JsonResponse
    {
        $blog = blogs::find($id);

        if (!$blog) {
            return response()->json([
                'message' => 'Blog not found'
            ], 404);
        }

        // Check if user owns the blog
        if ($blog->user_id !== auth()->id() && auth()->id() !== 1) {
            return response()->json([
                'message' => 'Unauthorized to delete this blog'
            ], 403);
        }

        // Delete image if exists
        if ($blog->cover_photo_path) {
            Storage::disk('public')->delete($blog->cover_photo_path);
        }

        $blog->categories()->detach();
        $blog->delete();

        return response()->json([
            'message' => 'Blog deleted successfully'
        ], 200);
    }
}
