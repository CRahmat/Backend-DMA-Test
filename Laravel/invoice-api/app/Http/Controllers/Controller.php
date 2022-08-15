<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function trueResponse($message="",$httpcode=200){
        return response()->json([
            "status"    => true,
            "message"   => $message,
        ],$httpcode);
    }

    public function falseResponse($message="",$httpcode=422){
        return response()->json([
            "status"    => false,
            "message"   => $message,
        ],$httpcode);
    }
}
