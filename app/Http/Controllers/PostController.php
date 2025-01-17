<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = auth()->user()->posts()->latest()->get();
        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        $post = auth()->user()->posts()->create($validated);

        return response()->json($post, 201);
    }

    public function show(string $id)
    {
        $post = auth()->user()->posts()->find($id);

        if (!$post) {
            // security: avoid descriptive error message
            return response()->json([
                'message' => 'Post not found'
            ], 404);
        }

        return response()->json($post);
    }

    public function update(Request $request, string $id)
    {
        $post = auth()->user()->posts()->find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string'
            ]);

            $post->update($validated);

            return response()->json($post);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating post',
                'errors' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy(string $id)
    {
        $post = auth()->user()->posts()->find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found'
            ], 404);
        }

        try {
            $post->delete();
            return response()->json([
                'message' => 'Post deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting post',
                'errors' => $e->getMessage()
            ], 500);
        }
    }
}
