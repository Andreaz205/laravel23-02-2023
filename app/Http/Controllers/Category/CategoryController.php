<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\BindRequest;
use App\Http\Requests\Category\CategoryStoreRequest;
use App\Http\Requests\Category\StoreCategoryImageRequest;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use App\Models\CategoryProducts;
use App\Models\CategoryVariants;
use App\Models\PopularCategories;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:category list', ['only' => ['index', 'show']]);
        $this->middleware('can:category create', ['only' => ['create', 'store']]);
        $this->middleware('can:category edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:category delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $categories = Category::all();
        return inertia('Category/Index', [
            'categoriesData' => CategoryResource::collection($categories),
            'can-categories' => [
                'list' => Auth('admin')->user()?->can('category list'),
                'create' => Auth('admin')->user()?->can('category create'),
                'edit' => Auth('admin')->user()?->can('category edit'),
                'delete' => Auth('admin')->user()?->can('category delete'),
            ]
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function store(CategoryStoreRequest $request)
    {
        $data = $request->validated();
        $name = $data['name'];
        $categoryId = $data['category_id'];

        if (isset($categoryId)) {
            $parentCategory = Category::find($categoryId);
            if (isset($parentCategory->parent_category_id)) throw ValidationException::withMessages(['Недопустимая вложенность!']);
            $category = Category::create([
                'name' => $name,
                'parent_category_id' => $categoryId
            ]);
        } else {
            $category = Category::create([
                'name' => $name,
            ]);
        }
        return new CategoryResource($category);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return Response::json(['status' => 'success']);
    }

    public function appendCategory(Product $product, BindRequest $request)
    {
        $data = $request->validated();
        $categoryId = $data['category_id'];
        $variants = $product->variants;
        $category = Category::find($categoryId);
        $categoryOptionNames = $category->option_names;
        try {
            DB::beginTransaction();
//            foreach ($variants as $variant) {
//                $variant->option_values()->where('option_name_id');
//            }
            $product->update(['category_id' => $categoryId]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::json(['message' => $e->getMessage()], 500);
        }

        return 1111;
//        return redirect('/admin/products/'. $product->id)
//            ->with('message', 'Категория ' . $category->name . ' была добавлена к товару ' . $product->title);
//        $categoryProducts = $category->products()->whereNull('category_products.deleted_at')->get();
//        if (isset($categoryProducts) && count($categoryProducts) > 0) {
//            foreach ($categoryProducts as $categoryProduct) {
//                if ($categoryProduct->id === $product->id) {
//                    CategoryProducts::where('product_id', $product->id)->where('category_id', $category->id)->delete();
//                    return Response::json(['status' => 'Deleted successfully!']);
//                }
//            }
//        }
//        CategoryProducts::create([
//            'product_id' => $product->id,
//            'category_id' => $category->id,
//        ]);
//        return Response::json(['status' => 'success']);

    }

    public function clearCategory(Product $product)
    {
        $category = $product->category()->first();
        $name = $category->name;
        $product->update(['category_id' => null]);
        return 11;
    }






    public function bind(Product $product, Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required', 'integer']
        ]);
        $categoryId = $data['category_id'];

        $categoryProductItemForCheck = CategoryProducts::firstOrCreate([
            'product_id' => $product->id,
            'category_id' => $categoryId
        ]);

        return response()->json([$categoryProductItemForCheck]);
    }

    public function bindVariant(Variant $variant, Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required', 'integer']
        ]);
        $categoryId = $data['category_id'];

        $categoryProductItemForCheck = CategoryVariants::firstOrCreate([
            'variant_id' => $variant->id,
            'category_id' => $categoryId
        ]);

        return response()->json([$categoryProductItemForCheck]);
    }



    public function destroyBind(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id' => ['required', 'integer', 'exists:category_products,category_id']
        ]);
        $categoryId = $data['category_id'];

//        dd($categoryId, $product->id);
        $item = CategoryProducts::where('product_id', $product->id)->where('category_id', $categoryId)->firstOrFail();
        $item->delete();
        return response('deleted successfully');
    }

    public function destroyBindVariant(Request $request, Variant $variant)
    {
        $data = $request->validate([
            'category_id' => ['required', 'integer', 'exists:category_variants,variant_id']
        ]);
        $categoryId = $data['category_id'];

        $item = CategoryVariants::where('variant_id', $variant->id)->where('category_id', $categoryId)->firstOrFail();
        $item->delete();

        return response('deleted successfully');
    }

    public function edit(Category $category)
    {
        $popularCategories = PopularCategories::all();
        if (isset($popularCategories)) {
            foreach ($popularCategories as $popularCategory) {
                if ($popularCategory->category_id == $category->id) {
                    $category->is_popular = true;
                    break;
                }
            }
        }

        return view('category.edit', compact('category'));
    }

    public function togglePopular(Category $category) {
        $popularCategory = PopularCategories::where('category_id', $category->id)->first();
        if (isset($popularCategory)) {
            $popularCategory->delete();
            return response()->json(['message' => 'toggle delete success']);
        } else {
            PopularCategories::create(['category_id' => $category->id]);
            return response()->json(['message' => 'toggle create success']);
        }
    }

    public function publish(Category $category, Request $request)
    {
        $data = $request->validate([
            'is_published' => 'required|boolean'
        ]);
        $category->update(['is_published' => $data['is_published']]);
        return view('category.edit', compact('category'));
    }

    public function storeImage(Category $category, StoreCategoryImageRequest $request) {
        $data = $request->validated();
        $image = $data['image'];
        unset($data['images']);

        $name = md5(Carbon::now() . '_' . $image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();

        $filePath = Storage::disk('public')->putFileAs('images', $image, $name);

        $category->update([
            'image_path' => $filePath,
            'image_url' => url('/storage/' . $filePath),
        ]);

        return response()->json($category);
    }

    public function deleteImage(Category $category) {
        $imagePath = $category->image_path;
        $category->update(['image_url' => null, 'image_path' => null]);
        Storage::disk('public')->delete($imagePath);

        return response()->json(['status' => 'success']);
    }

    public function storeSketch(Category $category, StoreCategoryImageRequest $request)
    {
        $data = $request->validated();
        $image = $data['image'];
        unset($data['images']);

        $name = 'sketch_'.md5( Carbon::now() . '_' . $image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();

        $filePath = Storage::disk('public')->putFileAs('images', $image, $name);

        $category->update([
            'sketch_path' => $filePath,
            'sketch_url' => url('/storage/' . $filePath),
        ]);

        return response()->json($category);
    }

    public function deleteSketch(Category $category)
    {
        $imagePath = $category->sketch_path;
        $category->update(['sketch_url' => null, 'sketch_path' => null]);
        Storage::disk('public')->delete($imagePath);

        return response()->json(['status' => 'success']);
    }

    protected function loopDelete($categories)
    {
        foreach($categories as $category) {
            if(isset($category->child_categories)) {
                $this->loopDelete($category->child_categories);
            } else {
                $category->delete();
            }
        }
    }
}
