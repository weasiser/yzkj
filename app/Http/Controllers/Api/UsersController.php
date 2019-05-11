<?php

namespace App\Http\Controllers\Api;

use App\Transformers\UserTransformer;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function me(UserTransformer $userTransformer)
    {
        return $this->response->item($this->user(), $userTransformer);
    }
}
