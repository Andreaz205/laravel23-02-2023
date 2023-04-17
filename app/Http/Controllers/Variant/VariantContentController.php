<?php

namespace App\Http\Controllers\Variant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Variant\Content\AppendImageRequest;
use App\Http\Requests\Variant\Content\AppendItemRequest;
use App\Http\Requests\Variant\Content\AppendTextItemRequest;
use App\Http\Requests\Variant\Content\StoreRequest;
use App\Http\Services\Image\UploadImageService;
use App\Models\Material;
use App\Models\MaterialUnitValue;
use App\Models\Product;
use App\Models\VariantContent;
use App\Models\VariantContentItem;
use App\Models\VariantContentTextItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\FlareClient\Http\Response;

class VariantContentController extends Controller
{
    public function index()
    {
        $contentItems = VariantContent::query()->latest()->get();

        return inertia('Material/Content/Index', [
            'contentItems' => $contentItems
        ]);
    }

    public function create()
    {
        $materials = Material::query()
            ->with(['material_units' => fn ($query) => $query->whereNull('parent_material_unit_id')->with('values')])
            ->get()
            ->map(function ($item) {
                return [
                    'material' => $item->title,
                    'material_unit_values' => $item->material_units[0]->values
                ];
            });

        return inertia('Material/Content/Create', [
            'materials' => $materials
        ]);
    }

    public function store( StoreRequest $request)
    {
        $data = $request->validated();
        $valuesIds = $data['material_unit_value_ids'];
        unset($data['material_unit_value_ids']);
        $requestMaterialUnitValues = MaterialUnitValue::whereIn('id', $valuesIds)->get();

        $availableMaterials = Material::query()->with('material_units')->get();
        $availableUnits = collect([]);
        $availableMaterials->each(fn ($material) => $availableUnits->push(...$material->material_units));

        foreach ($requestMaterialUnitValues as $requestMaterialUnitValue) {
            $isFound = $availableUnits->search(fn ($unit) => (int)$requestMaterialUnitValue->material_unit_id === (int)$unit->id);
            if ($isFound === false) throw ValidationException::withMessages(['Некорректные значения']);
        }

        try {
            DB::beginTransaction();
            $content = VariantContent::query()->create($data);
            $content->material_unit_values()->attach($valuesIds);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('message', 'Непредвиденная ошибка!');
        }
        return redirect("/admin/materials/variants-content")->with('message', "Контент {$data['title']} успешно создан!");
    }

    public function edit(VariantContent $variantContent)
    {
        $variantContent->load('items', 'text_items', 'material_unit_values');
        return inertia('Material/Content/Edit', [
            'variantContent' => $variantContent
        ]);
    }

    public function appendItem(VariantContent $variantContent, AppendItemRequest $request)
    {
        $data = $request->validated();
        return $variantContent->items()->create($data);
    }

    public function deleteItem(VariantContentItem $variantContentItem)
    {
        return $variantContentItem->delete();
    }

    public function appendTextItem(VariantContent $variantContent, AppendTextItemRequest $request)
    {
        $data = $request->validated();
        return $variantContent->text_items()->create($data);
    }

    public function deleteTextItem(VariantContentTextItem $variantContentTextItem)
    {
        return $variantContentTextItem->delete();
    }

    public function appendImage(AppendImageRequest $request, UploadImageService $uploadImageService, VariantContentItem $variantContentItem)
    {
        $data = $request->validated();
        $data = $uploadImageService->upload($data['image']);
        $variantContentItem->update([
            'image_url' => $data['url'],
            'image_path' => $data['path'],
        ]);
        return $variantContentItem;
    }

    public function destroy(VariantContent $contentItem)
    {
        return $contentItem->delete();
    }
}
