<?php
/**
 * Created by PhpStorm.
 * User: thanakom
 * Date: 8/30/15 AD
 * Time: 7:36 AM
 */

namespace App\Payment;


class PaymentUtil
{
    const AMEX = 'American Express';
    const VISA = 'Visa';
    const DCI = 'Diners Club';
    const JCB = 'JCB';
    const DC = 'Discover';
    const MC = 'MasterCard';

    public static function getCardType($number)
    {
        //http://wephp.co/detect-credit-card-type-php/
        $number = preg_replace('/[^\d]/', '', $number);
        if (preg_match('/^3[47][0-9]{13}$/', $number)) {
            return self::AMEX;
        } elseif (preg_match('/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/', $number)) {
            return self::DCI;
        } elseif (preg_match('/^6(?:011|5[0-9][0-9])[0-9]{12}$/', $number)) {
            return self::DC;
        } elseif (preg_match('/^(?:2131|1800|35\d{3})\d{11}$/', $number)) {
            return self::JCB;
        } elseif (preg_match('/^5[1-5][0-9]{14}$/', $number)) {
            return self::MC;
        } elseif (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $number)) {
            return self::VISA;
        }
        return 'Unknown';
    }
}