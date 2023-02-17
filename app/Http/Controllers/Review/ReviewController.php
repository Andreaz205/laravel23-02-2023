<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\Api\Variant\VariantController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Review\UpdateReviewRequest;
use App\Http\Services\Variant\VariantService;
use App\Models\Review;
use App\Models\ReviewImage;
use App\Services\VariantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function __construct(VariantService $service)
    {
        $this->variantService = $service;
    }

    public function index()
    {
        return view('review.index');
    }

    public function data()
    {
        $reviews = Review::latest()->get();
        if (isset($reviews)) {
            foreach ($reviews as $review) {
                $review->images;
                $variant = $review->variant;
                $this->variantService->aggregateVariantByNameValues($variant);
            }
        }
        return $reviews;
    }

    public function saveAndPublic(Review $review, UpdateReviewRequest $request)
    {
        $data = $request->validated();

        $imagesForDelete = null;
        if (isset($data['images_for_delete'])) $imagesForDelete = $data['images_for_delete'];
        if (isset($data['images_for_delete'])) unset($data['images_for_delete']);

        if (isset($data)) {
            $review->update([...$data, 'published' => 1]);
        }
        return $review;
    }

    public function save(Review $review, UpdateReviewRequest $request)
    {
        $data = $request->validated();

        $imagesForDelete = null;
        if (isset($data['images_for_delete'])) $imagesForDelete = $data['images_for_delete'];
        if (isset($data['images_for_delete'])) unset($data['images_for_delete']);

        if (isset($data)) {
            $review->update($data);
        }

        if (isset($imagesForDelete)) {
            foreach ($imagesForDelete as $key=>$imageId) {
                $image = ReviewImage::findOrFail($imageId);
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }
        }
        return response()->json(['status' => 'success']);
    }

    public function delete(Review $review)
    {
        $review->delete();
        return $review;
    }
}
