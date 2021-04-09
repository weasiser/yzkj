<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscribeMessage extends Model
{
    protected $fillable = [
        'template_id',
        'platform'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
