<?php

namespace App\Services;

class TkpScoringService
{
    /**
     * Calculate final TKP score on a 1–100 scale.
     * Empty answers are treated as 0; for scaling we clamp min to N.
     */
    public function calculateFinalScore(int $numQuestions, int $totalRawScore): float
    {
        if ($numQuestions <= 0) {
            return 0.0;
        }

        $minTotal = $numQuestions; // all answers valued 1
        $scaledTotal = max($totalRawScore, $minTotal);
        $denominator = 4 * $numQuestions;
        $final = 1 + (($scaledTotal - $numQuestions) * 99) / $denominator;

        // Clamp to 1..100
        if ($final < 1) {
            $final = 1;
        } elseif ($final > 100) {
            $final = 100;
        }

        return round($final, 2);
    }
}


