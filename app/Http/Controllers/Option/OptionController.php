<?php

namespace App\Http\Controllers\Option;

use App\Http\Controllers\Controller;
use App\Http\Requests\BindOptionWithNewValueToVariantRequest;
use App\Http\Requests\Option\BindOptionToVariantRequest;
use App\Http\Requests\Option\StoreImageRequest;
use App\Http\Resources\Image\OptionImageResource;
use App\Http\Resources\Option\OptionValuesResource;
use App\Models\OptionImage;
use App\Models\OptionName;
use App\Models\OptionValue;
use App\Models\OptionValueVariants;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:product list', ['only' => ['index', 'show']]);
        $this->middleware('can:product create', ['only' => ['create', 'store']]);
        $this->middleware('can:product edit', ['only' => ['bind', 'bindWithNewValue', 'toggleIsColor']]);
        $this->middleware('can:product delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $optionNames = OptionName::with('option_values')->get();
        return inertia('Option/Index', [
            'optionNames' => $optionNames,
            'can-options' => [
                'list' => Auth('admin')->user()?->can('options list'),
                'create' => Auth('admin')->user()?->can('options create'),
                'edit' => Auth('admin')->user()?->can('options edit'),
                'delete' => Auth('admin')->user()?->can('options delete'),
            ]
        ]);
    }

    public function show(OptionName $name)
    {
        $optionValues = $name->option_values()->with('image')->get();
        return inertia('Option/Show', [
            'optionName' => $name,
            'optionValues' => $optionValues,
            'can-options' => [
                'list' => Auth('admin')->user()?->can('options list'),
                'create' => Auth('admin')->user()?->can('options create'),
                'edit' => Auth('admin')->user()?->can('options edit'),
                'delete' => Auth('admin')->user()?->can('options delete'),
            ]
        ]);
    }

    public function store(Request $request, Product $product)
    {

        $validator = Validator::make($request->all(), [
            'options' => 'required|array',
            'options.*.option_name_id' => 'integer|exists:option_names,id',
            'options.*.option_value_id' => 'integer|exists:option_values,id',
            'options.*.name' => 'string|unique:option_names,title',
            'options.*.value' => 'string|unique:option_values,title',
        ]);

        if ($validator->fails()) {
            return Response::json(['errors' => $validator->messages()], 422);
        }
        $data = $validator->validated();
        $options = $data['options'];
        try {
            DB::beginTransaction();
            foreach ($options as $option) {
                if (isset($option['name']) && isset($option['value'])) {
                    $newName = $option['name'];
                    $newValue = $option['value'];

                    $optionName = OptionName::create([
                        'title' =>  $newName,
                    ]);
                    $value = OptionValue::create([
                        'title' => $newValue,
                        'option_name_id' => $optionName->id,
                    ]);
                    $product->option_names()->attach($optionName->id, ['default_option_value_id' => $value->id]);

                } else if (isset($option['option_name_id']) && isset($option['value'])) {
                    $optionNameId = $option['option_name_id'];
                    $newValue = $option['value'];
                    $candidateName = OptionName::find($optionNameId);
                    $candidateValue = $candidateName->option_values()->where('title', $newValue)->first();
                    if (isset($candidateValue)) {
                        $errorData = array(['Указанное вами значение ' . $candidateValue->title . ' уже существует!']);
                        return Response::json(['errors' => $errorData], 422);
                    }
                    $value = OptionValue::create([
                        'title' => $newValue,
                        'option_name_id' => $optionNameId,
                    ]);
                    $product->option_names()->attach($optionNameId, ['default_option_value_id' => $value->id]);

                } else if (isset($option['option_name_id']) && isset($option['option_value_id'])) {

                    $optionNameId = $option['option_name_id'];
                    $optionValueId = $option['option_value_id'];
                    $candidate = $product->option_names()->where('option_name_id', $optionNameId)->first();
                    if (isset($candidate)) {
                        $errorData = array(['Указанное вами свойство ' . $candidate->title . ' уже существует!']);
                        return Response::json(['errors' => $errorData], 422);
                    }
                    $product->option_names()->attach($optionNameId, ['default_option_value_id' => $optionValueId]);
                }
            }
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            return Response::json(['error' => $error->getMessage()], 500);
        }

        return null;

//        $validator = Validator::make($request->all(), [
//            'options' => 'required|array',
//            'options.*.name' => 'string',
//            'options.*.default_value' => 'string',
//        ]);
//
//        if ($validator->fails()) {
//            return Response::json(['errors' => $validator->messages()], 422);
//        }
//        $data = $validator->validated();
//
//        try {
//            DB::beginTransaction();
//            foreach ($data['options'] as $option) {
//                $newOptionValueAsDefault = OptionValue::create([
//                    'title' => $option['default_value'],
//                    'product_id' => $product->id,
//                    'is_default' => true
//                ]);
//                $newOptionName = OptionName::create([
//                    'title' => $option['name'],
//                    'product_id' => $product->id,
//                    'default_option_value_id' => $newOptionValueAsDefault->id,
//                ]);
//                $newOptionValueAsDefault->option_name_id = $newOptionName->id;
//                $newOptionValueAsDefault->save();
//
//                $variants = $product->variants;
//                if (isset($variants) && count($variants) > 0) {
//                    foreach ($variants as $variant) {
//                        OptionValueVariants::create([
//                           'option_value_id' => $newOptionValueAsDefault->id,
//                           'variant_id' => $variant->id,
//                        ]);
//                    }
//                }
//            }
//           DB::commit();
//        } catch (\Exception $exception) {
//            DB::rollBack();
//            return response()->json(['error' => $exception->getMessage()]);
//        }
//        return Response::json(['status' => 'success']);
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
//        return new OptionValuesResource($freshBoundOptionValue);
        return $freshBoundOptionValue;
    }

    public function bindWithNewValue(Product $product, Variant $variant, BindOptionWithNewValueToVariantRequest $request)
    {
        $data = $request->validated();
        $optionNameId = $data['option_name_id'];
        $newValue = $data['value'];
        $existsValue = $variant->option_values()->where('option_name_id', $optionNameId)->first();
        if (!isset($existsValue)) return Response::json(['error' => 'Invalid request, try to create option_value firstly!'], 400);
        try {
            DB::beginTransaction();
            $optionName = OptionName::find($optionNameId);
            $candidate = $optionName->option_values()->where('title', $newValue)->first();
            if (isset($candidate))throw ValidationException::withMessages(['У свойства ' . $optionName->title . ' уже существует значение ' . $newValue]);
            $newOptionValue = OptionValue::create([
                'title' => $newValue,
                'option_name_id' => $optionNameId,
            ]);
//            $variant->option_values()
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

    public function uploadImage(StoreImageRequest $request, OptionValue $value)
    {
        $data = $request->validated();
        $image = $data['image'];
        $name = md5(Carbon::now() . '_' . $image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();
        $filePath = Storage::disk('public')->putFileAs('images', $image, $name);
        $imageUrl = url('/storage/images/'. $name);
        $newImage = OptionImage::create([
            'option_value_id' => $value->id,
            'image_url' => $imageUrl,
            'image_path' => $filePath,
        ]);
        return new OptionImageResource($newImage);
    }

    public function updateImage(StoreImageRequest $request, OptionValue $value)
    {
        $data = $request->validated();
        $image = $data['image'];
        $name = md5(Carbon::now() . '_' . $image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();
        $filePath = Storage::disk('public')->putFileAs('images', $image, $name);
        $imageUrl = url('/storage/images/'. $name);
        $value->image()->delete();
        $newImage = OptionImage::create([
            'option_value_id' => $value->id,
            'image_url' => $imageUrl,
            'image_path' => $filePath,
        ]);
        return new OptionImageResource($newImage);
    }

    public function destroyImage(OptionValue $value)
    {
        $value->image()->delete();
        return Response::json(['status' => 'success']);
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
