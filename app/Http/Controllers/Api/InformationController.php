<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\InformationRequest;
use App\Models\Information;
use App\Transformers\InformationTransformer;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    public function store(InformationRequest $request, Information $information, InformationTransformer $informationTransformer)
    {
        $user = $this->user();
        $information->fill($request->all());
        $information->user()->associate($user);
        $information->save();

        return $this->response->item($information, $informationTransformer)->setStatusCode(201);
    }
}
