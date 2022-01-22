<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Occupation;

use Validator;
class AdminController extends Controller
{
    public function __construct() 
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    // Get member and occupation data of all normal users
    public function getAllMembers()
    {
        $members = User::getAll();

        return response()->json([
            'members' => $members
        ], 200);
    }

    // Get member and occupation data of a specific normal user
    public function getMember($user_uuid)
    {
        $user = User::where('uuid',$user_uuid)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user with id - ' . $uuid . ' cannot be found'
            ], 400);
        }
        $member = User::getData($user);

        return $member;
    }

}
