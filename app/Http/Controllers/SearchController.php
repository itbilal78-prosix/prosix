<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Blog;
use App\Models\CustomizerModel;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    private function resolveImage(?string $path, string $type = 'storage'): ?string
    {
        if (!$path) return null;
        if (str_starts_with($path, 'http')) return $path;
        if (str_starts_with($path, '/storage/') || str_starts_with($path, 'storage/')) {
            return asset($path);
        }
        if (str_starts_with($path, '/uploads/') || str_starts_with($path, 'uploads/')) {
            return asset($path);
        }

        // Model images — public/uploads/models/
        if ($type === 'model') {
            return asset('uploads/models/' . $path);
        }

        // Default — storage
        return asset('storage/' . $path);
    }

    public function search(Request $request)
    {
        $query = trim($request->get('q', ''));
        $limit = min((int) $request->get('limit', 8), 20);

        if (strlen($query) < 2) {
            return response()->json([
                'query'      => $query,
                'products'   => [],
                'categories' => [],
                'blogs'      => [],
                'models'     => [],
                'total'      => 0,
            ]);
        }

        $words = array_filter(explode(' ', $query), fn($w) => strlen($w) >= 1);

        // ── Products ──────────────────────────────────────────
        $products = Product::where(function ($q) use ($query, $words) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%");
                foreach ($words as $word) {
                    $q->orWhere('name', 'LIKE', "%{$word}%")
                      ->orWhere('description', 'LIKE', "%{$word}%");
                }
            })
            ->select('id', 'name', 'price', 'image')
            ->limit($limit)
            ->get()
            ->unique('id')
            ->map(function ($p) {
                return [
                    'id'    => $p->id,
                    'type'  => 'product',
                    'name'  => $p->name,
                    'price' => $p->price,
                    'image' => $this->resolveImage($p->image, 'storage'),
                    'url'   => '/product/' . $p->id,
                ];
            })
            ->values();

        // ── Categories ────────────────────────────────────────
        $categories = Category::where(function ($q) use ($query, $words) {
                $q->where('name', 'LIKE', "%{$query}%");
                foreach ($words as $word) {
                    $q->orWhere('name', 'LIKE', "%{$word}%");
                }
            })
            ->select('id', 'name', 'icon_image')
            ->limit(4)
            ->get()
            ->unique('id')
            ->map(function ($c) {
                return [
                    'id'    => $c->id,
                    'type'  => 'category',
                    'name'  => $c->name,
                    'image' => $this->resolveImage($c->icon_image, 'storage'),
                    'url'   => '/category/' . $c->id . '/products',
                ];
            })
            ->values();

        // ── Blogs ─────────────────────────────────────────────
        $blogs = Blog::where(function ($q) use ($query, $words) {
                $q->where('title', 'LIKE', "%{$query}%");
                foreach ($words as $word) {
                    $q->orWhere('title', 'LIKE', "%{$word}%");
                }
            })
            ->select('id', 'title', 'image', 'slug')
            ->limit(3)
            ->get()
            ->unique('id')
            ->map(function ($b) {
                return [
                    'id'    => $b->id,
                    'type'  => 'blog',
                    'name'  => $b->title,
                    'image' => $this->resolveImage($b->image, 'storage'),
                    'url'   => '/blog/' . ($b->slug ?? $b->id),
                ];
            })
            ->values();

        // ── Models (CustomizerModel) ───────────────────────────
        $models = CustomizerModel::where(function ($q) use ($query, $words) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhere('model_name', 'LIKE', "%{$query}%");
                foreach ($words as $word) {
                    $q->orWhere('title', 'LIKE', "%{$word}%")
                      ->orWhere('description', 'LIKE', "%{$word}%")
                      ->orWhere('model_name', 'LIKE', "%{$word}%");
                }
            })
            ->select('id', 'title', 'price', 'thumbnail', 'front_white', 'front_black')
            ->limit(4)
            ->get()
            ->unique('id')
            ->map(function ($m) {
                // Priority: thumbnail > front_white > front_black
                $raw = $m->thumbnail ?? $m->front_white ?? $m->front_black ?? null;
                return [
                    'id'    => $m->id,
                    'type'  => 'model',
                    'name'  => $m->title,
                    'price' => $m->price ?? '0.00',
                    'image' => $this->resolveImage($raw, 'model'),
                    'url'   => '/product/' . $m->id,
                ];
            })
            ->values();

        $total = $products->count() + $categories->count() + $blogs->count() + $models->count();

        return response()->json([
            'query'      => $query,
            'products'   => $products,
            'categories' => $categories,
            'blogs'      => $blogs,
            'models'     => $models,
            'total'      => $total,
        ]);
    }
}
