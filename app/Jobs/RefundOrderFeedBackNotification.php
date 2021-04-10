<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\SubscribeMessage;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class RefundOrderFeedBackNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deleteWhenMissingModels = true;

    protected $order, $app, $user, $template_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order, User $user, $template_id)
    {
        $this->order = $order;
        $this->user = $user;
        $this->template_id = $template_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $messageCard = [
            'touser' => $this->user->weapp_openid,
            'template_id' => $this->template_id,
            'page' => 'pages/order_refund_list/order_refund_list',
            'data' => [
                'character_string4' => [
                    'value' => $this->order->no
                ],
                'thing1' => [
                    'value' => $this->order->amount. '件 ' . $this->order->product->title
                ],
                'amount2' => [
                    'value' => '¥' . $this->order->total_amount
                ],
                'date3' => [
                    'value' => (string)$this->order->paid_at
                ],
                'date5' => [
                    'value' => (string)$this->order->updated_at
                ],
            ],
        ];
        $app = \EasyWeChat::miniProgram();
        $app->subscribe_message->send($messageCard);
        if ($subscribeMessage = $this->user->subscribeMessages->where('template_id', $this->template_id)->first()) {
            $subscribeMessage->delete();
        }
    }
}
