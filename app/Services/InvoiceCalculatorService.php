<?php

namespace App\Services;

class InvoiceCalculatorService
{
    public function tax(float $purchaseOrderPrice): float
    {
        return round($purchaseOrderPrice * 0.06, 2);
    }

    public function delayPenalty(float $purchaseOrderPrice): float
    {
        return round($purchaseOrderPrice * 0.01, 2);
    }

    public function calculate(float $purchaseOrderPrice, float $tax, float $discount, float $penalty = 0): float
    {
        return round($purchaseOrderPrice + $tax - $discount - $penalty, 2);
    }
}
