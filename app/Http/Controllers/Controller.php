<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function respondWithToken($token, $responseMessage, $data)
    {
        return \response()->json([
            "success" => true,
            "message" => $responseMessage,
            "data" => $data,
            "token" => $token,
            "token_type" => "bearer",

            'name' => $data->name,
            'email' => $data->email,
            'access_token' => $token,
        ], 200);
    }

    public function responseSuccess($responseMessage, $data)
    {
        return \response()->json([
            "success" => true,
            "message" => $responseMessage,
            "data" => $data,
        ], 200);
    }
    public function responseFail($responseMessage, $data)
    {
        return \response()->json([
            "success" => false,
            "message" => $responseMessage,
            "data" => $data,
        ], 500);
    }
}
