<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Http\Resources\Comment\CommentResource;
use App\Models\Comment;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    protected function resolveCommentable(string $type, $id)
    {
        $class = 'App\\Models\\' . Str::studly(Str::singular($type));

        abort_unless(class_exists($class), 404, "Tipo inválido: {$type}");

        return $class::findOrFail($id);
    }

    public function index(string $type, $id): AnonymousResourceCollection
    {
        $commentable = $this->resolveCommentable($type, $id);

        $comments = $commentable
            ->comments()
            ->with('user.image')
            ->latest()
            ->paginate();

        return CommentResource::collection($comments);
    }

    public function store(StoreCommentRequest $request, string $type, $id): JsonResponse
    {
        $commentable = $this->resolveCommentable($type, $id);

        $comment = $commentable->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->validated('content'),
        ]);

        return (new CommentResource($comment->load('user.image')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Comment $comment): CommentResource
    {
        return new CommentResource($comment->load('user.image'));
    }

    public function update(UpdateCommentRequest $request, Comment $comment): CommentResource
    {
        $this->authorize('update', $comment);
        $comment->update($request->validated());
        return new CommentResource($comment->load('user.image'));
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);
        $comment->delete();
        return response()->json(null, 204);
    }
}
