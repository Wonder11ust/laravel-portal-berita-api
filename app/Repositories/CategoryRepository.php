<?php

namespace App\Repositories;
use App\Models\Category;

class CategoryRepository
{
    public function create(array $data):Category
    {
        return Category::create($data);
    }

    public function all($perPage = 10)
    {
        return Category::active()->with('posts')->paginate($perPage);
    }   

    public function findById($id)
    {
        return Category::active()->find($id);
    }

    public function update(Category $category, array $data)
    {
        $category->update($data);
        return $category;
    }

    public function delete($id)
    {
        $category = Category::active()->findOrFail($id);
        return $category->delete();
    }

}