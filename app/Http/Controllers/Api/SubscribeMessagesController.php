<?php

namespace App\Http\Controllers\Api;

use App\Models\SubscribeMessage;
use Illuminate\Http\Request;

class SubscribeMessagesController extends Controller
{
    public function store(Request $request, SubscribeMessage $subscribeMessage)
    {
        $user = $this->user();
        if ($user->subscribeMessages->where('template_id', $request->input('template_id'))->first()) {
            return $this->response->accepted();
        }
        $subscribeMessage->fill($request->all());
        $subscribeMessage->user()->associate($user);
        $subscribeMessage->save();
        return $this->response->created();
    }

    public function show(Request $request, SubscribeMessage $subscribeMessage)
    {
        $user = $this->user();
        $template_id = $request->input('template_id');
        $subscribeMessageResult = $subscribeMessage->where('user_id', $user->id)->where('template_id', $template_id)->first();
        if ($subscribeMessageResult) {
            return $this->response->created();
        } else {
            return $this->response->noContent();
        }
    }

    public function destroy(Request $request, SubscribeMessage $subscribeMessage)
    {
        $user = $this->user();
        $template_id = $request->input('template_id');
        $subscribeMessageResult = $subscribeMessage->where('user_id', $user->id)->where('template_id', $template_id)->first();
        $subscribeMessageResult->delete();
        return $this->response->noContent();
    }
}
