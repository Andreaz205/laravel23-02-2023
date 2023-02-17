<?php

namespace App\Http\Controllers\Option;

use App\Http\Controllers\Controller;
use App\Http\Requests\Variant\UpdateVariantFieldRequest;
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
        $this->middleware('can:product edit', ['only' => ['edit', 'update']]);
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

    public function destroy(Product $product, OptionName $optionName)
    {
        $optionName->delete();
        return Response::json(['status' => 'success']);
    }
}
