<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Http\Resources\Comment\CommentResource;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CommentController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request, string $type, $commentableId): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Comment::class);

        $commentable = $this->resolveCommentable($type, $commentableId);
        $cacheTags = $this->getCacheTagsForCommentable($commentable);

        $queryParams = $request->query();
        $cacheKey = "{$cacheTags[0]}:list:" . http_build_query($queryParams);

        $comments = Cache::tags($cacheTags)->remember($cacheKey, now()->addHour(), function () use ($request, $commentable) {
            $perPage = $request->input('per_page', 15);
            $orderBy = $request->input('order_by', 'created_at');
            $orderDirection = $request->input('order_direction', 'desc');

            return $commentable
                ->comments()
                ->with('user.image')
                ->orderBy($orderBy, $orderDirection)
                ->paginate($perPage);
        });

        return CommentResource::collection($comments);
    }

    public function store(StoreCommentRequest $request, string $type, int $commentableId): JsonResponse
    {
        $commentable = $this->resolveCommentable($type, $commentableId);
        $comment = $commentable->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->validated('content'),
        ]);

        $this->flushCommentableCache($commentable);

        return (new CommentResource($comment->load('user.image')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateCommentRequest $request, Comment $comment): CommentResource
    {
        $comment->update($request->validated());

        $this->flushCommentableCache($comment->commentable);

        return new CommentResource($comment->load('user.image'));
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $commentable = $comment->commentable;
        $comment->delete();

        $this->flushCommentableCache($commentable);

        return response()->json(null, 204);
    }

    public function show(Comment $comment): CommentResource
    {
        $this->authorize('view', $comment);

        return new CommentResource($comment->load('user.image'));
    }

    protected function resolveCommentable(string $type, $id)
    {
        $class = 'App\\Models\\' . Str::studly(Str::singular($type));
        abort_unless(class_exists($class), 404, "Tipo inválido: {$type}");
        return $class::findOrFail($id);
    }

    private function getCacheTagsForCommentable(Model $commentable): array
    {
        $type = $commentable->getMorphClass();
        return ["comments:{$type}:{$commentable->id}", 'comments'];
    }

    private function flushCommentableCache(Model $commentable): void
    {
        Cache::tags($this->getCacheTagsForCommentable($commentable))->flush();
    }
}
