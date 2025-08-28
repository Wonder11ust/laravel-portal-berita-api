<?php
namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use App\Repositories\CategoryRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function addCategory(array $data)
    {
        try {
            return $this->categoryRepository->create($data);
        } catch (\Exception $e) {
            Log::error("Gagal menambahkan kategori: " . $e->getMessage());
            throw new Exception("Gagal menambahkan kategori: " . $e->getMessage(), 500);
        }
    }

    public function getAllCategories($perPage = 10)
    {
        try {
            return $this->categoryRepository->all($perPage);
        } catch (Exception $e) {
            Log::error("Gagal ambil data kategori: " . $e->getMessage());
            throw new Exception("Gagal mengambil data kategori", 500);
        }
    }

 public function getCategory($id)
{
    try {
        return $this->categoryRepository->findById($id);
    } catch (ModelNotFoundException $e) {
        return null; 
    } catch (\Exception $e) {
        Log::error("Gagal mengambil kategori: " . $e->getMessage());
        throw new Exception("Terjadi kesalahan saat mengambil kategori", 500);
    }
}

    public function updateCategory($id,array $data)
    {
        $category = $this->categoryRepository->findById($id);

        if(!$category){
            return null;
        }
        return $this->categoryRepository->update($category,$data);
    }

    public function deleteCategory($id)
    {
        try {
            $category = $this->categoryRepository->findById($id);
            if (!$category) {
               return null;
            }
            return $this->categoryRepository->delete($id);
        } catch (\Exception $e) {
           throw new Exception("Kategori tidak ditemukan", 404);
        }
    }
}