<?php

namespace App\Http\Controllers\Material;

use App\Http\Controllers\Controller;
use App\Http\Requests\Material\BindRequest;
use App\Http\Requests\Material\SearchRequest;
use App\Http\Requests\Material\StoreRequest;
use App\Http\Requests\Material\StoreUnitRequest;
use App\Http\Requests\Material\StoreValueRequest;
use App\Http\Resources\Material\MaterialResource;
use App\Http\Services\Material\MaterialService;
use App\Models\Category;
use App\Models\Material;
use App\Models\MaterialUnit;
use App\Models\MaterialUnitValue;
use App\Models\MaterialUnitValueVariants;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

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
        $categories = Category::whereNull('parent_category_id')->with(['materials'])->get();
        $materials = Material::with(['material_units' => fn ($query) => $query->with('child_unit')])->orderBy('created_at', 'ASC')->get();
        $materials = MaterialResource::collection($materials)->resolve();
//        foreach ($materials as $material) {
//            $material['plain_units'] = $materialService::plainMaterialUnits($material['material_unit']);
//        }

        return inertia('Material/Index', [
            'categories' => $categories,
            'materials' => $materials,
            'can-materials' => [
                'list' => Auth('admin')->user()?->can('material list'),
                'create' => Auth('admin')->user()?->can('material create'),
                'edit' => Auth('admin')->user()?->can('material edit'),
                'delete' => Auth('admin')->user()?->can('material delete'),
            ]
        ]);
    }

    public function edit(Category $category)
    {
        $category->load(['materials']);
        $allMaterials = Material::all();
        return inertia('Material/Edit', [
            'category' => $category,
            'materials' => $allMaterials,
            'list' => Auth('admin')->user()?->can('material list'),
            'create' => Auth('admin')->user()?->can('material create'),
            'edit' => Auth('admin')->user()?->can('material edit'),
            'delete' => Auth('admin')->user()?->can('material delete'),
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function bind(BindRequest $request, Category $category)
    {
        $data = $request->validated();
        $materialIds = $data['materials'];
        // Валидация категории
        if (isset($category->parent_category_id)) throw ValidationException::withMessages(['Недопустимая категория']);
        $variants = MaterialService::productsVariants(MaterialService::products($category));

        // Валидация для цвета
        $materials = Material::whereIn('id', $materialIds)->get();
        $materialsWithColors = $materials->filter(fn ($value, $key) => $value->with_colors);
        if (count($materialsWithColors) > 1) {
            $materialsTitles = $materialsWithColors->map(fn ($material) => $material->title)->all();
            $materialsString = implode(", ", $materialsTitles);
            $message = 'Недопустимое количество материалов с цветами (максимально 1), указаны ' . $materialsString;
            throw ValidationException::withMessages([$message]);
        }
        try {
            DB::beginTransaction();
//            MaterialService::syncMaterialsToVariant($variants, $data['materials']);
            $childCategories = $category->child_categories;
            $categories = [$category, ...$childCategories];
            foreach ($categories as $cat) {
                $cat->materials()->sync($materialIds);
            }

            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            return Response::json(['errors' => [$error->getMessage()]], 500);
        }
        return $category->load('materials');
    }

    public function material(Material $material)
    {
//        $optionName->load('option_values');
//            'option_name' => $optionName,
        $material = MaterialResource::make($material)->resolve();
//        dd($material);
        return inertia('Material/Material', [
            'material' => $material,
            'can-materials' => [
                'list' => Auth('admin')->user()?->can('material list'),
                'create' => Auth('admin')->user()?->can('material create'),
                'edit' => Auth('admin')->user()?->can('material edit'),
                'delete' => Auth('admin')->user()?->can('material delete'),
            ]
        ]);
    }

    public function unit(MaterialUnit $unit)
    {
        $parentUnit = MaterialUnit::find($unit->parent_material_unit_id);
        $parentValues = $parentUnit?->values;
        return inertia('Material/Unit', [
            'material_unit' => $unit->load('values', 'material'),
            'parent_values' => $parentValues,
            'parent_unit' => $parentUnit,
            'can-materials' => [
                'list' => Auth('admin')->user()?->can('material list'),
                'create' => Auth('admin')->user()?->can('material create'),
                'edit' => Auth('admin')->user()?->can('material edit'),
                'delete' => Auth('admin')->user()?->can('material delete'),
            ]
        ]);
    }

    public function storeMaterial(StoreRequest $request)
    {
        $data = $request->validated();
        return Material::create($data);
    }

    /**
     * @throws ValidationException
     */
    public function storeUnit(StoreUnitRequest $request, Material $material)
    {
        $data = $request->validated();
        if ($material->with_colors) throw ValidationException::withMessages(['Невозможно создать новый элемент так как к материалу уже указаны цвета']);
        if (isset($data['parent_material_unit_id'])) {
            $parentUnit = MaterialUnit::find($data['parent_material_unit_id']);
            if (isset($parentUnit->child_unit))
                throw ValidationException::withMessages(['Нарушен порядок!']);
        } else {
            if ($material->main_material_unit)
                throw ValidationException::withMessages(['Нарушен порядок!']);
        }
        $titleCandidate = $material->material_units()->whereTitle($data['title'])->first();
        if ($titleCandidate)
            throw ValidationException::withMessages(['В материале ' . $material->title . ' уже существует элемент с названием ' . $data['title']]);

        return $material->material_units()->create($data);
    }

    /**
     * @throws ValidationException
     */
    public function storeValue(MaterialUnit $unit, StoreValueRequest $request)
    {
        $data = $request->validated();

        // Валидация названия значения в внутри элемента

        if (!isset($unit->parent_material_unit_id)) {
            $values = $unit->values;
            $repeatCandidate = $values->search(fn ($value, $key) => $value->value === $data['value']);
            if ($repeatCandidate)
                throw ValidationException::withMessages(['У данного элемента ' . $unit->title . ' уже существует такое значение!']);
            return $unit->values()->create(['value' => $data['value']]);
        }
        // Валидация для значений родительского элемента
        if (isset($data['parent_material_unit_value_id'])) {

            $candidate = $unit->values()->whereValue($data['value'])->whereParentMaterialUnitValueId($data['parent_material_unit_value_id'])->first();

            if (isset($candidate)) {
                $parentValue = MaterialUnitValue::find($data['parent_material_unit_value_id']);
                throw ValidationException::withMessages(['В указанном вами элементе ' . $unit->title . ' уже существует значение с таким названием для родительского значения ' . $parentValue->value . '!']);
            }

            $parentUnit = $unit->parent;
            $candidateValue = $parentUnit->values()->whereId($data['parent_material_unit_value_id'])->first();
            if (!isset($candidateValue))
                throw ValidationException::withMessages(['В родительском элементе нет значений которые вы указали - перезагрузите страницу!']);
        } else {
            throw ValidationException::withMessages(['Вы не указали родительское значение для создаваемого!']);
        }
        return $unit->values()->create([
            'parent_material_unit_value_id' => $data['parent_material_unit_value_id'],
            'value' => $data['value']
        ]);

        // Дополнительная проверка в рамках option_name
//        $candidate = $optionName->option_values()->whereTitle($data['title'])->first();
//        if ($candidate) throw ValidationException::withMessages(['Значение с таким названием уже существет у свойства ' . $optionName->title]);
//        return $optionName->option_values()->create(['title' => $data['title']]);

    }

    public function search(Material $material, SearchRequest $request, MaterialService $materialService)
    {
        $term = $request->validated()['term'];
        $sets = collect($materialService->getSets(collect([$material]))[0]['sets']);
        $sets = $sets->filter(function  ($value, $key) use ($term) {
            $title = mb_strtolower($value['title']);
            $lowerTerm = mb_strtolower($term);
            return str_contains($title, $lowerTerm);
        });

        return $materialService->paginate($sets, 10);
    }

    public function destroyUnit(MaterialUnit $unit)
    {
        $value = $unit->values()->first();

        $candidateVariant = MaterialUnitValueVariants::where('material_unit_value_id', $value->id)->first();
        if (isset($candidateVariant))
            throw ValidationException::withMessages(['Невозможно удлаить значение так как к нему прявязаны варианты, удалите варианты!']);

        $unit->delete();

        return 1111;
    }

    public function destroyValue(MaterialUnitValue $value)
    {
        $existsVariantWithThisValue = Variant::query()->whereRelation('material_unit_values', 'material_unit_values.id', '=', $value->id)->with('material_unit_values')->first();
        if ($existsVariantWithThisValue) {
            $title = $existsVariantWithThisValue->getTitleAttribute();
            return redirect()->back()->with('message', 'Невозможно удалить значение ' . $value->value . ', так как для него существуют варианты. Например ' . $title . '!');
        }
        // Валидация на наличие дочерних значений
        if ($value->child_values()->exists())
            return redirect()->back()->with('message', 'Невозможно удалить значение ' . $value->value . ', так как для него существуют дочерние!');

        $candidateVariant = MaterialUnitValueVariants::where('material_unit_value_id', $value->id)->first();
        if (isset($candidateVariant))
            throw ValidationException::withMessages(['Невозможно удлаить значение так как к нему прявязаны варианты, удалите варианты!']);
        $value->delete();
        return 1111;
    }

    public function destroy(Material $material)
    {
        $candidate = Product::query()->whereRelation('materials', 'material_id', '=', $material->id)->first();
        if ($candidate)
            throw ValidationException::withMessages(['Невозможно удалить так как есть товар - ' . $candidate->title . ', у которого указан данный материал. ']);
        $material->delete();
        return redirect('/admin/materials')
            ->with('message', 'Материал ' . $material->title . ' удалено успешно!');
    }
}
