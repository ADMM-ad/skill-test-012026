<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;

class PostController extends Controller
{
    /**
     * 4-1. posts.index
     * Get paginated active posts
     */
    public function index()
{
    $posts = Post::active()
        ->with('user')
        ->paginate(20);

    return PostResource::collection($posts);
}

    /**
     * 4-2. posts.create
     * Auth only
     */
    public function create(): string
    {
        return 'posts.create';
    }

    /**
     * 4-3. posts.store
     * Auth only
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        $post = $request->user()->posts()->create($request->validated());

        return response()->json([
            'message' => 'Post created successfully',
            'data' => $post,
        ], 201);
    }

    /**
     * 4-4. posts.show
     */
    public function show(Post $post)
{
    $post = Post::active()
        ->with('user')
        ->findOrFail($post->id);

    return new PostResource($post);
}

    /**
     * 4-5. posts.edit
     * Only author
     */
    public function edit(Post $post): string
    {
        $this->authorize('update', $post);

        return 'posts.edit';
    }

    /**
     * 4-6. posts.update
     * Only author
     */
    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        $this->authorize('update', $post);

        $post->update($request->validated());

        return response()->json([
            'message' => 'Post updated successfully',
            'data' => $post,
        ]);
    }

    /**
     * 4-7. posts.destroy
     * Only author
     */
    public function destroy(Post $post): JsonResponse
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully',
        ]);
    }
}
