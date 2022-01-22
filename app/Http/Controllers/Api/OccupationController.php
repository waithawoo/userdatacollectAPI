<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Occupation;

use Validator;

class OccupationController extends Controller
{
    public function __construct() 
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    // Add salary and occupation data to a specific member
    public function add(Request $request)
    {
        $user = auth()->user();
        if($user->role == config('const.ADMIN')){
            $validator = Validator::make($request->all(), [
                'user_uuid' => 'required',
                'salary' => 'required',
                'occupation' => 'required',
            ]);
        }else{
            $validator = Validator::make($request->all(), [
                'salary' => 'required',
                'occupation' => 'required',
            ]);
        }
            

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        if($user->role == config('const.ADMIN')){
            $user_uuid = $request->user_uuid;
            if(User::where('uuid',$user_uuid)->first()->role == config('const.ADMIN')){
                return response()->json([
                    'success' => false,
                    'message' => 'Sorry,  Salary and Occupation could not be added for admin.'
                ], 500);
            }
        }else{
            $user_uuid = auth()->user()->uuid;
        }
        $occupation = new Occupation();

        $occupation->uuid = Str::uuid()->toString();
        $occupation->user_uuid = $user_uuid;
        $occupation->name = $request->occupation;
        $occupation->salary = $request->salary;

        $saved =$occupation->save();

        if($saved){
            return response()->json([
                'success' => true,
                "message" => "Added Salary and Occupation successfully.",
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Sorry,  Salary and Occupation could not be added.'
            ], 500);
        }
       
    }

    // Update salary and occupation data to a specific member
    public function update(Request $request, $occupation_uuid)
    {
        $occupation = Occupation::where('uuid', $occupation_uuid)->first();

        if (!$occupation) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, occupation with id ' . $occupation_uuid . ' cannot be found'
            ], 400);
        }
        $validator = Validator::make($request->all(), [
            'salary' => 'required',
            'occupation' => 'required',
        ]);
        $occupation->name = $request->occupation;
        $occupation->salary = $request->salary;
        $saved =$occupation->save();

        if($saved){
            return response()->json([
                'success' => true,
                "message" => "Update Salary and Occupation successfully.",
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Sorry,  Salary and Occupation could not be updated.'
            ], 500);
        }
    }
}
