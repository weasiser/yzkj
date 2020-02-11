<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title',
        'author',
        'body',
    ];

    public function articleCategory()
    {
        return $this->belongsTo(ArticleCategory::class);
    }
}
