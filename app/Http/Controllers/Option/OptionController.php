<?php

namespace App\Http\Controllers\Option;

use App\Http\Controllers\Controller;
use App\Http\Requests\BindOptionWithNewValueToVariantRequest;
use App\Http\Requests\Option\BindOptionToVariantRequest;
use App\Http\Resources\Option\OptionValuesResource;
use App\Models\OptionName;
use App\Models\OptionValue;
use App\Models\OptionValueVariants;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class OptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:product list', ['only' => ['index', 'show']]);
        $this->middleware('can:product create', ['only' => ['create', 'store']]);
        $this->middleware('can:product edit', ['only' => ['bind', 'bindWithNewValue', 'toggleIsColor']]);
        $this->middleware('can:product delete', ['only' => ['destroy']]);
    }


    public function store(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'options' => 'required|array',
            'options.*.name' => 'string',
            'options.*.default_value' => 'string',
        ]);

        if ($validator->fails()) {
            return Response::json(['errors' => $validator->messages()], 422);
        }
        $data = $validator->validated();

        try {
            DB::beginTransaction();
            foreach ($data['options'] as $option) {
                $newOptionValueAsDefault = OptionValue::create([
                    'title' => $option['default_value'],
                    'product_id' => $product->id,
                    'is_default' => true
                ]);
                $newOptionName = OptionName::create([
                    'title' => $option['name'],
                    'product_id' => $product->id,
                    'default_option_value_id' => $newOptionValueAsDefault->id,
                ]);
                $newOptionValueAsDefault->option_name_id = $newOptionName->id;
                $newOptionValueAsDefault->save();

                $variants = $product->variants;
                if (isset($variants) && count($variants) > 0) {
                    foreach ($variants as $variant) {
                        OptionValueVariants::create([
                           'option_value_id' => $newOptionValueAsDefault->id,
                           'variant_id' => $variant->id,
                        ]);
                    }
                }
            }
           DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['error' => $exception->getMessage()]);
        }
        return Response::json(['status' => 'success']);
    }


    public function bind(Product $product, Variant $variant, BindOptionToVariantRequest $request)
    {
        $data = $request->validated();
        $optionNameId = $data['option_name_id'];
        $optionValueId = $data['option_value_id'];
        $existsValue = $variant->option_values->where('option_name_id', $optionNameId)->first();
        if (!isset($existsValue)) return Response::json(['error' => 'Invalid request, try to create option_value firstly!'], 400);
        try {
            DB::beginTransaction();
            OptionValueVariants::where('variant_id', $variant->id)->where('option_value_id', $existsValue->id)->delete();
            OptionValueVariants::create([
                   'variant_id' => $variant->id,
                   'option_value_id' => $optionValueId,
                ]);
            DB::commit();
        } catch (\Exception $e) {
          DB::rollBack();
          return Response::json(['error' => $e->getMessage()], 500);
        }
        $freshBoundOptionValue = OptionValue::find($optionValueId);
        return new OptionValuesResource($freshBoundOptionValue);
    }


    public function bindWithNewValue(Product $product, Variant $variant, BindOptionWithNewValueToVariantRequest $request)
    {
        $data = $request->validated();
        $optionNameId = $data['option_name_id'];
        $newValue = $data['value'];
        $existsValue = $variant->option_values->where('option_name_id', $optionNameId)->first();
        if (!isset($existsValue)) return Response::json(['error' => 'Invalid request, try to create option_value firstly!'], 400);
        try {
            DB::beginTransaction();

            $newOptionValue = OptionValue::create([
                'title' => $newValue,
                'option_name_id' => $optionNameId,
                'product_id' => $product->id,
            ]);
            OptionValueVariants::where('variant_id', $variant->id)->where('option_value_id', $existsValue->id)->delete();
            OptionValueVariants::create([
                'variant_id' => $variant->id,
                'option_value_id' => $newOptionValue->id,
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::json(['error' => $e->getMessage()], 500);
        }
        return new OptionValuesResource($newOptionValue);
    }

    public function toggleIsColor(Product $product, OptionName $optionName)
    {
        $productOptionNames = $product->option_names;
        foreach ($productOptionNames as $productOptionName) {
            if ($productOptionName->is_color === true) {
                $productOptionName->update(['is_color' => false]);
            }
        }
        if (!$optionName->is_color) {
            $optionName->update(['is_color' => true]);
        }
        return $optionName->is_color;
    }

    public function destroy(Product $product, OptionName $optionName)
    {
        $optionName->delete();
        return Response::json(['status' => 'success']);
    }
}
