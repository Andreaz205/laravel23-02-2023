<?php

namespace App\Http\Controllers\Api\Review;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Review\StoreReviewImagesRequest;
use App\Http\Requests\Api\Review\StoreReviewRequest;
use App\Models\Review;
use App\Models\ReviewImage;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ReviewController extends Controller
{
    public function storeReviewImages(Variant $variant, StoreReviewImagesRequest $request)
    {
        $data = $request->validated();
        $images = $data['images'];
//        $product = $variant->product;
        $reviewImages = [];

        foreach($images as $image) {
            $name = md5(Carbon::now() . '_' . $image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();
            $filePath = Storage::disk('public')->putFileAs('images', $image, $name);
            $reviewImage = ReviewImage::create([
                'image_path' => $filePath,
                'image_url' => url('/storage/' . $filePath),
            ]);
            $reviewImages[] = $reviewImage;

//            \Intervention\Image\Facades\Image::make($image)->fit(100, 100)
//                ->save(storage_path('app/public/images/' . $previewName));
        }

        return response()->json(['data' => $reviewImages]);
    }

    public function storeReview(Variant $variant, StoreReviewRequest $request)
    {
        $data = $request->validated();
        if (isset($data['images'])) $imageIds = $data['images'];
        $number = $data['number'];
        $data['variant_id'] = $variant->id;
        unset($data['images'], $data['number']);
        try {
            DB::beginTransaction();

            $review = Review::create([...$data, 'product_id' => $variant->product_id]);
            $review->created_at = $review->created_at->toDateTimeString();
            if (isset($imageIds)) {
                foreach ($imageIds as $imageId) {
                    $reviewImage = ReviewImage::findOrFail($imageId);
                    $reviewImage->update([
                        'review_id' => $review->id
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return Response::json(['message' => $exception->getMessage()], 500);
        }

        //  Профилаткитка и очистка
        $notNeededReviewImages = ReviewImage::whereNull('review_id')->get();
        if (isset ($notNeededReviewImages)) {
            foreach($notNeededReviewImages as $notNeededReviewImage) {
                Storage::disk('public')->delete($notNeededReviewImage->image_path);
                $notNeededReviewImage->delete();
            }
        }
        ///////////
        ///
        return $review;
    }


    public function reviews(Variant $variant, Request $request)
    {
        $data = Validator::make($request->all(), [
            'last_review_id' => 'nullable|integer|exists:reviews,id',
            'all' => ['nullable', Rule::in('true')]
        ])->validated();

        if (isset($datap['all'])) {
            return  $variant->reviews()
                ->where('published', true)
                ->with(['images', 'answer' => fn ($query) => $query->with('manager')])
                ->latest()
                ->get();
        }

        if (isset($data['last_review_id'])) {
            return  $variant->reviews()
                ->where('id', '<', $data['last_review_id'])
                ->where('published', true)
                ->with(['images', 'answer' => fn ($query) => $query->with('manager')])
                ->latest()
                ->limit(10)
                ->get();
        }

        return  $variant->reviews()
            ->where('published', true)
            ->with(['images', 'answer' => fn ($query) => $query->with('manager')])
            ->latest()
            ->limit(10)
            ->get();
    }
}
