<?php

namespace App\Observers;

use App\Models\Subscription;

class SubscriptionObserver
{
    public function updated(Subscription $subscription)
    {
        // Cek jika end_date atau payment_status berubah
        if ($subscription->isDirty('end_date') || $subscription->isDirty('payment_status')) {
            $user = $subscription->user;

            // Hapus hasil tes jika subscription expired atau unpaid
            if ($subscription->end_date <= now() || $subscription->payment_status !== 'paid') {
                $user->hasilTes()->delete();
            }
        }
    }

    public function created(Subscription $subscription)
    {
        // Jika ada subscription baru tapi sudah expired atau unpaid
        if ($subscription->end_date <= now() || $subscription->payment_status !== 'paid') {
            $subscription->user->hasilTes()->delete();
        }
    }
}
