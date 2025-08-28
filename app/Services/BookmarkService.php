<?php

namespace App\Services;

use Exception;
use App\Services\PostService;
use App\Services\UserService;

class BookmarkService
{
    protected $postService;
    protected $userService;

    public function __construct(PostService $postService, UserService $userService)
    {
        $this->postService = $postService;
        $this->userService = $userService;
    }

    public function addBookmark($postId)
    {
        $user = $this->userService->getCurrentUser(); // misalnya wrapper dari Auth::user()
        $post = $this->postService->getArticle($postId);

        if (!$post) {
            throw new Exception('Post tidak ditemukan', 404);
        }

        if ($user->bookmarks()->where('post_id', $postId)->exists()) {
            throw new Exception('Post sudah di-bookmark sebelumnya', 409);
        }

        $user->bookmarks()->attach($postId);
        return true;
    }

    public function removeBookmark($postId)
    {
        $user = $this->userService->getCurrentUser();
        $post = $this->postService->getArticle($postId);

        if (!$post) {
            throw new Exception('Post tidak ditemukan', 404);
        }

        $detached = $user->bookmarks->detach($postId);
        if ($detached === 0) {
            throw new Exception('Bookmark tidak ditemukan untuk user ini', 404);
        }

        return true;
    }

    public function listBookmarks($perPage = 10)
    {
        $user = $this->userService->getCurrentUser();
        return $user->bookmarks()
                    ->whereNull('posts.deleted_at')
                    ->with(['category', 'user'])
                    ->paginate($perPage);
    }
}
