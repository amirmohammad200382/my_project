<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRquest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ApiLoginController extends Controller
{
    public function filter(Request $request)
    {
        $users = User::all();
        if ($request->filterEmail)
            $users = $users->where('email', $request->filterEmail);
        if ($request->filterFirstName)
            $users = $users->where('first_name', $request->filterFirstName);
        if ($request->filterLastName)
            $users = $users->where('last_name', $request->filterLastName);
        if ($request->filterUserName)
            $users = $users->where('user_name', $request->filterUserName);
        if ($request->filterAgeMin && $request->filterAgeMax)
            $users = $users->whereBetween('age', [$request->filterAgeMin, $request->filterAgeMax]);
        if ($request->filterPhoneNumber)
            $users = $users->where('phone_number', $request->filterPhoneNumber);
        if ($request->filterGender)
            $users = $users->where('gender', $request->filterGender);
        if ($request->filterRoles)
            $users = $users->where('role', $request->filterRoles);

        $filteredUsers = $users->toJson();

        return response()->json(['users' => json_decode($filteredUsers)]);
    }

    public function login(LoginRquest $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required',
        ]);

        if (Auth::attempt($validatedData)) {
            $user = Auth::user();
            $token = $user->createToken('api-token')->plainTextToken;;

            return response()->json([
                'token' => $token,
                'message' => 'ورود به سیستم با موفقیت انجام شد.',
            ]);
        } else {
            return response()->json([
                'message' => 'اطلاعات ورود نامعتبر است.',
            ], 401);
        }
    }

    public function logout($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'false',
                'text' => 'User not found'
            ], 404);
        }
        $user->tokens->each(function ($token, $key) {
            $token->delete();
        });
        Auth::logout();
        return response()->json([
            'status' => true,
            'text' => 'logout successfully'
        ]);
    }

    public function register(Request $request)
    {
        try {
            $validateuser = Validator::make($request->all(), [
                'first_name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                'role' => 'required'
            ]);
            if ($validateuser->fails()) {
                return response()->json([
                    'status' => false,
                    'text' => 'validation error',
                    'errors' => $validateuser->errors(),
                ], 401);
            }
            if ($request->role == 'seller') {
                $user = User::create([
                    'first_name' => $request->first_name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'status' => 'wating',
                ]);
                $user->assignRole($request->role);
            } else {
                $user = User::create([
                    'first_name' => $request->first_name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);
                $user->assignRole($request->role);
            }
                $token = $user->createToken("API TOKEN")->plainTextToken;
                return response()->json([
                    'status'=>true,
                    'token' => $token,
                    'message' => 'ثبت نام با موفقیت انجام شد.',
                ]);


        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'text' => $th->getMessage(),
            ], 500);

        }
    }
}


