<?php 
namespace App;
class Config {
    const DB_HOST = 'localhost';
    const DB_USER = 'username';
    const DB_PASS = 'password';

    const PAYPAL_MODE = 'sandbox';
    const PAYPAL_ENDPOINT = 'https://api.sandbox.paypal.com';
    const PAYPAL_CLIENT_ID = '<ID>';
    const PAYPAL_CLIENT_SECRET = '<SECRET>';

    const BRAINTREE_MODE = 'sandbox';
    const BRAINTREE_MERCHANT_ID = '<MERCHANT';
    const BRAINTREE_PUBLIC_KEY = 'PUBLIC_KEY';
    const BRAINTREE_PRIVATE_KEY = 'PRIVATE_KEY';
}
