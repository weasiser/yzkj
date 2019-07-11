<?php


namespace App\Handlers;

use GuzzleHttp\Client;

class VendingMachineDeliverAndQuery
{
    public function deliverProduct($machineId, $orderid, $latticeId, $cabid = 1, $cabtype = '1')
    {
        $http = new Client();

        $token = $this->getAccessToken();

        $deliverProductApi = config('services.huiyijie_vending_machine.deliver_product_api');

        $form_params = [
            'goodslist' => [
                [
                    'cabid' => (int)$cabid,
                    'cabtype' => (string)$cabtype,
                    'latticeId' => (string)$latticeId
                ]
            ],
            'machineId' => (string)$machineId,
            'orderid' => (string)$orderid
        ];

        $response = $http->post($deliverProductApi, [
            'headers' => ['Authorization' => $token],
            'form_params' => $form_params
        ]);

        $result = json_decode($response->getBody(), true);

        return $result;
    }

    public function queryMachineInfo($machineUuid = '')
    {
        $http = new Client();

        $token = $this->getAccessToken();

        $queryMachineInfoApi = config('services.huiyijie_vending_machine.query_machine_info');

        $response = $http->get($queryMachineInfoApi, [
            'headers' => ['Authorization' => $token],
            'query' => ['machineUuid' => $machineUuid]
        ]);

        $result = json_decode($response->getBody(), true);

        return $result;
    }

    protected function getAccessToken()
    {
        $http = new Client();

        $getAccessTokenApi = config('services.huiyijie_vending_machine.get_access_token_api');
        $userName = config('services.huiyijie_vending_machine.user_name');
        $password = config('services.huiyijie_vending_machine.password');

        $query = http_build_query([
            'userName' => $userName,
            'password' => $password
        ]);

        $response = $http->get($getAccessTokenApi . $query);

        $result = json_decode($response->getBody(), true);

        return $result['data'];
    }
}
