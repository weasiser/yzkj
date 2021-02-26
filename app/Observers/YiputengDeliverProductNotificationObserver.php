<?php

namespace App\Observers;

use App\Models\YiputengDeliverProductNotification;
use GuzzleHttp\Client;

class YiputengDeliverProductNotificationObserver
{
    public function created(YiputengDeliverProductNotification $yiputengDeliverProductNotification)
    {
        $this->timeoutCheck($yiputengDeliverProductNotification);
    }

    protected function timeoutCheck(YiputengDeliverProductNotification $yiputengDeliverProductNotification)
    {
        dispatch(function () use ($yiputengDeliverProductNotification) {
            if ($yiputengDeliverProductNotification->result === 'DELIVERING') {
                $yiputengDeliverProductNotification->result = 'TIMEOUT';
                $yiputengDeliverProductNotification->update();
                $http = new Client();
                $http->post('https://www.yzkj01.com/notice/yiputengDeliverResult', [
                    'json' => [
                        'out_trade_no' => $yiputengDeliverProductNotification->out_trade_no,
                        'trade_status' => 'TIMEOUT'
                    ]
                ]);
            }
        })->delay(now()->addSeconds(300));
    }
}
