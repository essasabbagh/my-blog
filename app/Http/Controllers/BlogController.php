<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');

        $posts = Post::query()
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%');
            })
            ->when($category, function ($query, $category) {
                return $query->whereHas('category', function ($query) use ($category) {
                    $query->where('slug', $category);
                });
            })
            ->latest()
            ->paginate(10);

        $categories = Category::all();

        return view(
            'blog.index',
            compact(
                'posts',
                'categories',
                'search',
                'category'
            )
        );
    }
}