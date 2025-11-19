<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class PricingCalculationTest extends TestCase
{
    public function test_calculates_price_for_exact_hours(): void
    {
        $hourlyRate = 5.00;
        $fractionRate = 1.00;
        $fractionMinutes = 15;
        
        $hours = 2.0;
        $fullHours = floor($hours);
        $remainingMinutes = ($hours - $fullHours) * 60;
        $fractionBlocks = $remainingMinutes > 0 ? ceil($remainingMinutes / $fractionMinutes) : 0;
        
        $totalAmount = ($fullHours * $hourlyRate) + ($fractionBlocks * $fractionRate);
        
        $this->assertEquals(10.00, $totalAmount);
    }

    public function test_calculates_price_for_fractional_hours(): void
    {
        $hourlyRate = 5.00;
        $fractionRate = 1.00;
        $fractionMinutes = 15;
        
        $hours = 2.5;
        $fullHours = floor($hours);
        $remainingMinutes = ($hours - $fullHours) * 60;
        $fractionBlocks = $remainingMinutes > 0 ? ceil($remainingMinutes / $fractionMinutes) : 0;
        
        $totalAmount = ($fullHours * $hourlyRate) + ($fractionBlocks * $fractionRate);
        
        $this->assertEquals(12.00, $totalAmount);
    }

    public function test_calculates_price_for_one_fraction_block(): void
    {
        $hourlyRate = 5.00;
        $fractionRate = 1.00;
        $fractionMinutes = 15;
        
        $hours = 1.2;
        $fullHours = floor($hours);
        $remainingMinutes = ($hours - $fullHours) * 60;
        $fractionBlocks = $remainingMinutes > 0 ? ceil($remainingMinutes / $fractionMinutes) : 0;
        
        $totalAmount = ($fullHours * $hourlyRate) + ($fractionBlocks * $fractionRate);
        
        $this->assertEquals(6.00, $totalAmount);
    }

    public function test_calculates_price_for_multiple_fraction_blocks(): void
    {
        $hourlyRate = 5.00;
        $fractionRate = 1.00;
        $fractionMinutes = 15;
        
        $hours = 3.75;
        $fullHours = floor($hours);
        $remainingMinutes = ($hours - $fullHours) * 60;
        $fractionBlocks = $remainingMinutes > 0 ? ceil($remainingMinutes / $fractionMinutes) : 0;
        
        $totalAmount = ($fullHours * $hourlyRate) + ($fractionBlocks * $fractionRate);
        
        $this->assertEquals(18.00, $totalAmount);
    }
}
