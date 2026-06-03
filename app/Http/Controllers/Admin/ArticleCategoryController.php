<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ArticleCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ArticleCategory::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $categories = $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(function (ArticleCategory $category) {
                $category->articles_count = $category->articlesCount();

                return $category;
            });

        return view('admin.article-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.article-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:article_categories,name',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        ArticleCategory::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.article-categories.index')
            ->with('success', 'Kategori artikel berjaya ditambah.');
    }

    public function edit(ArticleCategory $articleCategory)
    {
        return view('admin.article-categories.edit', compact('articleCategory'));
    }

    public function update(Request $request, ArticleCategory $articleCategory)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('article_categories', 'name')->ignore($articleCategory->id),
            ],
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $oldName = $articleCategory->name;

        $articleCategory->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($oldName !== $articleCategory->name) {
            Article::where('category', $oldName)->update(['category' => $articleCategory->name]);
        }

        return redirect()
            ->route('admin.article-categories.index')
            ->with('success', 'Kategori artikel berjaya dikemas kini.');
    }

    public function destroy(ArticleCategory $articleCategory)
    {
        if ($articleCategory->articlesCount() > 0) {
            return redirect()
                ->route('admin.article-categories.index')
                ->with('error', 'Kategori ini masih digunakan oleh artikel. Sila tukar kategori artikel tersebut terlebih dahulu.');
        }

        $articleCategory->delete();

        return redirect()
            ->route('admin.article-categories.index')
            ->with('success', 'Kategori artikel berjaya dipadam.');
    }
}
