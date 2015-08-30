<?php
namespace App\Payment\Paypal;

class PaypalAdapter implements \App\Payment\PaymentAdapter
{

    const CREDIT_CARD_NUMBER = 'credit-card-number';
    const CARD_FIRST_NAME = 'card-first-name';
    const CARD_LAST_NAME =  'card-last-name';
    const CARD_EXP_MONTH = 'expiration-date-month';
    const CARD_EXP_YEAR = 'expiration-date-year';
    const CARD_CVV = 'cvv';

    const ORDER_FIRST_NAME = 'order-first-name';
    const ORDER_LAST_NAME =  'order-last-name';
    const ORDER_AMOUNT = 'order-amount';
    const ORDER_CURRENCY = 'order-currency';

    private $paypal;

    function __construct(Paypal $paypal)
    {
        $this->paypal = $paypal;
    }

    public function pay($info)
    {
        $creditCardInfo = $info[0];
        $orderInfo = $info[1];

        $creditCardNumber = $creditCardInfo[self::CREDIT_CARD_NUMBER];
        $firstName = $creditCardInfo[self::CARD_FIRST_NAME];
        $lastName = $creditCardInfo[self::CARD_LAST_NAME];
        $expireMonth = $creditCardInfo[self::CARD_EXP_MONTH];
        $expireYear = $creditCardInfo[self::CARD_EXP_YEAR];
        $ccv = $creditCardInfo[self::CARD_CVV];


        $creditCard = $this->paypal->createCreditCard($creditCardNumber, $firstName, $lastName, $expireMonth, $expireYear, $ccv);

        $amount = $orderInfo[self::ORDER_AMOUNT];
        $currency = $orderInfo[self::ORDER_CURRENCY];
        $transaction = $this->paypal->createTransaction($amount, $currency);

        try {
        $payment = $this->paypal->createPayment($creditCard, $transaction);
        } catch (\PayPal\Exception\PayPalConnectionException $ex) {
            $logger = \Slim\Slim::getInstance()->getLog();
            $logger->error($ex->getCode());
            $logger->error($ex->getData());
            return false;
        }
        return $payment;
    }

}