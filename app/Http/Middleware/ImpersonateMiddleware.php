<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ImpersonateMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if admin is impersonating a user
        if (Session::has('impersonate_id')) {
            $originalUserId = Session::get('impersonate_id');
            $originalUser = \App\Models\User::find($originalUserId);
            
            if ($originalUser) {
                // Store original user in session for easy access
                Session::put('original_user', $originalUser);
            }
        }

        return $next($request);
    }
} 