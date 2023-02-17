<?php

namespace App\Http\Controllers\Room;

use App\Http\Controllers\Controller;
use App\Http\Requests\Room\StoreRoomRequest;
use App\Http\Requests\Room\UpdateImageRequest;
use App\Http\Resources\Room\FilteredCategoriesResource;
use App\Http\Resources\Room\RoomResource;
use App\Models\Category;
use App\Models\RichImage;
use App\Models\Room;
use App\Models\RoomCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:room list', ['only' => ['index', 'show']]);
        $this->middleware('can:room create', ['only' => ['create', 'store']]);
        $this->middleware('can:room edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:room delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $rooms = Room::latest()->with('categories')->get(['id', 'image_url', 'title']);
        foreach ($rooms as $room) {
            $dataRoomCategories = $room->categories;
            $categories = [];
            foreach($dataRoomCategories as $dataRoomCategory) {
                if ($dataRoomCategory->deleted_at !== null) $categories[] = $dataRoomCategory;
            }
            $room->categoriesData = $categories;
        }

        return inertia('Room/Index', [
            'data' => $rooms,
            'can-rooms' => [
                'list' => Auth('admin')->user()?->can('room list'),
                'create' => Auth('admin')->user()?->can('room create'),
                'edit' => Auth('admin')->user()?->can('room edit'),
                'delete' => Auth('admin')->user()?->can('room delete'),
            ]
        ]);
//        return view('room.index', compact('rooms'));
    }

    public function store(StoreRoomRequest $request)
    {
        $data = $request->validated();
        $image = $data['image'];

        $name = md5(Carbon::now() . '_' . $image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();
        $filePath = Storage::disk('public')->putFileAs('images', $image, $name);

        $room = Room::create([
            'title' => $data['title'],
            'image_path' => $filePath,
            'image_url' => url('/storage/' . $filePath),
        ]);
        return new RoomResource($room);
    }

    public function toggleBind(Room $room, Category $category)
    {
        $note = RoomCategories::where('room_id', $room->id)->where('category_id', $category->id)->first();
        if (isset($note)) {
            $note->delete();
            return response()->json(['status' => 'delete']);
        } else {
            RoomCategories::create([
                'room_id' => $room->id,
                'category_id' => $category->id
            ]);
        }
        return response()->json(['status' => 'create']);
    }

    public function filteredCategories(Room $room)
    {
        $categories = Category::all();
        $roomCategories = $room->categories()->whereNull('room_categories.deleted_at')->get();
        if (isset($roomCategories) && count($roomCategories) && count($roomCategories) > 0) {
            foreach ($categories as $category) {
                $category->is_exists = false;
                foreach ($roomCategories as $roomCategory) {
                    if ($roomCategory->id == $category->id) {
                        $category->is_exists = true;
                        continue 2;
                    }
                }
            }
        }
        return FilteredCategoriesResource::collection($categories);
    }

    public function updateImage(Room $room, UpdateImageRequest $request)
    {
        $data = $request->validated();
        $image = $data['image'];
        $name = md5(Carbon::now() . '_' . $image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();
        $filePath = Storage::disk('public')->putFileAs('images', $image, $name);
        $room->update([
            'image_path' => $filePath,
            'image_url' => url('/storage/' . $filePath),
        ]);
        return new RoomResource($room);
    }

    public function deleteImage(Room $room)
    {
        $path = $room->image_path;
        Storage::delete($path);
        $room->update([
            'image_path' => null,
            'image_url' => null,
        ]);
        return response()->json(['status' => 'success']);
    }

    public function deleteRoom(Room $room)
    {
        $room->delete();
        return response()->json(['status' => 'success']);
    }
}
