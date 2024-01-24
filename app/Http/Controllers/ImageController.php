<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function image(Request $request, User $user)
    {
        $user= User::find(19);
        $user->addMediaFormRequest('image')->toMediaCollection();
        return response()->json(['user' => $user]);
    }
}
