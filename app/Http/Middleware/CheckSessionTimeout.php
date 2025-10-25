<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionTimeout
{
    protected $timeout = 1800;

    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            $lastActivity = Session::get('last_activity');
            $currentTime = time();

            if ($lastActivity && ($currentTime - $lastActivity) > $this->timeout) {

                $user->session_id = null;
                $user->save();

                Auth::logout();
                Session::flush();

                return redirect()->route('login')
                    ->with('message', 'Sesi Anda telah berakhir karena tidak ada aktivitas. Silakan login kembali.');
            }

            Session::put('last_activity', $currentTime);
        }

        return $next($request);
    }
}
