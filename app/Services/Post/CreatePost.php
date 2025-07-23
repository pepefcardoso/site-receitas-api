<?php

namespace App\Services\Post;

use App\Models\Post;
use App\Services\Image\CreateImage;
use App\Services\Image\DeleteImage;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreatePost
{
    public function __construct(
        protected CreateImage $createImageService
    ) {
    }

    /**
     * Cria um novo post e sua imagem associada de forma transacionalmente segura.
     *
     * @param array $data
     * @return Post
     * @throws Exception
     */
    public function create(array $data): Post
    {
        $imageData = null;

        /** @var UploadedFile|null $imageFile */
        if ($imageFile = data_get($data, 'image')) {
            $imageData = $this->createImageService->uploadOnly($imageFile);
        }

        DB::beginTransaction();

        try {
            $data['user_id'] = Auth::id();
            $post = Post::create($data);

            $topics = data_get($data, 'topics', []);
            $post->topics()->sync($topics);

            if ($imageData) {
                $this->createImageService->createDbRecord($post, $imageData);
            }

            DB::commit();

            return $post;

        } catch (Exception $e) {
            DB::rollback();

            if ($imageData) {
                (new DeleteImage())->deleteFile($imageData['path']);
            }

            throw $e;
        }
    }
}
