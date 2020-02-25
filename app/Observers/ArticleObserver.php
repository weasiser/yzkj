<?php

namespace App\Observers;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Support\Facades\Log;

class ArticleObserver
{
    public function created(Article $article)
    {
        $this->increaseOrDecreaseArticleCategoryPublishCount($article, 'increase');
    }

    public function updating(Article $article)
    {
        if ($article->isDirty('article_category_id')) {
            //历史数据
            $original_article_category_id = $article->getOriginal('article_category_id');
            ArticleCategory::where('id', $original_article_category_id)->decrement('publish_count');
            //新数据
            $article->articleCategory->increment('publish_count');
        }
    }

//    public function updated(Article $article)
//    {
//        $this->increaseOrDecreaseArticleCategoryPublishCount($article, 'increase');
//    }

    public function deleted(Article $article)
    {
        $this->increaseOrDecreaseArticleCategoryPublishCount($article, 'decrease');
    }

    protected function increaseOrDecreaseArticleCategoryPublishCount($article, $action)
    {
        $articleCategory = $article->articleCategory;
        if ($action === 'increase') {
            $articleCategory->increment('publish_count');
        } elseif ($action === 'decrease') {
            $articleCategory->decrement('publish_count');
        }
    }
}
