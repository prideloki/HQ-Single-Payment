<?php


use App\Payment\BrainTree;
use App\Payment\Paypal;

$view = $app->view();
$view->parserOptions = array(
    'debug' => true
);

$view->parserExtensions = array(
    new \Slim\Views\TwigExtension()
);

$app->add(new \Slim\Middleware\SessionCookie());


$app->get('/', function () use ($app) {
    $brainTreeAdapter = new BrainTree\BrainTreeAdapter(new BrainTree\BrainTree());
    $brainTreeClientToken = $brainTreeAdapter->generateToken();
    $app->render('home.twig', array('braintreeToken' => $brainTreeClientToken));
})->name('home');

$app->post('/', function () use ($app) {
    $inputs = $app->request->post();

    list($firstName, $lastName) = App\StringUtil::parseFullName($inputs['full-name']);
    $amount = doubleval($inputs['amount']);
    $currency = $inputs['currency'];

    $cardType = App\Payment\PaymentUtil::getCardType($inputs['credit-card-number']);

    if ($cardType == App\Payment\PaymentUtil::AMEX && $currency !== 'USD') {
        $app->flash('errors', array('Error, American Express card should be used only for USD. ' . $currency . ' was used instead.'));
        $app->redirect($app->urlFor('home'));
    }

    $dbFilePath = __DIR__ . '/db';
    $sqliteHelper = new App\SQLiteHelper($dbFilePath);

    switch ($currency) {
        case 'USD':
        case 'EUR':
        case 'AUD':
            $orderInfo = array(
                Paypal\PaypalAdapter::ORDER_FIRST_NAME => $firstName,
                Paypal\PaypalAdapter::ORDER_LAST_NAME => $lastName,
                Paypal\PaypalAdapter::ORDER_AMOUNT => $amount,
                Paypal\PaypalAdapter::ORDER_CURRENCY => $currency,
            );

            list($cardFirstName, $cardLastName) = App\StringUtil::parseFullName($inputs['card-holder-name']);
            $creditCardNumber = $inputs['credit-card-number'];
            $expirationMonth = intval($inputs['expiration-date-month']);
            $expirationYear = intval($inputs['expiration-date-year']);
            $ccv = $inputs['ccv'];

            $creditInfo = array(
                Paypal\PaypalAdapter::CREDIT_CARD_NUMBER => $creditCardNumber,
                Paypal\PaypalAdapter::CARD_FIRST_NAME => $cardFirstName,
                Paypal\PaypalAdapter::CARD_LAST_NAME => $cardLastName,
                Paypal\PaypalAdapter::CARD_EXP_MONTH => $expirationMonth,
                Paypal\PaypalAdapter::CARD_EXP_YEAR => $expirationYear,
                Paypal\PaypalAdapter::CARD_CVV => $ccv
            );


            $paypalAdapter = new Paypal\PaypalAdapter(new Paypal\Paypal());

            $result = $paypalAdapter->pay(array($creditInfo, $orderInfo));

            // Save both success and failed results
            $response = print_r($result, true);
            $fullName = $firstName . ' ' . $lastName;
            $sqliteHelper->insert($fullName, $amount, $currency, $response);

            if ($result) {

                $amount = $result->getTransactions()[0]->getAmount()->total;
                $app->flash('success', "Successfully, {$cardFirstName} {$cardLastName} paid {$amount} {$currency} for {$firstName} {$lastName}.");
                $app->redirect($app->urlFor('home'));
            } else {
                $app->flash('errors', array('Errors, while processing your transaction. Please try again.'));
                $app->redirect($app->urlFor('home'));
            }

            break;
        case 'THB':
        case 'HKD':
        case 'SGD':
            $nonce = $inputs['payment_method_nonce'];
            $brainTreeAdapter = new BrainTree\BrainTreeAdapter(new BrainTree\BrainTree());
            $result = $brainTreeAdapter->pay(array(
                BrainTree\BrainTreeAdapter::AMOUNT => $amount,
                BrainTree\BrainTreeAdapter::NONCE => $nonce
            ));

            // Save both success and failed results
            $response = print_r($result, true);
            $fullName = $firstName . ' ' . $lastName;
            $sqliteHelper->insert($fullName, $amount, $currency, $response);

            if ($result->success) {

                $app->flash('success', "Successfully, {$result->transaction->creditCardDetails->cardholderName} paid {$amount} {$currency} for {$firstName} {$lastName}.");
                $app->redirect($app->urlFor('home'));
                break;
            } else if ($result->transaction) {
                $app->flash('errors', array($result->message));
            } else {
                $errors = array('Error(s)');
                foreach (($result->errors->deepAll()) as $error) {
                    array_push($errors, $error->message);
                }
                $app->flash('errors', $errors);
            }
            $app->redirect($app->urlFor('home'));
            break;


    }

})->name('payment');




