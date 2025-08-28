<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Services\BookmarkService;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    protected $bookmarkService;

    public function __construct(BookmarkService $bookmarkService)
    {
        $this->bookmarkService = $bookmarkService;
    }

    public function store($postId)
    {
        try {
            $this->bookmarkService->addBookmark($postId);
            return response()->json([
                'status' => 200,
                'message' => 'Post berhasil di-bookmark'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => $e->getCode() ?: 500,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    public function destroy($postId)
    {
        $user = Auth::user();
        $post = Post::where('id', $postId)->whereNull('deleted_at')->first();

        if (!$post) {
            return response()->json([
                'status' => 404,
                'message' => 'Post tidak ditemukan'
            ], 404);
        }

        $detached = $user->bookmarks()->detach($postId);

        if ($detached === 0) {
            return response()->json([
                'status' => 404,
                'message' => 'Bookmark tidak ditemukan untuk user ini'
            ], 404);
        }
        

        return response()->json([
            'status' => 200,
            'message' => 'Bookmark berhasil dihapus'
        ]);
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $bookmarks = $this->bookmarkService->listBookmarks($perPage);

        return PostResource::collection($bookmarks)->additional([
            'status' => 200,
            'message' => 'Berhasil mengambil daftar bookmark'
        ]);
    }
}
