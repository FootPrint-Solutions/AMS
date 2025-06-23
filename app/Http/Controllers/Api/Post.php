<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Publish\Post\PostModel;

class Post extends Controller
{
    /**
     * Get all posts.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllPost()
    {
        try {
            $posts = PostModel::all();
            return response()->json([
                'status' => 'success',
                'message' => 'Data ditemukan',
                'data' => $posts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan',
                'data' => null
            ], 500);
        }
    }

    /**
     * Get a post by its slug.
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPostBySlug($slug)
    {
        try {
            $post = PostModel::where('slug', $slug)->first();

            if (!$post) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Post tidak ditemukan',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data ditemukan',
                'data' => $post
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan',
                'data' => null
            ], 500);
        }
    }
}
