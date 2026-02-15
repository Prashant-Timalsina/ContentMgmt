<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Content;
use App\Models\AccessRequest;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function index()
    {
        //
        return User::select('id','name','email')
        ->with(['roles','permissions'])
        ->withCount(['accessRequests as pending' => function ($query){
            $query->where('status','pending');
        }])
        ->get();
    }

    public function updateRole(Request $request, User $user)
    {
        //
        $request->validate([
            'role' => 'required|exists:roles,name'
        ]);

        // dude u aint allowed to demote yourself
        if($request->user()->id === $user->id){
            return response()->json([
                'message'=> 'You cannot change your own role brah'
            ],401);
        }

        $user->syncRoles([$request->role]);

        return response()->json([
            'message' => 'Role updated Successfully'
        ]);
    }

    public function updatePermissions(Request $request, User $user)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);
    
        $user->syncPermissions($request->permissions ?? []);
    
        return response()->json([
            'message' => 'Permissions updated successfully'
        ]);
    }

    /**
     * List all articles (admin). Used for pending/review list.
     */
    public function articles()
    {
        return Content::with('type', 'author')->latest()->get();
    }
}
