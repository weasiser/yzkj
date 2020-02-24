<?php

namespace App\Http\Controllers\Api;

use App\Models\ArticleCategory;
use App\Transformers\ArticleCategoryTransformer;
use Illuminate\Http\Request;

class ArticleCategoriesController extends Controller
{
    public function index(ArticleCategory $articleCategory, ArticleCategoryTransformer $articleCategoryTransformer)
    {
        return $this->response->collection(ArticleCategory::all(), $articleCategoryTransformer);
    }
}
