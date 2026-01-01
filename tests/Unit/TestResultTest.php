<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\TestResult;

class TestResultTest extends TestCase
{
    public function test_compute_iq_scales_proportionally()
    {
        // Score 0 should map near lower bound (70 by formula)
        $this->assertEquals(70, TestResult::computeIq(0, 100));

        // Half of max -> IQ about 100
        $this->assertEquals(100, TestResult::computeIq(50, 100));

        // Full score -> IQ about 130
        $this->assertEquals(130, TestResult::computeIq(100, 100));

        // Guard against zero maxScore
        $this->assertEquals(70, TestResult::computeIq(10, 0));

        // Clamping: extreme high
        $this->assertTrue(TestResult::computeIq(1000, 100) <= 160);
        $this->assertTrue(TestResult::computeIq(-10, 100) >= 40);
    }
}
