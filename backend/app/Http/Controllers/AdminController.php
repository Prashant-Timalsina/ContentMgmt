<?php

namespace App\Http\Controllers;

use App\Models\User;
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
    

    public function approveRequest($id)
{
        $req = AccessRequest::with('user')->findOrFail($id);
        
        if ($req->type === 'role') {
            // Assign the whole role
            $req->user->assignRole($req->item_name);
        } else {
            // Give just the specific permission
            $req->user->givePermissionTo($req->item_name);
        }

        $req->update(['status' => 'approved']);
        return response()->json(['message' => "Successfully granted {$req->item_name}"]);
    }
}
