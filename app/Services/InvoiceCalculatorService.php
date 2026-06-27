<?php

namespace App\Services;

class InvoiceCalculatorService
{
    public function calculate(float $subtotal, float $tax, float $creditNote): float
    {
        return round($subtotal + $tax - $creditNote, 2);
    }
}
