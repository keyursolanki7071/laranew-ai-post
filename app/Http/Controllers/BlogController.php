<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::latest()->get();

        return Inertia::render('Blogs/Index', [
            'blogs' => $blogs
        ]);
    }

    public function show(Blog $blog)
    {
        return Inertia::render('Blogs/Show', [
            'blog' => $blog
        ]);
    }

    public function fetch()
    {
        \App\Jobs\FetchBlogs::dispatch();
        
        return back();
    }

    public function proxyImage(Request $request)
    {
        $url = $request->query('url');

        if (!$url) {
            abort(400, 'URL is required');
        }

        $response = \Illuminate\Support\Facades\Http::get($url);

        if ($response->failed()) {
            abort(404, 'Image not found');
        }

        return response($response->body())
            ->header('Content-Type', $response->header('Content-Type'));
    }
}
