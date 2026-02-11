<?php

namespace App\Http\Controllers\Admin;

use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    //
    public function index()
    {
        return Permission::select('id','name')->get();
    }
}
