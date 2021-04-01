<?php


namespace App\Transformers;

use App\Models\RefundOrderFeedback;
use League\Fractal\TransformerAbstract;

class RefundOrderFeedbackTransformer extends TransformerAbstract
{
    public function transform(RefundOrderFeedback $refundOrderFeedback)
    {
        return [
            'id'         => $refundOrderFeedback->id,
            'order_id'   => $refundOrderFeedback->order_id,
            'picture'    => $refundOrderFeedback->picture,
            'is_handled' => $refundOrderFeedback->is_handled,
            'created_at' => (string) $refundOrderFeedback->created_at,
            'updated_at' => (string) $refundOrderFeedback->updated_at,
        ];
    }
}
