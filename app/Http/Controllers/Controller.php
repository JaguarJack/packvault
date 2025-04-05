<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    //
    public function user()
    {
        return Auth::user();
    }


    public function userId()
    {
        return (int) $this->user()->id;
    }
}
