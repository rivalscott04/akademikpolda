<?php

namespace App\Observers;

use App\Models\UserTryoutSession;

class UserTryoutSessionObserver
{
    public function created(UserTryoutSession $session): void
    {
        $this->maybeClearOnCompleted($session);
    }

    public function updated(UserTryoutSession $session): void
    {
        $this->maybeClearOnCompleted($session);
    }

    private function maybeClearOnCompleted(UserTryoutSession $session): void
    {
        if ($session->status === 'completed') {
            $userId = $session->user_id;
            cache()->forget("paket_lengkap_status_{$userId}");
            cache()->forget("paket_lengkap_progress_{$userId}");
        }
    }
}


