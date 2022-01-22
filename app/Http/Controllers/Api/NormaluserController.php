<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Occupation;

use Validator;

class NormaluserController extends Controller
{
    public function __construct() 
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    // Show member and occupation data of a normal user(logged in)
    public function getMember()
    {
        $member = User::getData(auth()->user());

        return response()->json([
            'user' => $member
        ], 200);
    }


    // Delete membership_code for a normal user(logged in)
    public function deleteMembershipCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'membership_code' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $uuid = auth()->user()->uuid;
        $member = User::where('uuid', $uuid)->first();
       
        if($request->membership_code != $member->membership_code || $member->membership_code == null){
            return response()->json([
                "success" => false,
                "message" => "You don't have this MembershipCode.",
            ]);
            
        }else{
            $member->membership_code = null;
            $member->save();
        }

        return response()->json([
            "success" => true,
            "message" => "MembershipCode is deleted successfully.",
        ]);
    }
}
