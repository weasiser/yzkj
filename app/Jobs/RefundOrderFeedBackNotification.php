<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RefundOrderFeedBackNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deleteWhenMissingModels = true;

    protected $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $app = \EasyWeChat::miniProgram();
        $app->subscribe_message->send([
            'touser' => 'o8SDs4lKxsGonxkygndFKZxlCb-o',
            'template_id' => 'ObLdKbNrCk7L1ANMB6L5X96IKRoAZw4yxZ2f0iG_M5Y',
            'page' => 'pages/order_detail/order_detail?id=570',
            'data' => [
                'character_string4' => 'SDSD32326SD2551',
                'thing1' => '泰国小菠萝*1 金枕榴莲*2',
                'amount2' => '¥198.96',
                'date3' => '2019-11-1 10:13:24',
                'date5' => '2019-11-1 12:13:28',
            ],
        ]);
    }
}
