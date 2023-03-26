<?php

namespace App\Http\Controllers\Material;

use App\Http\Controllers\Controller;
use App\Http\Requests\Material\Color\AddRequest;
use App\Http\Resources\Material\MaterialResource;
use App\Http\Services\Image\UploadImageService;
use App\Http\Services\Material\MaterialService;
use App\Models\Category;
use App\Models\Color;
use App\Models\Material;
use App\Models\MaterialUnitValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
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
        $sets = $materialService->getSets(
            collect(
                [
                    $material->load(
                        [
                            'material_units' => fn ($query) => $query->with(['values' => fn ($query) => $query->with('color')])
                        ]
                    )
                ]
            )
        );
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

    public function addColor(AddRequest $request, Material $material, UploadImageService $uploadImageService)
    {
        $data = $request->validated();
        $materialUnitValue = MaterialUnitValue::query()
            ->where('id', $data['material_unit_value_id'])
            ->with('material_unit')
            ->first();


        //Валидация
        $lastUnit = $material->material_units()->doesntHave('child_unit')->first();
        if ($lastUnit->id != $materialUnitValue->material_unit->id)
            throw ValidationException::withMessages(['Недопустимое значение для цвета (элемент должен быть последним)']);


        $data = $uploadImageService->upload($data['image']);
        try {
            DB::beginTransaction();
            if ($materialUnitValue->color()->exists()) $materialUnitValue->color()->delete();
            $color = $materialUnitValue->color()->create([
                'image_url' => $data['url'],
                'image_path' => $data['path'],
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return Response::json(['errors' => [$exception->getMessage()]], 422);
        }
        return $color;
    }
}
