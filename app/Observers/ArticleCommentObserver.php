<?php

namespace App\Observers;

use App\Models\ArticleComment;

class ArticleCommentObserver
{
    public function created(ArticleComment $articleComment)
    {
        $this->increaseOrDecreaseArticleCommentCount($articleComment, 'increase');
    }

    public function deleted(ArticleComment $articleComment)
    {
        $this->increaseOrDecreaseArticleCommentCount($articleComment, 'decrease');
    }

    protected function increaseOrDecreaseArticleCommentCount($articleComment, $action)
    {
        $article = $articleComment->article;
        if ($action === 'increase') {
            $article->increment('comment_count');
        } elseif ($action === 'decrease') {
            $article->decrement('comment_count');
        }
    }
}
