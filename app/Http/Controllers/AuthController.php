<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function register(Request $request)
    {
        // Anti-bot protection: Check honeypot field
        if (!empty($request->website)) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        // Validasi input dengan pesan error yang lebih spesifik
        $request->validate([
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'phone_number' => 'required|regex:/^[0-9]{10,15}$/',
            'province' => 'required|string|max:255',
            'regency' => 'required|string|max:255',
            'terms' => 'required|accepted',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.min' => 'Nama minimal 2 karakter.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'phone_number.required' => 'Nomor HP wajib diisi.',
            'phone_number.regex' => 'Nomor HP harus 10-15 digit angka.',
            'province.required' => 'Provinsi wajib dipilih.',
            'regency.required' => 'Kabupaten/Kota wajib dipilih.',
            'terms.required' => 'Anda harus menyetujui syarat & ketentuan.',
            'terms.accepted' => 'Anda harus menyetujui syarat & ketentuan.',
        ]);


        DB::beginTransaction();
        try {
            // Buat user baru
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'is_active' => true,
                'province' => $request->province,
                'regency' => $request->regency,
                'role' => 'user',
                'package' => 'free', // Set default package to free for new users
            ]);

            Auth::login($user);

            // Simpan session_id ke database
            $user->session_id = session()->getId();
            $user->save();

            DB::commit();

            return redirect()->route('user.profile', ['userId' => Auth::user()->id])
                ->with('success', 'Pendaftaran berhasil! Anda dapat langsung mencoba tryout gratis.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error during registration: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan dalam proses pendaftaran. Silakan coba lagi.');
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Eager load subscription untuk menghindari N+1 query
            $user = Auth::user()->load('subscriptions');

            Log::info('User logged in: ' . $user->email);

            if ($user->role == 'admin') {
                return redirect()->route('admin.dashboard');
            }

            if ($user->role == 'user') {
                // Cache subscription status untuk menghindari query berulang
                $hasActiveSubscription = $user->hasActiveSubscription();
                
                return $hasActiveSubscription
                    ? redirect()->route('user.tryout.index')
                    : redirect()->route('user.profile', ['userId' => $user->id]);
            }

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            // Hapus session_id dari database
            $user->session_id = null;
            $user->save();
        }

        // Logout user
        Auth::logout();

        // Invalidate dan regenerate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
    public function showResetPassword()
    {
        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'new_password' => 'required|min:8|confirmed',
        ]);

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Email tidak ditemukan.',
            ]);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        Log::info('Password reset for user: ' . $user->email);

        return redirect()->route('login')
            ->with('message', 'Password berhasil diubah. Silakan login dengan password baru.');
    }
}
