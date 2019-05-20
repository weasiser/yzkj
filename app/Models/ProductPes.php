<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPes extends Model
{
    protected $fillable = ['production_date', 'expiration_date', 'stock'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
