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
                'title',
                'slug',
                'excerpt',
                'content',
                'category_id',
                'status',
                'published_at'
            ]);
            $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();

            if ($request->hasFile('featured_image')) {
                $image = $request->file('featured_image');
                $filename = uniqid() . '_' . time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('storage/image/post/');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $image->move($destinationPath, $filename);
                $data['featured_image'] = $filename;
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

        $data = PostModel::allForDataTables($request);

        $rows = [];
        $no = $start + 1;
        foreach ($data['row'] as $post) {
            $row = [];
            $row[] = $no++;
            $row[] = $post->title;
            $row[] = $post->category_name;
            $row[] = $post->tags->pluck('name')->implode(', ');
            $row[] = $post->featured_image
                ? '<img src="' . asset('storage/image/post/' . $post->featured_image) . '" alt="Featured" width="50" height="50" onerror="this.onerror=null;this.src=\'https://placehold.co/50x50\'">'
                : 'No Image';
            $row[] = $post->status ? 'Published' : 'Draft';
            $row[] = $post->id;
            $rows[] = $row;
        }

        return response()->json([
            "draw" => $draw,
            "recordsTotal" => $data['count'],
            "recordsFiltered" => $data['count'],
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
                'title',
                'slug',
                'excerpt',
                'content',
                'category_id',
                'status',
                'published_at'
            ]);
            $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
            $data['updated_by'] = Auth::id();

            if ($request->hasFile('featured_image')) {
                $image = $request->file('featured_image');
                $filename = uniqid() . '_' . time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('storage/image/post/');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $image->move($destinationPath, $filename);
                $data['featured_image'] = $filename;
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

    public function storeCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:post_categories,name',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category = PostCategoryModel::create([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
            'status' => $request->input('status'),
            'created_by' => Auth::id(),
        ]);

        $category = PostCategoryModel::where('status', 1)->get()->toArray();

        return getResponseData(true, "The new category was successfully created!", $category);
    }

    public function storeTag(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:post_tags,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tag = PostTagModel::create([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
            'created_by' => Auth::id(),
        ]);

        $tag = PostTagModel::all()->toArray();

        return getResponseData(true, "The new tag was successfully created!", $tag);
    }
}
