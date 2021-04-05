<?php


namespace App\Transformers;

use App\Models\RefundOrderFeedback;
use Illuminate\Support\Facades\Storage;
use League\Fractal\TransformerAbstract;

class RefundOrderFeedbackTransformer extends TransformerAbstract
{
    public function transform(RefundOrderFeedback $refundOrderFeedback)
    {
        if ($refundOrderFeedback->picture) {
            foreach ($refundOrderFeedback->picture as $key => $value) {
                $pictures[$key] = config('filesystems.disks.oss.cdnDomain') ? config('filesystems.disks.oss.cdnDomain') . '/' . $value . '-refundOrderFeedback' : Storage::disk(config('admin.upload.disk'))->url($value);
                $originalPictures[$key] = config('filesystems.disks.oss.cdnDomain') ? config('filesystems.disks.oss.cdnDomain') . '/' . $value : Storage::disk(config('admin.upload.disk'))->url($value);
            }
        }

        return [
            'id'         => $refundOrderFeedback->id,
            'order_id'   => $refundOrderFeedback->order_id,
            'user_id' => $refundOrderFeedback->order->user_id,
            'refund_status' => $refundOrderFeedback->order->refund_status,
            'content' => $refundOrderFeedback->content,
            'pictures'    => isset($pictures) ? $pictures : null,
            'originalPictures' => isset($originalPictures) ? $originalPictures : null,
            'is_handled' => $refundOrderFeedback->is_handled,
            'created_at' => (string) $refundOrderFeedback->created_at,
            'updated_at' => (string) $refundOrderFeedback->updated_at,
        ];
    }
}
