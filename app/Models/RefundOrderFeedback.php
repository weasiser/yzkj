<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundOrderFeedback extends Model
{
    protected $fillable = [
        'content',
        'picture',
        'is_handled'
    ];

    protected $casts = [
        'picture' => 'array',
        'is_handled' => 'boolean'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeRecentUnhandled($query)
    {
        return $query->where('is_handled', false)->orderBy('id', 'desc');
    }
}
