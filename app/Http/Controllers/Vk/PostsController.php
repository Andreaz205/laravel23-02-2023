<?php

namespace App\Http\Controllers\Vk;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\VK\GetPostsRequest;
use App\Http\Resources\Api\Vk\PostsResource;
use App\Models\VkPosts;

class PostsController extends Controller
{
    public function posts(GetPostsRequest $request)
    {
        $data = $request->validated();
        $count = isset($data['count']) ? min($data['count'], 10) : 10;
        $posts = VkPosts::limit($count)->get();
        return PostsResource::collection($posts);
    }
}
