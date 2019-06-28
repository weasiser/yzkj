<?php

namespace App\Transformers;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
    public function transform(Product $product)
    {
        return [
            'id'                       => $product->id,
            'title'                    => $product->title,
            'image'                    => config('filesystems.disks.oss.cdnDomain') ? config('filesystems.disks.oss.cdnDomain') . '/' . $product->image : Storage::disk(config('admin.upload.disk'))->url($product->image),
            'buying_price'             => (float) $product->buying_price,
            'selling_price'            => (float) $product->selling_price,
            'quality_guarantee_period' => $product->quality_guarantee_period,
            'total_stock'              => $product->total_stock,
            'min_expiration_date'      => $product->min_expiration_date,
//            'created_at' => (string) $product->created_at,
//            'updated_at' => (string) $product->updated_at,
        ];
    }
}
