<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ArticleCommentRequest;
use App\Models\Article;
use App\Models\ArticleComment;
use App\Transformers\ArticleCommentTransformer;

class ArticleCommentsController extends Controller
{
    public function store(ArticleCommentRequest $request, Article $article, ArticleComment $articleComment, ArticleCommentTransformer $articleCommentTransformer)
    {
        $checkResult = $this->checkText($request->input('content'));
        if ($checkResult['errcode'] !== 0) {
            return $checkResult;
        }
        $articleComment->fill($request->all());
        $articleComment->article()->associate($article);
        $articleComment->user()->associate($this->user());
        $articleComment->save();

        return $this->response->item($articleComment, $articleCommentTransformer)->setStatusCode(201);
    }

    public function destroy(Article $article, ArticleComment $articleComment)
    {
        if ($articleComment->article_id !== $article->id) {
            return $this->response->errorBadRequest();
        }

        $this->authorize('destroy', $articleComment);
        $articleComment->delete();

        return $this->response->noContent();
    }

    protected function checkText($content)
    {
        $miniProgram = \EasyWeChat::miniProgram();

        $result = $miniProgram->content_security->checkText($content);

        return $result;
    }
}
