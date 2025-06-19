<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Http\Resources\Comment\CommentResource;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('index');
    }

    public function index(Model $commentable): AnonymousResourceCollection
    {
        $comments = $commentable->comments()->with('user.image')->latest()->paginate();
        return CommentResource::collection($comments);
    }

    public function store(StoreCommentRequest $request, Model $commentable): JsonResponse
    {
        $comment = $commentable->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->validated('content'),
        ]);
        return (new CommentResource($comment->load('user.image')))->response()->setStatusCode(201);
    }

    public function show(Comment $comment): CommentResource
    {
        return new CommentResource($comment->load('user.image'));
    }

    public function update(UpdateCommentRequest $request, Comment $comment): CommentResource
    {
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
