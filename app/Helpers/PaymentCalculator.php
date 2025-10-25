<?php

namespace App\Helpers;

class PaymentCalculator
{
    const GOPAY_RATE = 0.02;        // 2%
    const QRIS_RATE = 0.007;        // 0.7%
    const SHOPEEPAY_RATE = 0.02;    // 2%
    const DANA_RATE = 0.015;        // 1.5%
    const CREDIT_CARD_RATE = 0.029; // 2.9%
    const CREDIT_CARD_FIXED = 2000; // Rp 2.000
    const BANK_TRANSFER_FIXED = 4000; // Rp 4.000

    /**
     * Calculate total amount including payment gateway fees
     *
     * @param float $baseAmount Original amount before fees
     * @param string $paymentMethod Payment method chosen
     * @return array Total amount and fee details
     */
    public static function calculateTotalAmount($baseAmount, $paymentMethod)
    {
        $fee = 0;
        $totalAmount = $baseAmount;

        switch (strtolower($paymentMethod)) {
            case 'gopay':
                $fee = $baseAmount * self::GOPAY_RATE;
                $totalAmount = $baseAmount + $fee;
                break;

            case 'qris':
                $fee = $baseAmount * self::QRIS_RATE;
                $totalAmount = $baseAmount + $fee;
                break;

            case 'shopeepay':
                $fee = $baseAmount * self::SHOPEEPAY_RATE;
                $totalAmount = $baseAmount + $fee;
                break;

            case 'dana':
                $fee = $baseAmount * self::DANA_RATE;
                $totalAmount = $baseAmount + $fee;
                break;

            case 'credit_card':
                $fee = ($baseAmount * self::CREDIT_CARD_RATE) + self::CREDIT_CARD_FIXED;
                $totalAmount = $baseAmount + $fee;
                break;

            case 'bank_transfer':
            case 'bca_va':
            case 'bni_va':
            case 'bri_va':
            case 'mandiri_va':
            case 'permata_va':
                $fee = self::BANK_TRANSFER_FIXED;
                $totalAmount = $baseAmount + $fee;
                break;

            default:
                throw new \InvalidArgumentException("Unknown payment method: {$paymentMethod}");
        }

        return [
            'base_amount' => $baseAmount,
            'fee' => ceil($fee), // Pembulatan ke atas untuk fee
            'total_amount' => ceil($totalAmount), // Pembulatan ke atas untuk total
            'payment_method' => $paymentMethod
        ];
    }

    /**
     * Get all available payment methods with their fees
     *
     * @param float $amount Base amount to calculate fees
     * @return array List of payment methods with their fees
     */
    public static function getPaymentMethodsWithFees($amount)
    {
        $methods = [
            'gopay',
            'qris',
            'shopeepay',
            'dana',
            'credit_card',
            'bank_transfer'
        ];

        $result = [];
        foreach ($methods as $method) {
            $calculation = self::calculateTotalAmount($amount, $method);
            $result[$method] = $calculation;
        }

        return $result;
    }
}
