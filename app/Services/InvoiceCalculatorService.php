<?php

namespace App\Services;

class InvoiceCalculatorService
{
    public const TAX_RATE = 0.06;

    public const PENALTY_RATE = 0.01;

    public function tax(float $purchaseOrderPrice): float
    {
        return round($purchaseOrderPrice * self::TAX_RATE, 2);
    }

    public function delayPenalty(float $purchaseOrderPrice): float
    {
        return round($purchaseOrderPrice * self::PENALTY_RATE, 2);
    }

    public function calculate(float $purchaseOrderPrice, float $tax, float $discount, float $penalty = 0): float
    {
        return max(0, round($purchaseOrderPrice + $tax - $discount - $penalty, 2));
    }
}
