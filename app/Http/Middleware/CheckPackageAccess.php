<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPackageAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $requiredAccess): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Periksa apakah pengguna memiliki langganan aktif
        if (!$user->hasActiveSubscription()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda harus memiliki langganan aktif untuk mengakses fitur ini.'
                ], 403);
            }

            return redirect()->route('user.profile', ['userId' => Auth::user()->id])
                ->with('subscriptionError', 'Anda harus memiliki langganan aktif untuk mengakses fitur ini.');
        }

        // Periksa akses berdasarkan package
        $hasAccess = false;
        $errorMessage = '';

        switch ($requiredAccess) {
            case 'tryout':
                $hasAccess = $user->canAccessTryout();
                $errorMessage = 'Paket Anda tidak termasuk akses ke Tryout CBT. Upgrade ke paket AKADEMIK atau Lengkap untuk mengakses fitur ini.';
                break;

            default:
                $hasAccess = false;
                $errorMessage = 'Akses tidak valid.';
        }

        if (!$hasAccess) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $errorMessage
                ], 403);
            }

            return redirect()->route('user.profile', ['userId' => Auth::user()->id])
                ->with('packageError', $errorMessage);
        }

        return $next($request);
    }
}
