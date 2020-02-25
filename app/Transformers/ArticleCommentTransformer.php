<?php


namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\ArticleComment;

class ArticleCommentTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['user'];

    public function transform(ArticleComment $articleComment)
    {
        return [
            'id' => $articleComment->id,
            'user_id' => $articleComment->user_id,
            'article_id' => $articleComment->article_id,
            'content' => $articleComment->content,
            'created_at' => (string) $articleComment->created_at,
            'updated_at' => (string) $articleComment->updated_at,
        ];
    }

    public function includeUser(ArticleComment $articleComment)
    {
        return $this->item($articleComment->user, new UserTransformer());
    }
}
