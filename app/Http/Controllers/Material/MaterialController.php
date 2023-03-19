<?php

namespace App\Http\Controllers\Material;

use App\Http\Controllers\Controller;
use App\Http\Services\Group\GroupService;
use App\Models\Category;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:material list', ['only' => ['index', 'show']]);
        $this->middleware('can:material create', ['only' => ['create', 'store']]);
        $this->middleware('can:material edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:material delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $categories = Category::query()
            ->whereNull('parent_category_id')->get();
        return inertia('Material/Index', [
            'categories' => $categories,
            'list' => Auth('admin')->user()?->can('material list'),
            'create' => Auth('admin')->user()?->can('material create'),
            'edit' => Auth('admin')->user()?->can('material edit'),
            'delete' => Auth('admin')->user()?->can('material delete'),
        ]);
    }

    public function edit(Category $category)
    {
        return inertia('Material/Edit', [
           'category' => $category,
            'list' => Auth('admin')->user()?->can('material list'),
            'create' => Auth('admin')->user()?->can('material create'),
            'edit' => Auth('admin')->user()?->can('material edit'),
            'delete' => Auth('admin')->user()?->can('material delete'),
        ]);
    }
}
