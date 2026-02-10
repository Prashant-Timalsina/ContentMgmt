<?php

namespace App\Http\Controllers;

use App\Models\AccessRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccessRequestController extends Controller
{
    /**
     * Store a newly created request from a User.
     */
    public function requestUpdate(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:role,permission',
            'item_name' => 'required|string',
            'reason' => 'nullable|string'
        ]);

        $accessRequest = auth()->user()->accessRequests()->create($data);
        return response()->json([
            'message'=>'Request submitted for review',
            'data'=> $accessRequest
        ],201);
    }

    public function approve(Request $request, AccessRequest $accessRequest){
        if($accessRequest->status !== 'pending'){
            return response()->json(['message'=>'Request already Processed'],400);
        }

        $user = $accessRequest->user;

        DB::transaction(function () use ($accessRequest,$user,$request)
        {
            if ($accessRequest->type === 'role') {
                $user->syncRoles($accessRequest->item_name);
            }
            else {
                $user->givePermissionTo($accessRequest->item_name);
            }

            $accessRequest->update([
                'status'=>'approved',
                'admin_note'=> $request->admin_note ?? 'Approved by Admin'
            ]);
        });

        return response()->json([
            'message'=>"Successfully granted {$accessRequest->item_name} to {$user->name}"
        ]);
    }

    public function reject(Request $request,AccessRequest $accessRequest)
    {
        $request->validate(['admin_note'=>'required|string']);

        $accessRequest->update([
            'status'=>'rejected',
            'admin_note'=>$request->admin_note
        ]);

        return response()->json([
            'message'=>'Request rejected.'
        ]);
    }

}
