<?php

namespace App\Listeners;

use App\Events\PostCreated;
use App\Services\PostService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class UpdatePostCache
{
   protected $postService;
    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Handle the event.
     */
    public function handle(PostCreated $event): void
    {
        Cache::forget('posts.latest');
        Cache::remember('posts.latest',60,function(){
            return $this->postService->getAllArticles(10);
        });
    }
}
