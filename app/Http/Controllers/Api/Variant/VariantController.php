<?php

namespace App\Http\Controllers\Api\Variant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Variant\PriceRequest;
use App\Http\Services\Delivery\CDEKService;
use App\Http\Services\Variant\VariantService;
use App\Models\Discount;
use App\Models\Price;
use App\Models\RelatedVariant;
use App\Models\Variant;
use Illuminate\Http\Request;

class VariantController extends Controller
{
    public function __construct(VariantService $variantService)
    {
        $this->variantService = $variantService;
    }

    public function variantIds()
    {
        return Variant::pluck('id')->toArray();
    }

    public function variants()
    {
        $variants = Variant::all();
//        foreach($variants as $variant) {
//            $this->variantService->aggregateVariantByNameValues($variant);
//        }
        return $variants;
    }

    public function getPrice(PriceRequest $request)
    {
        $data = $request->validated();
        $user = Auth('sanctum')->user();
        $group = $user?->group()->get();

        if (isset($group) && count($group)) {
            $price = Price::query()->whereRelation('groups', 'id', '=', $group[0]?->id)->first();
            if (isset($price)) {
                $variants = Variant::query()
                    ->whereIn('id', $data['variants'])
                    ->with(['prices' => fn ($query) => $query->where('price_id', $price->id)])
                    ->get();

                $response = [];
                foreach ($variants as $variant) {
                    $response[] = [
                        'id' => $variant->id,
                        'price' => $variant->prices[0]->price,
                    ];
                }
                return $response;
            }
        }
        return Variant::query()->whereIn('id', $data['variants'])->get(['id', 'price']);
    }

    public function variant(Variant $variant, CDEKService $CDEKService, Request $request)
    {
        $product = $variant->product;
        $this->variantService->aggregateVariantByMaterialUnits($variant);
        unset($variant->product->variants, $variant->material_unit_values);

        $this->variantService->productColorsForVariant($variant, $product);

//        if (isset($product->height) && isset($product->width) && isset($product->length) && isset($product->weight)) {
//            $cdekBody = [
//                'from_location' => [
//                    'code' => 259,
//                ],
//                "to_location" => [
//                    "code" => 270
//                ],
//                "packages" => [
//                    "number" => 1,
//                    "weight" =>  $product->weight * 1000,
//                    "length" =>  $product->length,
//                    "width" =>  $product->width,
//                    "height" =>  $product->height
//                ]
//            ];
//            $cdekData = $CDEKService->calculateByAvailableTariffs($cdekBody);
//            $variant->delivery = ['cdek' => $cdekData];
//        }
        $variant->title = $variant->getTitleAttribute();

        $variant->load('images');

        $additionalSizes = $product->additional_sizes()->get();
        $sizes['main_size'] = [
            'length' => $product->length,
            'width' => $product->width,
            'height' => $product->height,
        ];
        $sizes['other_sizes'] = $additionalSizes;
        $variant->sizes = $sizes;



//        $variant->sizes->main_size->width = $product->width;
//        $variant->sizes->main_size->height = $product->height;

//        $variant->load('images');
//
//        $relatedVariantNotes = RelatedVariant::where('parent_variant_id', $variant->id)->get();
//        $ids = [];
//        foreach($relatedVariantNotes as $relatedVariantNote) {
//            array_push($ids, $relatedVariantNote->related_variant_id);
//        }
//        $relatedVariants = Variant::whereIn('id', $ids)->get();
//
//        foreach($relatedVariants as $relatedVariant) {
//            $relatedVariant->title = $relatedVariant->product->title;
//        }
//        $variant->related = $relatedVariants;
//        $publishedReviews = $variant->publishedReviews;
//
//        $reviewsImages = [];
//        $markArray = [];
//        if (isset($publishedReviews)) {
//            foreach($publishedReviews as $variantReview) {
//                array_push($markArray, $variantReview->mark);
//                if (isset($variantReview->images)) {
//                    $variantReview->load('images');
//                    foreach($variantReview->images as $image) {
//                        array_push($reviewsImages, $image);
//                    }
//                }
//            }
//            $sum = array_sum($markArray);
//            if (count($markArray)) {
//                $variant->rating = $sum / count($markArray);
//            }
//        }
//        $variant->reviews = $publishedReviews;
//        $variant->reviewsImages = $reviewsImages;
//        $variant->load('rich_content');

        return $variant;
    }


}




