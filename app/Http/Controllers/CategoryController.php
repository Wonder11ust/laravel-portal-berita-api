<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\CategoryService;

class CategoryController extends Controller
{
   protected $categoryService;
   public function __construct(CategoryService $categoryService)
   {
      $this->categoryService = $categoryService;
   }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $categories = $this->categoryService->getAllCategories($perPage);
    
        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        $category = $this->categoryService->addCategory($request->validated());

        return response()->json([
            'status'  => 200,
            'message' => 'Berhasil Menambahkan Kategori',
            'data'    => new CategoryResource($category)
        ]);    
    }

    
    public function show($id)
    {
        $category = $this->categoryService->getCategory($id);

        if (is_null($category)) {
            return response()->json([
                'message' => 'Kategori tidak ditemukan',
                'data' => []
            ], 404);
        }

        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = $this->categoryService->updateCategory($id, $request->validated());

        if (is_null($category)) {
            return response()->json([
                'message' => 'Kategori tidak ditemukan',
                'data' => []
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil Memperbarui Kategori',
            'data' => new CategoryResource($category)
        ]);
    }

   
    public function destroy($id)
    {
        $deleted =  $this->categoryService->deleteCategory($id);

        if(!$deleted){
            return response()->json([
                'message' => 'Kategori tidak ditemukan',
                'data' => []
            ], 404);
        }
       
        return response()->json([
            'status'  => 200,
            'message' => 'Berhasil Menghapus Kategori'
        ]);
    }
}
