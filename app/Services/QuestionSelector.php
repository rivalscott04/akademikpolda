<?php

namespace App\Services;

use App\Models\Soal;
use App\Models\Tryout;
use Illuminate\Support\Collection;

class QuestionSelector
{
    /**
     * Validate availability for each blueprint row.
     */
    public function validateBlueprintAvailability(Tryout $tryout): void
    {
        $blueprints = $tryout->blueprints;

        foreach ($blueprints as $bp) {
            $available = Soal::active()
                ->byKategoriLevel($bp->kategori_id, $bp->level)
                ->count();

            if ($available < $bp->jumlah) {
                throw new \Exception("Kategori {$bp->kategori_id} level {$bp->level} kurang. Tersedia: {$available}, Dibutuhkan: {$bp->jumlah}");
            }
        }
    }

    /**
     * Pick questions per blueprint (kategori + level) with no duplication.
     * Returns a flat collection of Soal.
     */
    public function pickByBlueprint(Tryout $tryout): Collection
    {
        $pickedIds = [];
        $picked = collect();

        foreach ($tryout->blueprints as $bp) {
            $soals = Soal::active()
                ->byKategoriLevel($bp->kategori_id, $bp->level)
                ->whereNotIn('id', $pickedIds)
                ->inRandomOrder()
                ->limit($bp->jumlah)
                ->get();

            $picked = $picked->concat($soals);
            $pickedIds = array_merge($pickedIds, $soals->pluck('id')->all());
        }

        return $picked;
    }
}


