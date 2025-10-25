<?php

namespace Tests\Unit;

use App\Services\TkpScoringService;
use PHPUnit\Framework\TestCase;

class TkpScoringServiceTest extends TestCase
{
    public function test_min_boundary_and_examples()
    {
        $svc = new TkpScoringService();

        // All 1s (T=N) -> ~1
        $this->assertEquals(1.0, $svc->calculateFinalScore(50, 50));

        // Example 1: N=50, T=185 -> 67.83
        $this->assertEquals(67.83, $svc->calculateFinalScore(50, 185));

        // Example 2: N=40, T=185 -> 90.72
        $this->assertEquals(90.72, $svc->calculateFinalScore(40, 185));

        // All 5s (T=5N) -> 100
        $this->assertEquals(100.0, $svc->calculateFinalScore(10, 50));

        // Empty answers treated as 0 but clamped to N -> ~1
        $this->assertEquals(1.0, $svc->calculateFinalScore(10, 0));
    }
}




