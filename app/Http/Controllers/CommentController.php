<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Services\Comment\CreateComment;
use App\Services\Comment\DeleteComment;
use App\Services\Comment\ListComments;
use App\Services\Comment\ShowComment;
use App\Services\Comment\UpdateComment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class CommentController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request, ListComments $service)
    {
        return $this->execute(function () use ($request, $service) {
            $perPage = $request->input('per_page', 10);
            $comments = $service->list($perPage);
            return response()->json($comments);
        });
    }

    public function store(Request $request, CreateComment $service)
    {
        return $this->execute(function () use ($request, $service) {
            $this->authorize('create', Comment::class);

            $data = $request->validate(Comment::createRules());

            $model = $data['commentable_type']::findOrFail($data['commentable_id']);
            $content = $data['content'];

            $comment = $service->create($model, $content);
            return response()->json($comment, 201);
        });
    }

    public function show(Comment $comment, ShowComment $service)
    {
        return $this->execute(function () use ($comment, $service) {
            $comment = $service->show($comment->id);
            return response()->json($comment);
        });
    }

    public function update(Request $request, Comment $comment, UpdateComment $service)
    {
        return $this->execute(function () use ($request, $comment, $service) {
            $this->authorize('update', $comment);

            $data = $request->validate([
                'content' => 'required|string|max:255',
            ]);
            $content = $data['content'];

            $updatedComment = $service->update($comment->id, $content);
            return response()->json($updatedComment);
        });
    }

    public function destroy(Comment $comment, DeleteComment $service)
    {
        return $this->execute(function () use ($comment, $service) {
            $this->authorize('delete', $comment);
            $response = $service->delete($comment->id);
            return response()->json($response);
        });
    }
}
