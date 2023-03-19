<?php

namespace App\Http\Controllers\Variant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Variant\DeleteVariantsRequest;
use App\Http\Requests\Variant\UpdateVariantFieldRequest;
use App\Http\Resources\Variant\CreatedVariantResource;
use App\Http\Services\Variant\VariantService;
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

    public function store(Product $product, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'newOptions' => 'array',
            'newOptions.*.name' => 'required|string',
            'newOptions.*.value' => 'required|string',
            'newValues' => 'array',
            'newValues.*.value' => 'required|string',
            'newValues.*.name_id' => 'required|integer|exists:option_names,id',
            'values' => 'array',
            'values.*' => 'integer|required',
        ]);

        if ($validator->fails()) {
            return Response::json(['error' => $validator->messages()], 422);
        }
        // 'newValues' => ['value' => 'someValue', 'name_id' => id ]
        $data = $validator->validated();
        $newValues = $data['newValues'] ?? null;
        $values = $data['values'] ?? null;
        $newOptions = $data['newOptions'] ?? null;

        try {
            $error = $this->variantService
                ->validateVariantWhenCreate($values, $newValues, $newOptions, $product);
            if (isset ($error)) {
                if ($error === 'empty_options')
                    return Response::json(['error' => 'Невозможно создать вариант без свойств!'], 422);
                if ($error === 'new_value_already_exists')
                    return Response::json(['error' => 'Невозможно добавить значение для свойства, т.к. оно уже существет!'], 422);
                if ($error === 'variant_with_options_already_exists')
                    return Response::json(['error' => 'Вариант с указанными свойствами уже существет!'], 422);
            }


            DB::beginTransaction();
            $variant = Variant::create([
                'product_id' => $product->id
            ]);


            $productOptionNames = $product->option_names;
            if (isset($newOptions) && (count($newOptions) > 0) && count($productOptionNames) < 1) {
                $errorData = array(['Чтобы добавить вариант необходмо создать свойства!']);
                return Response::json(['errors' => $errorData], 422);
//                foreach ($newOptions as $newOption) {
//
//                    $newOptionValue = OptionValue::create([
//                        'title' => $newOption['value'],
//                        'product_id' => $product->id,
//                    ]);
//                    $newOptionName = OptionName::create([
//                        'title' => $newOption['name'],
//                        'product_id' => $product->id,
//                        'default_option_value_id' => $newOptionValue->id
//                    ]);
//                    $newOptionValue->update([
//                        'option_name_id' =>  $newOptionName->id,
//                    ]);
//                    OptionValueVariants::create([
//                        'variant_id' => $variant->id,
//                        'option_value_id' => $newOptionValue->id,
//                    ]);
//                }
            }

            else if (isset($newValues) and isset($values)) {
                foreach($newValues as $newValue) {
                    $optionName = OptionName::find($newValue['name_id']);
                    if ($newValue['value'])  {
                        $candidate = $optionName->option_values()->where('title', $newValue['value'])->first();
                        if (isset($candidate)) throw ValidationException::withMessages(['У свйства ' . $optionName->title . ' уже существует свойство с названием ' . $newValue['value']]);
                    }
                    $createdValue = OptionValue::create([
                        'title' => $newValue['value'],
                        'option_name_id' => $newValue['name_id']
                    ]);
                    OptionValueVariants::firstOrCreate([
                        'variant_id' => $variant->id,
                        'option_value_id' => $createdValue->id
                    ]);
                }
                foreach($values as $value) {
                    OptionValueVariants::firstOrCreate([
                        'variant_id' => $variant->id,
                        'option_value_id' => $value
                    ]);
                }
            }

            $prices = Price::all();
            if (count($prices) > 0) {
                foreach ($prices as $price) {
                    PriceVariants::create([
                        'variant_id' => $variant->id,
                        'price_id' => $price->id,
                        'price' => 0
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => $exception->getMessage()], 500);
        }
        return new CreatedVariantResource($variant);
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
        $stringIdsTroughComma = $data['images_ids'];
        $result = explode(',', $stringIdsTroughComma);
        if (!isset($result)) {
            return Response::json(['error' => 'No values exists in request!'], 400);
        }
        Variant::whereIn('id', $result)->delete();
        OptionValueVariants::whereIn('variant_id', $result)->delete();

        return Response::json(['status' => 'Deleted successfully!']);
    }
}
