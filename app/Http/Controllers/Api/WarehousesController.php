<?php

namespace App\Http\Controllers\Api;

use App\Models\Warehouse;
use App\Transformers\WarehouseTransformer;
use Illuminate\Http\Request;

class WarehousesController extends Controller
{
    public function index(Warehouse $warehouse, Request $request, WarehouseTransformer $warehouseTransformer)
    {
        $user = $this->user();

        app(\Dingo\Api\Transformer\Factory::class)->disableEagerLoading();

        if ($user->is_mobile_admin) {
            $warehouses = $warehouse->all();
        } else {
            $warehouses = $user->warehouses;
        }

        if ($request->include) {
            $warehouses->load($request->include);
        }

        return $this->response->collection($warehouses, $warehouseTransformer);
    }
}
