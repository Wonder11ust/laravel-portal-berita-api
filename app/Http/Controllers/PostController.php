<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Services\PostService;
use App\Http\Resources\PostResource;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;
   protected $postService;
   public function __construct(PostService $postService)
   {
    $this->postService = $postService;
   }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $posts = $this->postService->getAllArticles($perPage);

        if($posts->count() == 0) {
            return response()->json([
                'message' => 'Artikel tidak ditemukan',
                'data' => []
            ], 404);
        }
    
        return PostResource::collection($posts)->additional([
            'status'  => 200,
            'message' => 'Berhasil mengambil artikel'
        ]);
    }

    public function store(StorePostRequest $request)
    {
        $post = $this->postService->addArticle($request->validated());

        return response()->json([
            'status'  => 200,
            'message' => 'Berhasil Menambahkan Artikel',
            'data'    => new PostResource($post)
        ]);    
    }

    public function show($id)
    {
        $post = $this->postService->getArticle($id);

        if(is_null($post)) {
            return response()->json([
                'message' => 'Artikel tidak ditemukan',
                'data' => []
            ], 404);
        }

        return response()->json([
            'status'  => 200,
            'message' => 'Detail Artikel',
            'data'    => new PostResource($post)
        ]);
    }

    public function update(UpdatePostRequest $request, $id)
    {
        $post = $this->postService->getArticle($id);

        if (!$post) {
            return response()->json(['message' => 'Artikel tidak ditemukan'], 404);
        }

        $this->authorize('update', $post);

        $updatedPost = $this->postService->updateArticle($id, $request->validated());

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil Memperbarui Artikel',
            'data' => new PostResource($updatedPost)
        ]);
    }

    public function destroy($id)
    {
        $post = $this->postService->getArticle($id);

        if (!$post) {
            return response()->json(['message' => 'Artikel tidak ditemukan'], 404);
        }

        $this->authorize('delete', $post);

       $deleted = $this->postService->deleteArticle($id);

       if(!$deleted){
        return response()->json(['message' => 'Artikel sudah terhapus'], 404);
       }

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil Menghapus Artikel'
        ]);
    }

}
