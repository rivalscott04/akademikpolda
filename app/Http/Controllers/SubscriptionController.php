<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Subscription;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function checkout()
    {
        return view('subscription.checkout');
    }

    public function packages()
    {
        $startTime = microtime(true);
        $user = Auth::user();
        
        // Jika admin, tampilkan halaman pembelian paket
        if ($user->role === 'admin') {
            return view('subscription.packages');
        }
        
        // OPTIMASI: Cache package limits untuk menghindari multiple config calls
        $packageLimits = $user->getPackageLimits();
        $maxTryouts = $packageLimits['max_tryouts'];
        
        // OPTIMASI: Load subscription dengan eager loading
        $subscription = $user->subscriptions;
        
        // OPTIMASI: Cache allowed categories (bisa di-cache lebih lama karena jarang berubah)
        $allowedCategories = $user->getAllowedCategories();
        
        // OPTIMASI: Cache package features (static data)
        $packageFeatures = $user->getPackageFeaturesDescription();
        $packageDisplayName = $user->getPackageDisplayName();
        
        // OPTIMASI: Cache method calls yang dipanggil berulang di view
        $hasActiveSubscription = $user->hasActiveSubscription();
        $canAccessTryout = $user->canAccessTryout();
        // canAccessKecermatan method removed - kecermatan feature removed
        $userPackage = $user->package;
        
        // OPTIMASI: Cache paket lengkap status (jika diperlukan)
        $paketLengkapStatus = null;
        $paketLengkapProgress = 0;
        if ($userPackage === 'lengkap') {
            $paketLengkapStatus = $user->getPaketLengkapStatus();
            $paketLengkapProgress = $user->getPaketLengkapProgress();
        }

        // OPTIMASI: Cache user statistics
        $userStatistics = $user->getUserStatistics();
        
        // Tambahkan informasi tambahan untuk display yang lebih lengkap
        $additionalInfo = [
            'subscription_start_date' => $subscription ? $subscription->created_at : null,
            'days_remaining' => $subscription && $subscription->end_date ? 
                max(0, now()->diffInDays($subscription->end_date, false)) : null,
            'total_questions_answered' => $userStatistics['total_questions_answered'] ?? 0,
            'total_tryouts_completed' => $userStatistics['total_tryouts'] ?? 0,
            'last_activity' => $userStatistics['last_activity'] ?? null
        ];
        
        // Log execution time untuk debugging
        $executionTime = round((microtime(true) - $startTime) * 1000, 2);
        Log::info("Subscription packages page loaded in {$executionTime}ms for user {$user->id}");
        
        return view('subscription.user-package-status', [
            'user' => $user,
            'packageLimits' => $packageLimits,
            'subscription' => $subscription,
            'allowedCategories' => $allowedCategories,
            'maxTryouts' => $maxTryouts,
            'packageFeatures' => $packageFeatures,
            'packageDisplayName' => $packageDisplayName,
            'hasActiveSubscription' => $hasActiveSubscription,
            'canAccessTryout' => $canAccessTryout,
            // canAccessKecermatan removed - kecermatan feature removed
            'userPackage' => $userPackage,
            'paketLengkapStatus' => $paketLengkapStatus,
            'paketLengkapProgress' => $paketLengkapProgress,
            'userStatistics' => $userStatistics,
            'additionalInfo' => $additionalInfo
        ]);
    }

    public function packagesAdmin()
    {
        // Halaman pembelian paket khusus untuk admin
        return view('subscription.packages');
    }

    public function expired()
    {
        return view('subscription.expired');
    }

    public function check()
    {
        $user = auth()->user();
        $subscription = $user->subscription;

        return response()->json([
            'has_active_subscription' => $user->hasActiveSubscription(),
            'subscription' => $subscription
        ]);
    }

    public function processSubscription(Request $request, $package)
    {
        try {
            // Get authenticated user
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
            }

            // Validasi package yang dipilih dari database
            $packageExists = Package::where('name', 'like', '%' . $package . '%')
                ->active()
                ->exists();

            if (!$packageExists) {
                return redirect()->back()->with('error', 'Paket yang dipilih tidak tersedia.');
            }

            // Redirect ke halaman checkout dengan parameter package
            return redirect()->route('subscription.checkout', ['package' => $package]);
        } catch (\Exception $e) {
            // Log error jika diperlukan
            \Log::error('Error processing subscription: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses paket berlangganan. Silakan coba lagi.');
        }
    }

    public function showCheckout($package)
    {
        try {
            // Get authenticated user
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
            }

            // Get package from database
            $selectedPackage = Package::where('name', 'like', '%' . $package . '%')
                ->active()
                ->first();

            if (!$selectedPackage) {
                return redirect()->route('subscription.packages')->with('error', 'Paket yang dipilih tidak tersedia.');
            }

            // Convert package to array format expected by view
            $packageData = [
                'key' => $package,
                'name' => $selectedPackage->name,
                'description' => $selectedPackage->description,
                'price' => $selectedPackage->price,
                'old_price' => $selectedPackage->old_price,
                'label' => $selectedPackage->label,
                'features' => $selectedPackage->features,
                'duration' => 30 // Default duration, bisa disesuaikan jika ada field di database
            ];

            return view('subscription.checkout', [
                'package' => $packageData,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            \Log::error('Error showing checkout: ' . $e->getMessage());
            return redirect()->route('subscription.packages')->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    public function process()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                throw new \Exception('User tidak ditemukan');
            }

            // Cek apakah user sudah memiliki transaksi yang belum selesai
            $subscription = Subscription::where('user_id', $user->id)
                ->whereIn('payment_status', ['pending', 'failed'])
                ->latest()
                ->first();

            if (!$subscription) {
                // Jika tidak ada transaksi sebelumnya, buat yang baru
                $transaction_id = 'ORD-' . time() . '-' . $user->id;
                $subscription = Subscription::create([
                    'transaction_id' => $transaction_id,
                    'user_id' => $user->id,
                    'amount_paid' => 105000,
                    'payment_status' => 'pending',
                    'start_date' => now(),
                    'end_date' => now()->addDays(30),
                ]);
            } else {
                // Jika ada transaksi pending, generate transaction_id baru
                $subscription->update([
                    'transaction_id' => 'ORD-' . time() . '-' . $user->id,
                ]);
            }

            $params = [
                'transaction_details' => [
                    'order_id' => $subscription->transaction_id,
                    'gross_amount' => $subscription->amount_paid,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone_number ?? '',
                ],
                'item_details' => [
                    [
                        'id' => 'PAKET_CERMAT',
                        'price' => $subscription->amount_paid,
                        'quantity' => 1,
                        'name' => 'Paket Cermat - Persiapan Tes BINTARA POLRI',
                    ],
                ],
                'enabled_payments' => [
                    'credit_card',
                    'bca_va',
                    'bni_va',
                    'bri_va',
                    'mandiri_va',
                    'permata_va',
                    'gopay',
                    'shopeepay',
                    'qris'
                ],
            ];

            // Generate Snap Token baru
            try {
                $snapToken = Snap::getSnapToken($params);

                // Update payment details dengan token baru
                $subscription->update([
                    'payment_details' => json_encode([
                        'snap_token' => $snapToken,
                        'package' => 'PAKET_CERMAT',
                        'package_type' => 'PAKET_CERMAT'
                    ])
                ]);

                return view('subscription.payment', [
                    'subscription' => $subscription,
                    'snap_token' => $snapToken
                ]);
            } catch (\Exception $e) {
                Log::error('Midtrans Error for user ' . $user->id . ': ' . $e->getMessage());
                Log::error('Params: ' . json_encode($params));

                throw new \Exception('Gagal membuat token pembayaran. Silakan coba beberapa saat lagi.');
            }
        } catch (\Exception $e) {
            Log::error('Subscription Process Error: ' . $e->getMessage());

            return redirect()->route('subscription.checkout')
                ->with('error', $e->getMessage());
        }
    }


    public function notification(Request $request)
    {
        try {
            // Log raw request untuk debugging
            \Log::info('Raw notification received:', [
                'headers' => $request->headers->all(),
                'body' => $request->all()
            ]);

            // Ambil data dari request langsung
            $order_id = $request->input('order_id');
            $transaction_status = $request->input('transaction_status');
            $payment_type = $request->input('payment_type');
            $transaction_id = $request->input('transaction_id');

            // Log notification data
            \Log::info('Parsed Notification Data:', [
                'order_id' => $order_id,
                'transaction_status' => $transaction_status,
                'payment_type' => $payment_type,
                'transaction_id' => $transaction_id
            ]);

            // Validate required fields
            if (empty($order_id)) {
                throw new \Exception('Order ID tidak ditemukan dalam notifikasi');
            }

            // Find subscription
            $subscription = Subscription::where('transaction_id', $order_id)->first();
            if (!$subscription) {
                \Log::error('Subscription not found:', ['order_id' => $order_id]);
                throw new \Exception('Subscription dengan order ID ' . $order_id . ' tidak ditemukan');
            }

            // Build payment details
            $paymentDetails = [
                'payment_type' => $payment_type,
                'transaction_status' => $transaction_status,
                'transaction_time' => $request->input('transaction_time'),
                'package' => 'PAKET_CERMAT',
                'gross_amount' => $request->input('gross_amount'),
                'transaction_id' => $transaction_id,
            ];

            if ($payment_type === 'qris') {
                $paymentDetails['platform'] = 'QRIS';
                $paymentDetails['acquirer'] = $request->input('acquirer');
            }

            \Log::info('Processing transaction:', [
                'order_id' => $order_id,
                'status' => $transaction_status,
                'payment_type' => $payment_type
            ]);

            try {
                \DB::beginTransaction();

                switch ($transaction_status) {
                    case 'capture':
                    case 'settlement':
                        $subscription->update([
                            'payment_status' => 'paid',
                            'payment_method' => $payment_type,
                            'payment_details' => json_encode($paymentDetails)
                        ]);

                        // Update user status
                        $subscription->user()->update(['is_active' => true]);
                        \Log::info('Payment completed successfully:', ['order_id' => $order_id]);
                        break;

                    case 'pending':
                        $subscription->update([
                            'payment_status' => 'pending',
                            'payment_method' => $payment_type,
                            'payment_details' => json_encode($paymentDetails)
                        ]);
                        \Log::info('Payment pending:', ['order_id' => $order_id]);
                        break;

                    case 'deny':
                    case 'expire':
                    case 'cancel':
                        $subscription->update([
                            'payment_status' => 'failed',
                            'payment_method' => $payment_type,
                            'payment_details' => json_encode($paymentDetails)
                        ]);
                        \Log::info('Payment failed:', ['order_id' => $order_id]);
                        break;
                }

                \DB::commit();
                return response()->json(['success' => true]);
            } catch (\Exception $e) {
                \DB::rollBack();
                \Log::error('Database transaction failed:', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        } catch (\Exception $e) {
            \Log::error('Notification Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            // Return 200 status untuk mencegah Midtrans melakukan retry
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 200);
        }
    }

    public function finish(Request $request)
    {
        return redirect()->route('user.profile', ['userId' => Auth::user()->id])
            ->with('message', 'Pembayaran berhasil, anda bisa melakukan tes');
    }

    public function unfinish(Request $request)
    {
        return redirect()->route('subscription.checkout')
            ->with('warning', 'Pembayaran belum selesai');
    }

    public function error(Request $request)
    {
        return redirect()->route('subscription.checkout')
            ->with('error', 'Pembayaran gagal');
    }
}
