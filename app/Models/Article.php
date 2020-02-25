<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title',
        'author',
        'banner',
        'body',
    ];

    public function articleCategory()
    {
        return $this->belongsTo(ArticleCategory::class);
    }

    public function articleComments()
    {
        return $this->hasMany(ArticleComment::class);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('id', 'desc');
    }
}
