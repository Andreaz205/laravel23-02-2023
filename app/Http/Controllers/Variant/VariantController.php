<?php

namespace App\Http\Controllers\Variant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Variant\BindMaterialsRequest;
use App\Http\Requests\Variant\DeleteVariantsRequest;
use App\Http\Requests\Variant\SearchRequest;
use App\Http\Requests\Variant\StoreRequest;
use App\Http\Requests\Variant\UpdateVariantFieldRequest;
use App\Http\Resources\Variant\CreatedVariantResource;
use App\Http\Services\Material\MaterialService;
use App\Http\Services\Variant\VariantService;
use App\Models\Material;
use App\Models\MaterialUnit;
use App\Models\MaterialUnitValue;
use App\Models\MaterialUnitValueVariants;
use App\Models\OptionName;
use App\Models\OptionValue;
use App\Models\OptionValueVariants;
use App\Models\Price;
use App\Models\PriceVariants;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class VariantController extends Controller
{
    protected VariantService $variantService;

    public function __construct(VariantService $variantService)
    {
        $this->variantService = $variantService;
        $this->middleware('can:product list', ['only' => ['index', 'show']]);
        $this->middleware('can:product create', ['only' => ['create', 'store']]);
        $this->middleware('can:product edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:product delete', ['only' => ['destroy']]);
    }

    /**
     * @throws ValidationException
     */
    public function store(Product $product, StoreRequest $request)
    {
        $data = $request->validated();
        $form = collect($data['materials']);
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Валидация по юнитам
        $productMaterials = $product->materials()
            ->with([
                'main_material_unit',
                'material_units' => fn ($query) => $query->with('values')
            ])->get();

        foreach ($form as $formItem) {
            $materialId = $formItem['material_id'];
            $key = $productMaterials->search(fn ($value, $key) => $value->id == $materialId);
            if ($key === false)
                throw ValidationException::withMessages(['Указанного material_id = ' . $formItem['material_id'] . " не существет"]);
        }

        foreach ($productMaterials as $productMaterial) {
            $plainUnits = MaterialService::plainMaterialUnits($productMaterial->main_material_unit);
            $materialForm = $form->first(fn ($item) => $item['material_id'] === $productMaterial->id);
            $materialFormIds = $materialForm['ids'];
            foreach ($plainUnits as $key => $plainUnit) {
                $plainUnitValues = $plainUnit->values;
                if ($plainUnitValues->search(fn ($value) => $value->id == $materialFormIds[$key]) === null)
                    throw ValidationException::withMessages(['Неверно указаны значения, нарушение последовательности так как не указано значение для ' . $plainUnit->title]);
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $valuesIds = [];
        foreach ($form as $formItem) {
            $ids = $formItem['ids'];
            foreach($ids as $id) {
                $valuesIds[] = $id;
            }
        }

        $otherVariants = $product->variants()->with('material_unit_values')->get();
        foreach ($otherVariants as $variant) {
            $variantValuesIds = $variant->material_unit_values->map(fn ($value) => $value->id)->toArray();
            $result = array_intersect($variantValuesIds, $valuesIds);
            if (count($result) === count($valuesIds))
                throw ValidationException::withMessages(['Невозможно создать вариант так, как вариант с такими значениями уже существует!']);
        }

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        try {
            DB::beginTransaction();

            $variant = Variant::create([
                'product_id' => $product->id
            ]);
            $prices = Price::all();
            foreach ($prices as $price) {
                $variant->prices()->create(['price_id' => $price->id]);
            }
            $variant->material_unit_values()->sync($valuesIds);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return Response::json(['errors' => [$exception->getMessage()]], 422);
        }
        $response = Variant::with(['material_unit_values' => fn ($query) => $query->with('color'), 'prices', 'images'])->where('id', $variant->id)->first();
        return $response;
    }

    public function updateField(Product $product, Variant $variant, UpdateVariantFieldRequest $request)
    {
        $data = $request->validated();
        $field = $data['field'];
        $value = $data['value'];

        $variant->update([
            $field => $value
        ]);

        return $variant->$field;
    }

    public function destroy(DeleteVariantsRequest $request, Product $product)
    {
        $data = $request->validated();
        $stringIdsTroughComma = $data['variants_ids'];
        $result = explode(',', $stringIdsTroughComma);
        if (!isset($result)) {
            throw ValidationException::withMessages(['No values exists in request!']);
        }
        Variant::whereIn('id', $result)->delete();
        OptionValueVariants::whereIn('variant_id', $result)->delete();
        return Response::json(['status' => 'Deleted successfully!']);
    }

    public function search(SearchRequest $request)
    {
        $data = $request->validated();
        if (isset($data['count'])) {
            $count = $data['count'];
        }
        $term = $data['term'];
        $variants = Variant::query()
            ->whereRelation('material_unit_values', 'value', 'ILike', '%'. $term.'%')
            ->orWhereRelation('product', 'title', 'ILike', '%'.$term.'%')
            ->paginate($count ?? 50)
            ->through(function ($variant) {
                $variant->setRelation('images', $variant->images->take(1));
                return $variant;
            });
        foreach ($variants as $variant) {
            $variant->title = $variant->getTitleAttribute();
        }
        return $variants;
    }

    /**
     * @throws ValidationException
     */
    public function bindMaterials(BindMaterialsRequest $request, Variant $variant, MaterialService $materialService, Material $material)
    {

        $data = $request->validated();
        // Валидация
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $materialValuesIds = $data['material_unit_values'];
        $materialUnits = $material->material_units;
        $lastId = $materialValuesIds[count($materialValuesIds) - 1];
        $value = MaterialUnitValue::find($lastId);
        $allValues = $materialService->allMaterialValues($materialUnits);
        $set = array_reverse($materialService->getSetByLastValue($value, $allValues));

        foreach ($set as $setItem) {
            if (!in_array($setItem['id'], $materialValuesIds)) {
                $errorMaterialValue = MaterialUnitValue::find($setItem['id']);
                throw ValidationException::withMessages(['Ошибка валидации! В запросе указано значение ' . $errorMaterialValue->value . ' которого не существет в базе наборов!']);
            }
        }

        $unitIds = $materialUnits->map(fn ($item) => $item->id);
        $valuesToDetach = $variant->material_unit_values()->whereIn('material_unit_id', $unitIds)->pluck('material_unit_values.id')->toArray();


        // Проверка чтобы не повторялись свойства для вариантов
        $variantValuesIds = $variant->material_unit_values()->pluck('material_unit_values.id')->toArray();
        foreach ($valuesToDetach as $valueIdToDetach) {
            $variantValuesIds = array_filter($variantValuesIds, fn ($item) => $item !== $valueIdToDetach);
        }
        $resultIds = [...$variantValuesIds, ...$materialValuesIds];

        $otherProductVariants = Variant::where('product_id', $variant->product_id)->whereNot('id', $variant->id)->with('material_unit_values')->get();
        foreach ($otherProductVariants as $otherProductVariant) {
            $valuesIds = $otherProductVariant->material_unit_values->map(fn($item) => $item->id);
            $found = true;
            foreach ($valuesIds as $valId) {
                if (!in_array($valId, $resultIds)){
                    $found = false;
                }
            }
            if ($found) throw ValidationException::withMessages(['Такой вариант уже сущуствует!']);
        }
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        try {
           DB::beginTransaction();
            MaterialUnitValueVariants::whereIn('material_unit_value_id', $valuesToDetach)->whereVariantId($variant->id)->delete();
            $result = [];
            foreach ($materialValuesIds as $id) {
                $result[] = [
                    'variant_id' => $variant->id,
                    'material_unit_value_id' => $id,
                ];
            }
            MaterialUnitValueVariants::insert($result);
           DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::json(['errors' => [$e->getMessage()]], 422);
        }
        return $variant->load(['material_unit_values' => fn ($query) => $query->with('color')], 'images', 'prices');
    }
}
