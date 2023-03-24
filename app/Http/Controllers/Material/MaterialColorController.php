<?php

namespace App\Http\Controllers\Material;

use App\Http\Controllers\Controller;
use App\Http\Resources\Material\MaterialResource;
use App\Http\Services\Material\MaterialService;
use App\Models\Category;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MaterialColorController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:material list', ['only' => ['index', 'show']]);
        $this->middleware('can:material create', ['only' => ['create', 'store']]);
        $this->middleware('can:material edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:material delete', ['only' => ['destroy']]);
    }

    public function index(Material $material, MaterialService $materialService)
    {
        $sets = $materialService->getSets(collect([$material]));
        $sets = $materialService->paginate($sets[0]['sets'], 10);

        return inertia('Material/Colors/Index', [
            'material' => $material,
            'paginated_material_sets' => $sets,
            'can-materials' => [
                'list' => Auth('admin')->user()?->can('material list'),
                'create' => Auth('admin')->user()?->can('material create'),
                'edit' => Auth('admin')->user()?->can('material edit'),
                'delete' => Auth('admin')->user()?->can('material delete'),
            ]
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function toggleColor(Material $material, MaterialService $materialService)
    {
        if ($material->with_colors) {
            $material->update(['with_colors' => false]);
            return redirect('/admin/materials')->with('message', 'Для материала ' . $material->title . ' успешно удалены цвета!');
        } else {
            $materialService->validateToggleColor($material);
            $material->update(['with_colors' => true]);
            return redirect('/admin/materials')->with('message', 'Для материала ' . $material->title . ' успешно добавлены цвета!');
        }
    }
}
