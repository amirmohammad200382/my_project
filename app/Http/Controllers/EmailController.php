<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\MyMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function email(Request $request) {

        $request->validate([
            'to' => 'required|email',
            'user_name' => 'required|string',
        ]);

        $emailData = [
            'user_name' => $request->input( 'user_name')
        ];
            Mail::to($request->input( 'to'))->send(new MyMail($emailData));

            return response()->json(['message' => 'Email sent successfully']);
    }
}
