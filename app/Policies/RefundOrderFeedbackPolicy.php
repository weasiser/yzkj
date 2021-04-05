<?php

namespace App\Policies;

use App\Models\RefundOrderFeedback;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RefundOrderFeedbackPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function own(User $user, RefundOrderFeedback $refundOrderFeedback)
    {
        return $user->id === $refundOrderFeedback->order->user_id;
    }
}
