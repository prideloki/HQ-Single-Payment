<?php
namespace App\Payment\Paypal;

use PayPal\Api\Amount;
use PayPal\Api\CreditCard;
use PayPal\Api\FundingInstrument;

use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;

use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

use App\Config as Config;


class Paypal
{
    private $apiContext;

    public function __construct()
    {
        $clientId = Config::PAYPAL_CLIENT_ID;
        $clientSecret = Config::PAYPAL_CLIENT_SECRET;
        $this->apiContext = $this->getApiContext($clientId, $clientSecret);


    }

    private function getApiContext($clientId, $clientSecret)
    {

        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $clientId,
                $clientSecret
            )
        );

        return $apiContext;
    }

    public function createCreditCard($creditCardNumber, $firstName, $lastName, $expireMonth, $expireYear, $ccv, $type = "visa")
    {
        $card = new CreditCard();
        $card->setType($type)
            ->setNumber($creditCardNumber)
            ->setExpireMonth($expireMonth)
            ->setExpireYear($expireYear)
            ->setCvv2($ccv)
            ->setFirstName($firstName)
            ->setLastName($lastName);
        return $card;
    }

    public function createTransaction($price, $currency, $invoiceNumber = '')
    {

        $amount = new Amount();
        $amount->setCurrency($currency)
            ->setTotal($price);

        if (empty($invoiceNumber)) {
            $invoiceNumber = uniqid();
        }
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setInvoiceNumber($invoiceNumber);
        return $transaction;
    }

    public function createPayment($card, $transaction)
    {

        $fi = new FundingInstrument();
        $fi->setCreditCard($card);

        $payer = new Payer();
        $payer->setPaymentMethod("credit_card")
            ->setFundingInstruments(array($fi));


        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setTransactions(array($transaction));

        $payment->create($this->apiContext);

        return $payment;

    }
}