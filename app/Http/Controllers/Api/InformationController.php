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

    public function show(Information $information, InformationTransformer $informationTransformer)
    {
        return $this->response->item($information, $informationTransformer);
    }

    public function index(Information $information, InformationTransformer $informationTransformer)
    {
        $informationList = $information->recent()->paginate(5);
        return $this->response->paginator($informationList, $informationTransformer);
    }

    public function update(Information $information, InformationRequest $request, InformationTransformer $informationTransformer)
    {
        $this->authorize('own', $information);
        $information->update($request->all());
        return $this->response->item($information, $informationTransformer);
    }

    public function destroy(Information $information)
    {
        $this->authorize('own', $information);
        $information->delete();
        return $this->response->noContent();
    }
}
