<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Member;
use App\Models\Loginhistory;

use Validator;

use Illuminate\Support\Facades\Hash;

use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function __construct() 
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    // Login 
    public function login(Request $request)
    {
        $this->setLoginHistory($request);
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }
    
    // Register a User.
    public function register(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        if(substr($request->email,strrpos($request->email,"@")) == '@sinw.com'){
            $role = config('const.ADMIN');
            $membership_code = null;
        }else{
            $role = config('const.NORMAL_USER');
            $membership_code = substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyz"), 0, 6);
        }
        $uuid = Str::uuid()->toString();
        $user = User::create(array_merge(
                    $validator->validated(),
                    [   'uuid' => $uuid,
                        'password' => Hash::make($request->password),
                        'role' => $role,
                        'membership_code' => $membership_code,
                    ]
                ));
        if($membership_code != null){
            $user->salary = null;
            $user->occupation = null;
            return response()->json([
                'message' => 'User successfully registered',
                'user' => $user
            ], 201);
        }
      
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    // Log the user out (Invalidate the token)
    public function logout(Request $request) 
    {
        auth()->logout();
        return response()->json(['message' => 'User successfully logged out']);

    }

    // Refresh a token.
    public function refresh() 
    {
        return $this->createNewToken(auth()->refresh());
    }

    // Get the authenticated User.
    public function user() 
    {
        $user = User::getData(auth()->user());
        return response()->json($user);
    }

    // Get the token array structure.
    protected function createNewToken($token)
    {
        $user = User::getData(auth()->user());

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => $user,
        ]);
    }

    // Get client's IP address
    public function getIPAddress() 
    {  
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) {  
            $ip = $_SERVER['HTTP_CLIENT_IP'];  
        }  
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {  
            $ip = $_SERVER['HTTP_X_FORWARDED'];  
        }
        elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {  
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];  
        } 
        elseif (!empty($_SERVER['HTTP_FORWARDED'])) {  
            $ip = $_SERVER['HTTP_FORWARDED'];  
        } 
        else{  
            $ip = $_SERVER['REMOTE_ADDR'];  
        }  
        return $ip;  
    }  
    
    // Store log in history
    protected function setLoginHistory($request)
    {
        Loginhistory::create([
            'ip_address' => $this->getIPAddress(),
            'email' => $request->email,
        ]);
    }

    // Insert membershipcode automatically at once registering for normal user
    // public function insertMember($uuid,$membership_code)
    // {
    //     $member = Member::create([
    //         'user_id' => $uuid,
    //         'membership_code' => $membership_code,
    //     ]);

    //     return $member;
    // }

}
