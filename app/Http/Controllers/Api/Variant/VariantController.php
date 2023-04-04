<?php

namespace App\Http\Controllers\Api\Variant;

use App\Http\Controllers\Controller;
use App\Http\Services\Delivery\CDEKService;
use App\Http\Services\Variant\VariantService;
use App\Models\RelatedVariant;
use App\Models\Variant;
use Illuminate\Http\Request;

class VariantController extends Controller
{
    public function __construct(VariantService $variantService)
    {
        $this->variantService = $variantService;
    }

    public function variants()
    {
        $variants = Variant::all();
//        foreach($variants as $variant) {
//            $this->variantService->aggregateVariantByNameValues($variant);
//        }
        return $variants;
    }

    public function variant(Variant $variant, CDEKService $CDEKService)
    {
        $product = $variant->product;
        $this->variantService->aggregateVariantByNameValues($variant);
//        dd([
//            "weight" =>  $product->weight * 1000,
//            "length" =>  $product->length,
//            "width" =>  $product->width,
//            "height" =>  $product->height
//        ]);

        if (isset($product->height) && isset($product->width) && isset($product->length) && isset($product->weight)) {
            $cdekBody = [
                'from_location' => [
                    'code' => 259,
                ],
                "to_location" => [
                    "code" => 270
                ],
                "packages" => [
                    "number" => 1,
                    "weight" =>  $product->weight * 1000,
                    "length" =>  $product->length,
                    "width" =>  $product->width,
                    "height" =>  $product->height
                ]
            ];
            $cdekData = $CDEKService->calculateByAvailableTariffs($cdekBody);
            dd($cdekData);
            $variant->delivery = ['cdek' => $cdekData];
        }


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
