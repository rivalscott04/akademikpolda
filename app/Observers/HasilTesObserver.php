<?php

namespace App\Observers;

use App\Models\HasilTes;

class HasilTesObserver
{
    public function created(HasilTes $hasilTes): void
    {
        $this->clearPaketLengkapCache($hasilTes);
    }

    public function updated(HasilTes $hasilTes): void
    {
        $this->clearPaketLengkapCache($hasilTes);
    }

    private function clearPaketLengkapCache(HasilTes $hasilTes): void
    {
        $userId = $hasilTes->user_id;
        cache()->forget("paket_lengkap_status_{$userId}");
        cache()->forget("paket_lengkap_progress_{$userId}");
    }
}


