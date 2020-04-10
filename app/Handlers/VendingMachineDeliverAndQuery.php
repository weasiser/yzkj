<?php


namespace App\Handlers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class VendingMachineDeliverAndQuery
{
    public function deliverProduct($machineId, $orderNo, $latticeId, $cabid = 1, $cabtype = '1')
    {
        if (Cache::store('redis')->has('huiyijie_access_token')) {
            $access_token = Cache::store('redis')->get('huiyijie_access_token');
        } else {
            $token = $this->getAccessToken();
            if ($token['result'] === '200') {
                $access_token = $token['data'];
            } else {
                return $token;
            }
        }

        $http = new Client();
        $deliverProductApi = config('services.huiyijie_vending_machine.deliver_product_api');

        $params = [
            'goodslist' => [
                [
                    'cabid' => (int)$cabid,
                    'cabtype' => (string)$cabtype,
                    'latticeId' => (string)$latticeId
                ]
            ],
            'machineId' => (string)$machineId,
            'orderid' => (string)$orderNo
        ];

        $response = $http->post($deliverProductApi, [
            'headers' => [
                'Authorization' => $access_token
            ],
            'json' => $params
        ]);

        $result = json_decode($response->getBody(), true);

        if ($result['result'] === '406') {
            $token = $this->getAccessToken();
            if ($token['result'] === '200') {
                $access_token = $token['data'];
                $response = $http->post($deliverProductApi, [
                    'headers' => [
                        'Authorization' => $access_token
                    ],
                    'json' => $params
                ]);
                $result = json_decode($response->getBody(), true);
                return $result;
            } else {
                return $token;
            }
        } elseif ($result['result'] === '200') {
            return $result;
        } else {
            return $result;
        }
    }

    public function queryMachineInfo($machineUuid = '')
    {
        if (Cache::store('redis')->has('huiyijie_access_token')) {
            $access_token = Cache::store('redis')->get('huiyijie_access_token');
        } else {
            $token = $this->getAccessToken();
            if ($token['result'] === '200') {
                $access_token = $token['data'];
            } else {
                return $token;
            }
        }

        $http = new Client();
        $queryMachineInfoApi = config('services.huiyijie_vending_machine.query_machine_info');

        $response = $http->get($queryMachineInfoApi, [
            'headers' => [
                'Authorization' => $access_token
            ],
            'query' => ['machineUuid' => $machineUuid]
        ]);

        $result = json_decode($response->getBody(), true);

        dd($result);

        if ($result['result'] === '406') {
            $token = $this->getAccessToken();
            if ($token['result'] === '200') {
                $access_token = $token['data'];
                $response = $http->get($queryMachineInfoApi, [
                    'headers' => [
                        'Authorization' => $access_token
                    ],
                    'query' => ['machineUuid' => $machineUuid]
                ]);
                $result = json_decode($response->getBody(), true);
                return $result;
            } else {
                return $token;
            }
        } elseif ($result['result'] === '200') {
            return $result;
        } else {
            return $result;
        }
    }

    public function getAccessToken()
    {
        $http = new Client();

        $getAccessTokenApi = config('services.huiyijie_vending_machine.get_access_token_api');
        $userName = config('services.huiyijie_vending_machine.user_name');
        $password = config('services.huiyijie_vending_machine.password');

        $query = [
            'userName' => $userName,
            'password' => $password
        ];

        $response = $http->get($getAccessTokenApi, [
            'query' => $query
        ]);

        $result = json_decode($response->getBody(), true);

        if ($result['result'] === '200' && $result['data']) {
            Cache::store('redis')->put('huiyijie_access_token', $result['data'], now()->addDays(7));
        }

        return $result;
    }
}
