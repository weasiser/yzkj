<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Transformers\ArticleTransformer;
use Illuminate\Http\Request;

class ArticlesController extends Controller
{
    public function index(Article $article, ArticleTransformer $articleTransformer)
    {
        $articleList = $article->recent()->paginate(15);
        return $this->response->paginator($articleList, $articleTransformer);
    }

    public function show(Article $article, ArticleTransformer $articleTransformer)
    {
        return $this->response->item($article, $articleTransformer);
    }
}
