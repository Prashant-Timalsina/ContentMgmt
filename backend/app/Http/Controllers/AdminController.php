<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return User::with(['roles','permissions'])
        ->select('id','name','email')
        ->get();
    }

    /**
     * Store a newly created resource in storage.
     */
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
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
