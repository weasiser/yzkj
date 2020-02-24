<?php


namespace App\Transformers;

use App\Models\ArticleCategory;
use League\Fractal\TransformerAbstract;

class ArticleCategoryTransformer extends TransformerAbstract
{
//    protected $availableIncludes = ['articles'];

    public function transform(ArticleCategory $articleCategory)
    {
        return [
            'id'            => $articleCategory->id,
            'name'          => $articleCategory->name,
            'description'   => $articleCategory->description,
            'publish_count' => $articleCategory->publish_count,
            'created_at'    => (string) $articleCategory->created_at,
            'updated_at'    => (string) $articleCategory->updated_at,
        ];
    }

//    public function includeArticles(ArticleCategory $articleCategory)
//    {
//        return $this->collection($articleCategory->articles, new ArticleTransformer());
//    }
}
