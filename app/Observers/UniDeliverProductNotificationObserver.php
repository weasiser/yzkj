<?php

namespace App\Observers;

use App\Jobs\DeliverProduct;
use App\Models\UniDeliverProductNotification;

class UniDeliverProductNotificationObserver
{
    public function created(UniDeliverProductNotification $uniDeliverProductNotification)
    {
        DeliverProduct::dispatch($uniDeliverProductNotification);
    }
}
