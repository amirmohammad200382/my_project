<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRquest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {

        $users = User::all();

        return response()->json(['users' => $users], 200);
    }


    public function store(UserRequest $request)
    {

        $image = $request->image->getClientOriginalName();
        $request->image->move(public_path('image/users'), $image);
        User::create([
            'user_name' => $request->user_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'age' => $request->age,
            'gender' => $request->gender,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => md5($request->password),
            'address' => $request->address,
            'postalcode' => $request->postalcode,
            'country' => $request->country,
            'province' => $request->province,
            'city' => $request->city,
            'image' => $image,
            'status' => 'enable',

        ]);
        return response()->json(['message' => 'User created successfully'], 201);
    }
    public function update(Request $request, string $id)
    {
        User::where('id', $id)->update([
            'user_name' => $request->user_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'age' => $request->age,
            'gender' => $request->gender,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'postalcode' => $request->postalcode,
            'country' => $request->country,
            'province' => $request->province,
            'city' => $request->city,
            'updated_at' => date('Y-m-d H:i:s'),

        ]);
        return response()->json(['message' => 'User updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        User::where('id', $id)->update([
            'status' => 'disable'
        ]);
        return back();

    }

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
        return view('users.usersData', compact('users'));



    }

    public function createUser(Request $request)
    {
        try {

            $validateUser = Validator::make($request->all(),
                [
                    'first_name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required',
                    'role' => 'required',
                ]);
            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors(),
                ], 401);
            }

            if ($request->role == 'seller') {
                $user = User::create([
                    'first_name' => $request->first_name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => $request->role,
                    'status' => 'waiting',
                ]);

            } else {
                $user = User::create([
                    'first_name' => $request->first_name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => $request->role,
                ]);
            }
            $token = $user->createToken("API TOKEN")->plainTextToken;

            return response()->json(['status' => true,
                'message' => 'User Created Successfully',
                'token' => $token,], 200);

        } catch (\Throwable $th) {
            return response()->json(['status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function loginUser(LoginRquest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response('Login invalid', 503);
        }
        else {
            return redirect()->route('workplace');
        }

        $token = $user->createToken('Login-token')->plainTextToken;

        $res = [
            'user' => $user,
            'token' => $token
        ];


    }
}
