<?php


namespace App\Transformers;

use App\Models\Article;
use Illuminate\Support\Facades\Storage;
use League\Fractal\TransformerAbstract;

class ArticleTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['articleCategory', 'articleComments'];

    public function transform(Article $article)
    {
        return [
            'id'                  => $article->id,
            'title'               => $article->title,
            'author'              => $article->author,
            'article_category_id' => $article->article_category_id,
            'banner'              => config('filesystems.disks.oss.cdnDomain') ? config('filesystems.disks.oss.cdnDomain') . '/' . $article->banner . '-articleBanner' : Storage::disk(config('admin.upload.disk'))->url($article->banner),
            'big_banner'              => config('filesystems.disks.oss.cdnDomain') ? config('filesystems.disks.oss.cdnDomain') . '/' . $article->banner . '-article' : Storage::disk(config('admin.upload.disk'))->url($article->banner),
            'body'                => $article->body,
            'comment_count'       => $article->comment_count,
            'visit_count'         => $article->visit_count,
            'created_at'          => (string) $article->created_at,
            'updated_at'          => (string) $article->updated_at,
        ];
    }

    public function includeArticleCategory(Article $article)
    {
        return $this->item($article->articleCategory, new ArticleCategoryTransformer());
    }

    public function includeArticleComments(Article $article)
    {
        return $this->collection($article->articleComments, new ArticleCommentTransformer());
    }
}
