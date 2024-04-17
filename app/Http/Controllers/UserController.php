<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function UserList(Request $request)
    {
        try {
            $users = User::orderBy('id', 'desc')->select('id', 'name', 'email')->paginate($request->per_page);
            $users->appends($request->all());
            $data['users'] = $users;
            return response()->json([
                'success' => true,
                'message' => 'User List',
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }
    function UserRegistration(Request $request)
    {
        try {
            User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => $request->input('password'),
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'User Registration Successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'User Registration Failed'
            ], 200);
        }
    }

    function UserLogin(Request $request)
    {
        $count = User::where('email', '=', $request->input('email'))
            ->where('password', '=', $request->input('password'))
            ->select('id')->first();

        if ($count !== null) {
            // User Login-> JWT Token Issue
            $token = JWTToken::CreateToken($request->input('email'), $count->id);
            return response()->json([
                'status' => 'success',
                'message' => 'User Login Successfully',
                // 'token' => $token /*If you want to show token when you are logged in */

            ], 200)->cookie('token', $token, time() + 60 * 24 * 30); /*If you are use token set in cookie then you can use it and with time duration.*/
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'unauthorized'
            ], 401);
        }
    }

    function UserLogout(Request $request)
    {
        if ($request->hasCookie('token')) {
            $response = response()->json([
                'status' => 'success',
                'message' => 'User logged out successfully'
            ], 200);

            $response->cookie('token', '', -1);

            return $response;
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated'
            ], 401);
        }
    }
}
