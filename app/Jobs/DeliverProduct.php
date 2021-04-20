<?php

namespace App\Jobs;

use App\Handlers\VendingMachineDeliverAndQuery;
use App\Models\UniDeliverProductNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeliverProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $uniDeliverProductNotification, $machine_api_type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(UniDeliverProductNotification $uniDeliverProductNotification, $machine_api_type)
    {
        $this->uniDeliverProductNotification = $uniDeliverProductNotification;
        $this->machine_api_type = $machine_api_type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->uniDeliverProductNotification->result = 'delivering';
        $this->uniDeliverProductNotification->save();
        if ($this->machine_api_type === 0) {
            $result = app(VendingMachineDeliverAndQuery::class)->deliverProduct($this->uniDeliverProductNotification->machine_id, $this->uniDeliverProductNotification->order_no, $this->uniDeliverProductNotification->aisle_number);
            if ($result['result'] === '200') {
                for ($i = 0; $i < 30; $i++) {
                    sleep(1);
                    $this->uniDeliverProductNotification->refresh();
                    if ($this->uniDeliverProductNotification->result === '1') {
                        $this->delete();
                    } elseif ($this->uniDeliverProductNotification->result === '2' || $this->uniDeliverProductNotification->result === '3') {
                        $this->delete();
                    }
                }
            } else {
                $this->uniDeliverProductNotification->result = '调用出货接口失败';
                $this->uniDeliverProductNotification->save();
            }
        } elseif ($this->machine_api_type === 1) {
            $shelf_id = $this->uniDeliverProductNotification->aisle_number;
            $params['machine_id'] = $this->uniDeliverProductNotification->machine_id;
            $params['trade_no'] = $this->uniDeliverProductNotification->order_no;
            $params['multi_pay'] = '[{' . $shelf_id . ':' . $this->uniDeliverProductNotification->number . '}]';
            $result = app(VendingMachineDeliverAndQuery::class)->payMultiDelivery($params);
            if ($result['code'] === 0) {
                for ($i = 0; $i < 30; $i++) {
                    sleep(1);
                    $this->uniDeliverProductNotification->refresh();
                    if ($this->uniDeliverProductNotification->result === 'SUCCESS') {
                        $this->delete();
                    } elseif ($this->uniDeliverProductNotification->result === 'FAIL') {
                        $this->delete();
                    }
                }
            } else {
                $this->uniDeliverProductNotification->result = '调用出货接口失败';
                $this->uniDeliverProductNotification->save();
            }
        }
        $this->delete();
    }
}
