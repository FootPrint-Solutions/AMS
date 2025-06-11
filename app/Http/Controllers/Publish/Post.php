<?php

namespace App\Http\Controllers\Publish;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Publish\Post\PostModel;
use App\Models\Publish\Post\PostCategoryModel;
use App\Models\Publish\Post\PostTagModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class Post extends Controller
{
    private $title = "Post";

    public function index()
    {
        return view(
            "Publish.Post.index",
            getIndexData($this->title)
        );
    }

    public function create()
    {
        $categories = PostCategoryModel::where('status', 1)->get();
        $tags = PostTagModel::all();
        $data = [
            'categories' => $categories,
            'tags' => $tags,
        ];
        return view(
            "Publish.Post.create",
            getIndexData($this->title, $data)
        );
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:posts,slug',
                'excerpt' => 'nullable|string',
                'content' => 'required|string',
                'category_id' => 'required|exists:post_categories,id',
                'featured_image' => 'nullable|image',
                'status' => 'required|boolean',
                'tags' => 'nullable|array',
                'tags.*' => 'exists:post_tags,id',
                'published_at' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $request->only([
                'title', 'slug', 'excerpt', 'content', 'category_id', 'status', 'published_at'
            ]);
            $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();

            if ($request->hasFile('featured_image')) {
                $image = $request->file('featured_image');
                $path = $image->store('posts', 'public');
                $data['featured_image'] = basename($path);
            }

            $post = PostModel::create($data);

            if ($request->filled('tags')) {
                $post->tags()->sync($request->input('tags'));
            }

            DB::commit();
            return getResponseData(true, "The new post was successfully created!");
        } catch (\Throwable $th) {
            DB::rollBack();
            return getResponseData(false, "Failed to create the new post!", $th->getMessage());
        }
    }

    public function show(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start', 0);

        $query = PostModel::with(['category', 'tags', 'creator']);
        $count = $query->count();
        $posts = $query->skip($start)->take($request->input('length', 10))->get();

        $rows = [];
        $no = $start + 1;
        foreach ($posts as $post) {
            $row = [];
            $row[] = $no++;
            $row[] = $post->title;
            $row[] = $post->category ? $post->category->name : '-';
            $row[] = $post->tags->pluck('name')->implode(', ');
            $row[] = $post->featured_image
                ? '<img src="' . asset('storage/posts/' . $post->featured_image) . '" alt="Featured" width="50" height="50" onerror="this.onerror=null;this.src=\'https://placehold.co/50x50\'">'
                : 'No Image';
            $row[] = $post->status ? 'Published' : 'Draft';
            $row[] = $post->id;
            $rows[] = $row;
        }

        return response()->json([
            "draw" => $draw,
            "recordsTotal" => $count,
            "recordsFiltered" => $count,
            "data" => $rows
        ]);
    }

    public function edit($id)
    {
        $post = PostModel::with('tags')->findOrFail($id);
        $categories = PostCategoryModel::where('status', 1)->get();
        $tags = PostTagModel::all();

        return view(
            "Publish.Post.create",
            getIndexData(
                $this->title,
                [
                    'post' => $post,
                    'categories' => $categories,
                    'tags' => $tags,
                ]
            )
        );
    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:posts,id',
                'title' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:posts,slug,' . $request->input('id'),
                'excerpt' => 'nullable|string',
                'content' => 'required|string',
                'category_id' => 'required|exists:post_categories,id',
                'featured_image' => 'nullable|image',
                'status' => 'required|boolean',
                'tags' => 'nullable|array',
                'tags.*' => 'exists:post_tags,id',
                'published_at' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $post = PostModel::findOrFail($request->input('id'));
            $data = $request->only([
                'title', 'slug', 'excerpt', 'content', 'category_id', 'status', 'published_at'
            ]);
            $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
            $data['updated_by'] = Auth::id();

            if ($request->hasFile('featured_image')) {
                $image = $request->file('featured_image');
                $path = $image->store('posts', 'public');
                $data['featured_image'] = basename($path);
            }

            $post->update($data);

            if ($request->filled('tags')) {
                $post->tags()->sync($request->input('tags'));
            } else {
                $post->tags()->detach();
            }

            DB::commit();
            return getResponseData(true, "The post was successfully updated!");
        } catch (\Throwable $th) {
            DB::rollBack();
            return getResponseData(false, "Failed to update the post!", $th->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $ids = $request->input('id');
            $posts = PostModel::whereIn('id', $ids)->get();
            foreach ($posts as $post) {
                $post->deleted_by = Auth::id();
                $post->save();
                $post->delete();
            }
            DB::commit();
            return getResponseData(true, "The selected posts were successfully deleted!");
        } catch (\Throwable $th) {
            DB::rollBack();
            return getResponseData(false, "Failed to delete the selected posts!", $th->getMessage());
        }
    }
}
