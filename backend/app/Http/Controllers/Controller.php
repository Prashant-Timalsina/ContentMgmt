<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
// use Illuminate\Foundation\Validation\ValidatesRequests; //if i use it hehe

abstract class Controller
{
    use AuthorizesRequests;
}
