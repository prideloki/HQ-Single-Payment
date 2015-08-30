<?php
namespace App\Payment\BrainTree;

use App\Config;

class BrainTree
{
    public function __construct()
    {
        \Braintree_Configuration::environment(Config::BRAINTREE_MODE);
        \Braintree_Configuration::merchantId(Config::BRAINTREE_MERCHANT_ID);
        \Braintree_Configuration::publicKey(Config::BRAINTREE_PUBLIC_KEY);
        \Braintree_Configuration::privateKey(Config::BRAINTREE_PRIVATE_KEY);
    }

    public function generateClientToken()
    {
        $clientToken = \Braintree_ClientToken::generate();
        return $clientToken;
    }

    public function createTransaction($amount = 0, $nonce)
    {
        $result = \Braintree_Transaction::sale(array(
            'amount' => $amount,
            'paymentMethodNonce' => $nonce
        ));


        return $result;
    }

}