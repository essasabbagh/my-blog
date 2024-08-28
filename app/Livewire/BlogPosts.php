<?php

namespace App\Http\Livewire;

use App\Models\Post;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class BlogPosts extends Component
{
    use WithPagination;

    public $search;
    public $category;

    protected $updatesQueryString = ['search', 'category'];

    public function render()
    {
        // Fetch categories for the sidebar
        $categories = Category::all();

        // Fetch posts based on search and category filters
        $posts = Post::query()
            ->when($this->search, function ($query) {
                return $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('content', 'like', '%' . $this->search . '%');
            })
            ->when($this->category, function ($query) {
                return $query->whereHas('category', function ($query) {
                    $query->where('slug', $this->category);
                });
            })
            ->latest()
            ->paginate(10);

        // Pass posts and categories to the view
        return view('livewire.blog-posts', [
            'posts' => $posts,
            'categories' => $categories,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }
}