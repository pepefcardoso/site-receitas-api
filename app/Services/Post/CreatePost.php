<?php

namespace App\Services\Post;

use App\Models\Post;
use App\Services\Image\CreateImage;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreatePost
{
    protected CreateImage $createImageService;

    public function __construct(
        CreateImage $createImageService,
    ) {
        $this->createImageService = $createImageService;
    }

    public function create(array $data): Post|string
    {
        try {
            DB::beginTransaction();

            $user_id = Auth::id();
            $data['user_id'] = $user_id;

            $post = Post::create($data);

            $topics = data_get($data, 'topics');
            $post->topics()->sync($topics);

            $image = data_get($data, 'image');
            $this->createImageService->create($post, $image);

            DB::commit();

            return $post;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
