<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Information extends Model
{
    protected $fillable = [
        'contact',
        'contact_info',
        'location',
        'summary',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
