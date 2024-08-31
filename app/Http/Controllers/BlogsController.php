<?php

namespace App\Http\Controllers;

use App\Models\blogs;
use Illuminate\Http\Request;

class BlogsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(blogs $blogs)
    {
//        SEOMeta::setTitle($post->seoDetail?->title);
//
//        SEOMeta::setDescription($post->seoDetail?->description);
//
//        SEOMeta::setKeywords($post->seoDetail->keywords ?? []);
//
       $shareButton = null;
//        $blogs->load(['user', 'categories', 'tags', 'comments' => fn ($query) => $query->approved(), 'comments.user']);
        $blogs->load(['user', 'categories', 'tags']);
        return view('blogdetail', [
            'post' => $blogs,
            'shareButton' => $shareButton,
        ]);
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
}
