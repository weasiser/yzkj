<?php

namespace App\Http\Controllers\Api;

use App\Models\Warehouse;
use App\Transformers\WarehouseTransformer;
use Illuminate\Http\Request;

class WarehousesController extends Controller
{
    public function index(Warehouse $warehouse, Request $request, WarehouseTransformer $warehouseTransformer)
    {
        app(\Dingo\Api\Transformer\Factory::class)->disableEagerLoading();

        $warehouses = $warehouse->all();
        if ($request->include) {
            $warehouses->load($request->include);
        }

        return $this->response->collection($warehouses, $warehouseTransformer);
    }
}
