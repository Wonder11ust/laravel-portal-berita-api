<?php

namespace App\Repositories;
use App\Models\Post;

class PostRepository
{
    public function create(array $data):Post
    {
        return Post::create($data);
    }

    public function all($perPage = 10)
    {
        return Post::active()->with(['category', 'user'])->latest()->paginate($perPage);
    }   

    public function findById($id)
    {
      
        return Post::active()->with(['category', 'user'])->find($id);
    }

    public function update(Post $post, array $data)
    {
        $post->update($data);
        return $post;
    }

    public function delete($id)
    {
        $post = Post::active()->findOrFail($id);
        return $post->delete();
    }

}