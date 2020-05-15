<?php

namespace App\Classes;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;


class CurrencyHelper
{

    public static function convert($amount, $from = null, $to = null, $format = true)
    {
        // Get currencies involved
        $from = $from ?: $this->config('default');
        $to = $to ?: $this->getUserCurrency();
        // Get exchange rates
        $from_rate = $this->getCurrencyProp($from, 'exchange_rate');
        $to_rate = $this->getCurrencyProp($to, 'exchange_rate');
        // Skip invalid to currency rates
        if ($to_rate === null) {
            return null;
        }
        // Convert amount
        if ($from === $to) {
            $value = $amount;
        } else {
            $value = ($amount * $to_rate) / $from_rate;
        }
        // Should the result be formatted?
        if ($format === true) {
            return $this->format($value, $to);
        }
        // Return value
        return $value;
    }

}