<?php

namespace App\Http\Controllers;


abstract class Controller
{
    public function test()
    {
        return response()->json(['status' => 'ok']);
    }
}
