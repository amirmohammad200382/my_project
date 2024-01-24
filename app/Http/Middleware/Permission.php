<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Permission
{
    public static function create(array $array)
    {

    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next , string $admin , string $seller , string $customer): Response
    {
//        $user = auth()->user();
//
//        if (!$user) {
//            return response()->json(['message' => 'User not authenticated'], 401);
//        } else {
//            if ($admin == 'admin' && $seller == '' && $customer == '') {
//                if ($user->role == 'admin') {
//                    return $next($request);
//                } else {
//                    return response()->json(['message' => 'Unauthorized'], 403);
//                }
//            }
//            if ($admin == 'admin' && $seller == 'seller' && $customer == '') {
//                if ($user->role == 'admin' || $user->role == 'seller') {
//                    return $next($request);
//                } else {
//                    return response()->json(['message' => 'Unauthorized'], 403);
//                }
//            }
//            if ($admin == 'admin' && $seller == 'seller' && $customer == 'customer') {
//                if ($user->role == 'admin' || $user->role == 'seller' || $user->role == 'customer') {
//                    return $next($request);
//                } else {
//                    return response()->json(['message' => 'Unauthorized'], 403);
//                }
//            }
//        }
//        try {
//            $user = auth()->user();
//            if (in_array($user->role, $role));
//
//            return $next($request);
//        }
//        else {
//
//    }
        return $next($request);
    }
}
