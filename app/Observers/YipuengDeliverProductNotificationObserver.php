<?php

namespace App\Observers;

use App\Models\YipuengDeliverProductNotification;

class YipuengDeliverProductNotificationObserver
{
    public function created(YipuengDeliverProductNotification $yipuengDeliverProductNotification)
    {
        $this->timeoutCheck($yipuengDeliverProductNotification);
    }

    protected function timeoutCheck(YipuengDeliverProductNotification $yipuengDeliverProductNotification)
    {
        dispatch(function () use ($yipuengDeliverProductNotification) {
            if ($yipuengDeliverProductNotification->result === 'DELIVERING') {
                $yipuengDeliverProductNotification->result = 'TIMEOUT';
                $yipuengDeliverProductNotification->update();
            }
        })->delay(now()->addSeconds(30));
    }
}
