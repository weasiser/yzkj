<?php


namespace App\Handlers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

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

    public function queryCommodityInfo($machineUuid)
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
        $queryCommodityInfoApi = config('services.huiyijie_vending_machine.query_commodity_info');

        $response = $http->get($queryCommodityInfoApi, [
            'headers' => [
                'Authorization' => $access_token
            ],
            'query' => ['machineUuid' => $machineUuid]
        ]);

        $result = json_decode($response->getBody(), true);

        if ($result['result'] === '406') {
            $token = $this->getAccessToken();
            if ($token['result'] === '200') {
                $access_token = $token['data'];
                $response = $http->get($queryCommodityInfoApi, [
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
            Cache::store('redis')->put('huiyijie_access_token', $result['data'], now()->addDays(6));
        }

        return $result;
    }

    public function getApiToken()
    {
        $http = new Client();

        $getApiTokenApi = config('services.yiputeng_vending_machine.get_api_token');
        $appKey = config('services.yiputeng_vending_machine.app_key');
        $appSecret = config('services.yiputeng_vending_machine.app_secret');

        $params = [
            'app_key' => (string)$appKey,
            'app_secret' => (string)$appSecret,
            'version' => '2.0'
        ];

        $response = $http->post($getApiTokenApi, [
            'form_params' => $params
        ]);

        $result = json_decode($response->getBody(), true);

        if ($result['code'] === 0) {
            Cache::store('redis')->put('yiputeng_api_token', $result['api_token'], now()->addSeconds(86400));
        }

        return $result;
    }

    public function queryMachineList()
    {
        if (Cache::store('redis')->has('yiputeng_api_token')) {
            $api_token = Cache::store('redis')->get('yiputeng_api_token');
        } else {
            $token = $this->getApiToken();
            if ($token['code'] === 0) {
                $api_token = $token['api_token'];
            } else {
                return $token;
            }
        }

        $http = new Client();

        $queryMachineListApi = config('services.yiputeng_vending_machine.query_machine_list');

        $response = $http->post($queryMachineListApi, [
            'form_params' => [
                'api_token' => $api_token,
                'version' => '1.0'
            ]
        ]);

        $result = json_decode($response->getBody(), true);

        return $result;
    }

    public function queryShelfList($machine_id)
    {
        if (Cache::store('redis')->has('yiputeng_api_token')) {
            $api_token = Cache::store('redis')->get('yiputeng_api_token');
        } else {
            $token = $this->getApiToken();
            if ($token['code'] === 0) {
                $api_token = $token['api_token'];
            } else {
                return $token;
            }
        }

        $http = new Client();

        $queryShelfListApi = config('services.yiputeng_vending_machine.query_shelf_list');

        $response = $http->post($queryShelfListApi, [
            'form_params' => [
                'api_token' => $api_token,
                'version' => '1.0',
                'machine_id' => $machine_id
            ]
        ]);

        $result = json_decode($response->getBody(), true);

        return $result;
    }

    public function payDelivery($params)
    {
        if (Cache::store('redis')->has('yiputeng_api_token')) {
            $api_token = Cache::store('redis')->get('yiputeng_api_token');
        } else {
            $token = $this->getApiToken();
            if ($token['code'] === 0) {
                $api_token = $token['api_token'];
            } else {
                return $token;
            }
        }

        $params['api_token'] = $api_token;

        $params['version'] = '1.0';
        $params['notify_url'] = 'https://vm.yzkj01.com/yiputeng/deliverProductNotifications/notify';
        $params['sign_type'] = 'MD5';

        $params['pay_price'] = 1;
        $params['pay_person_id'] = '1';

        $nonce_str = Str::random();

        $params['nonce_str'] = $nonce_str;

        $sign = $this->getSign($params);

        $params['sign'] = $sign;

        $http = new Client();

        $payDeliveryApi = config('services.yiputeng_vending_machine.pay_delivery');

        $response = $http->post($payDeliveryApi, [
            'form_params' => $params
        ]);

        $result = json_decode($response->getBody(), true);

        return $result;
    }

    public function payMultiDelivery($params)
    {
        if (Cache::store('redis')->has('yiputeng_api_token')) {
            $api_token = Cache::store('redis')->get('yiputeng_api_token');
        } else {
            $token = $this->getApiToken();
            if ($token['code'] === 0) {
                $api_token = $token['api_token'];
            } else {
                return $token;
            }
        }

        $params['api_token'] = $api_token;

        $params['version'] = '1.0';
        $params['notify_url'] = 'https://vm.yzkj01.com/yiputeng/deliverProductNotifications/notify';
        $params['sign_type'] = 'MD5';

        $params['pay_price'] = 1;
        $params['pay_person_id'] = '1';

        $nonce_str = Str::random();

        $params['nonce_str'] = $nonce_str;

        $sign = $this->getSign($params);

        $params['sign'] = $sign;

        $http = new Client();

        $payMultiDeliveryApi = config('services.yiputeng_vending_machine.pay_multi_delivery');

        $response = $http->post($payMultiDeliveryApi, [
            'form_params' => $params
        ]);

        $result = json_decode($response->getBody(), true);

        return $result;
    }

    protected function getSign($params)
    {
        $str = '';
        ksort($params);
//        if (array_key_exists('multi_pay', $params)) {
//            $params['multi_pay'] = urlencode($params['multi_pay']);
//        }
        foreach ($params as $k => $v) {
            //为key/value对生成一个key=value格式的字符串，并拼接到待签名字符串后面
            $str .= "$k=$v&";
        }

        $appSecret = config('services.yiputeng_vending_machine.app_secret');

        $str .= 'key=' . $appSecret;

//        dd($str);

        return md5($str);
    }
}
