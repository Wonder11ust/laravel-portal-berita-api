<?php
namespace App\Services;

use App\Events\PostCreated;
use Exception;
use App\Models\User;
use App\Mail\NewPostNotification;

use Illuminate\Support\Facades\Log;
use App\Repositories\PostRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;

class PostService
{
    protected $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function addArticle(array $data)
    {
        try {
            $data['user_id'] = Auth::user()->id;
            $post = $this->postRepository->create($data);
            if(request()->hasFile('cover')){
                $post->addMedia(request()->file('cover'))->toMediaCollection('cover');
            }

             Cache::forget("posts.page.10");
             Cache::forget("posts.latest");
            event(new PostCreated($post));
            return $post;
        } catch (\Exception $e) {
            Log::error("Gagal menambahkan artikel: " . $e->getMessage());
            throw new Exception("Gagal menambahkan artikel: " . $e->getMessage(), 500);
        }
    }

    public function getAllArticles($perPage = 10)
    {
        try {
            $page = request()->get('page', 1);  
            $cacheKey = "posts.page.$perPage.$page";

            return Cache::remember($cacheKey,60, function() use ($perPage){
               return $this->postRepository->all($perPage);
            });
            
        } catch (Exception $e) {
            Log::error("Gagal ambil data artikel: " . $e->getMessage());
            throw new Exception("Gagal mengambil data artikel", 500);
        }
    }

    public function getArticle($id)
    {
        try {
            $cacheKey = "post.$id";
            return Cache::remember($cacheKey,60, function() use ($id){
                return $this->postRepository->findById($id);
            });
        } catch (ModelNotFoundException $e) {
            return null; 
        } catch (\Exception $e) {
            Log::error("Gagal mengambil kategori: " . $e->getMessage());
            throw new Exception("Terjadi kesalahan saat mengambil artikel", 500);
        }
    }

    public function updateArticle($id, array $data)
    {
        $post = $this->postRepository->findById($id);

        if (!$post) {
            return null;
        }
    
        $post = $this->postRepository->update($post, $data);

        if (request()->hasFile('cover')) {
            $post->clearMediaCollection('cover'); 
            $post->addMediaFromRequest('cover')->toMediaCollection('cover');
        }

        return $post;
    }


public function deleteArticle($id)
{
    try {
        $post = $this->postRepository->findById($id);

        if (!$post) {
            return null;
        }
        
        $post->clearMediaCollection('cover'); // hapus file fisik + record media
        return $this->postRepository->delete($id);
    } catch (\Exception $e) {
        throw new Exception("Artikel tidak ditemukan", 404);
    }
}
}