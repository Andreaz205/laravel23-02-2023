<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\Api\Variant\VariantController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Review\UpdateReviewRequest;
use App\Http\Services\Variant\VariantService;
use App\Models\Product;
use App\Models\Review;
use App\Models\ReviewAnswer;
use App\Models\ReviewImage;
use App\Services\VariantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function __construct(VariantService $service)
    {
        $this->variantService = $service;
        $this->middleware('can:room list', ['only' => ['index', 'show']]);
        $this->middleware('can:room create', ['only' => ['create', 'store']]);
        $this->middleware('can:room edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:room delete', ['only' => ['destroy']]);
    }

    public function index()
    {

        $reviews = Review::latest()->paginate(50);
        if (isset($reviews)) {
            foreach ($reviews as $review) {
                $review->load('images');
                $review->answer?->load('manager');
                $variant = $review->variant->load('product');
                $variant->title = $variant->getTitleAttribute();
//                $this->variantService->aggregateVariantByNameValues($variant);
            }
        }
        return inertia('Review/Index', [
            'reviewsData' => $reviews,
            'can-reviews' => [
                'list' => Auth('admin')->user()?->can('review list'),
                'create' => Auth('admin')->user()?->can('review create'),
                'edit' => Auth('admin')->user()?->can('review edit'),
                'delete' => Auth('admin')->user()?->can('review delete'),
            ]
        ]);
    }



    public function publish(Review $review)
    {
//        $imagesForDelete = null;
//        if (isset($data['images_for_delete'])) $imagesForDelete = $data['images_for_delete'];
//        if (isset($data['images_for_delete'])) unset($data['images_for_delete']);
        $review->update(['published' => 1]);
        return $review;
    }

    public function review(Review $review)
    {
        if (!$review->is_viewed) $review->update(['is_viewed' => true]);
        $review->load('images');
        $review->answer?->load('manager');
        $variant = $review->variant->load('product');
        $variant->title = $variant->getTitleAttribute();


        return inertia('Review/Show', [
            'reviewData' => $review,
            'user' => Auth('admin')->user(),
            'can-reviews' => [
                'list' => Auth('admin')->user()?->can('review list'),
                'create' => Auth('admin')->user()?->can('review create'),
                'edit' => Auth('admin')->user()?->can('review edit'),
                'delete' => Auth('admin')->user()?->can('review delete'),
            ]
        ]);
    }

    public function save(Review $review, UpdateReviewRequest $request)
    {
        $data = $request->validated();

        if (isset($data['images_for_delete'])) $imagesForDelete = $data['images_for_delete'];
        if (isset($data['images_for_delete'])) unset($data['images_for_delete']);

        if (isset($data['answer'])) {
            $answer = $data['answer'];
            ReviewAnswer::create([
                'review_id' => $review->id,
                'content' => $answer['content'],
                'admin_id' => $answer['admin_id']
            ]);
            unset($data['answer']);
        }
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

    public function deleteReviewAnswer(Review $review)
    {
        $review->answer()->delete();
        return Response::json(['status' => 'success']);
    }

    public function unpublic(Review $review)
    {
        //check if published
        if (!$review->published) {
            return Response::json(['error' => 'Отзыв не опубликован!'], 400);
        }
        $review->published = false;
        $review->save();
        return Response::json(['status' => 'success']);
    }


    public function delete(Review $review)
    {
        $review->delete();
        return $review;
    }
}
