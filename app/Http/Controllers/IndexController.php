<?php

namespace App\Http\Controllers;

class IndexController extends Controller
{
    //
    public function home()
    {
        return to_route('connect.vcs');
    }
}
