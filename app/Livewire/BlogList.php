<?php

namespace App\Livewire;

use App\Models\blogs as Blogs;
use App\Models\Category;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class BlogList extends Component
{
    use WithPagination;

    #[Url(as: 'search', history: true)]
    public string $search = '';

    #[Url(as: 'type', history: true)]
    public string $type = '';

    #[Url(as: 'category', history: true)]
    public string $category = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function setType(string $value): void
    {
        $this->type = $this->type === $value ? '' : $value;
        $this->resetPage();
    }

    public function toggleCategory(string $slug): void
    {
        $cats = array_values(array_filter(explode(',', $this->category)));
        $idx  = array_search($slug, $cats);

        if ($idx !== false) {
            array_splice($cats, $idx, 1);
        } else {
            $cats[] = $slug;
        }

        $this->category = implode(',', $cats);
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->type     = '';
        $this->category = '';
        $this->search   = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = Blogs::with(['user', 'categories', 'tags'])->published();

        if ($this->type) {
            $query->where('type', $this->type);
        }

        if ($this->category) {
            $cats = array_values(array_filter(explode(',', $this->category)));
            if ($cats) {
                $query->whereHas('categories', fn ($q) => $q->whereIn('slug', $cats));
            }
        }

        if ($this->search) {
            $s = $this->search;
            $query->where(fn ($q) => $q->where('title', 'like', "%{$s}%")
                ->orWhere('sub_title', 'like', "%{$s}%"));
        }

        $posts = $query->latest()->paginate(9)->through(fn ($p) => $p->getDataArray());

        $categories = Category::pluck('name')->toArray();
        if (! in_array('Tất cả', $categories)) {
            array_unshift($categories, 'Tất cả');
        }

        return view('livewire.blog-list', compact('posts', 'categories'));
    }
}
