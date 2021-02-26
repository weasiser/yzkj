<?php

namespace App\Observers;

use App\Models\YiputengDeliverProductNotification;

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
            }
        })->delay(now()->addSeconds(30));
    }
}
