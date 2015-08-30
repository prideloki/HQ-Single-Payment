<?php

class PaymentTest extends \LocalWebTestCase
{
    private $defaultParams;

    public function setup()
    {
        parent::setup();
        $this->defaultParams = $params = array(
            'amount' => '1',
            'full-name' => 'John Doe',
            'card-holder-name' => 'Alice Smith',
            'ccv' => '123'
        );
    }

    public function test_index_page()
    {
        $this->client->get('/');
        $this->assertEquals(200, $this->client->response->status());
    }

    public function test_should_error_when_use_AMEX_with_non_USD()
    {

        $params = array(
            'credit-card-number' => '378282246310005',//AMEX
            'expiration-date-month' => '09',
            'expiration-date-year' => '2020',
        );
        $params = array_merge($this->defaultParams, $params);

        $currencies = ['THB', 'AUD', 'SGD', 'HKD', 'EUR'];

        $expectedResult = 'errors';
        $expectedMsg = 'American Express card should be used only for USD.';

        $this->pay($params,$currencies,$expectedResult,$expectedMsg);

    }

    public function test_should_pass_with_paypal_payment()
    {
        //PayPal SANDBOX CC Number
        $paypalCCNumber = '4032034421465561';
        $params = array(
            'credit-card-number' => $paypalCCNumber,
            'expiration-date-month' => '09',
            'expiration-date-year' => '2020',
        );

        $params = array_merge($this->defaultParams, $params);

        $currencies = ['USD', 'AUD', 'EUR'];

        $expectedResult = 'success';
        $expectedMsg = 'Successfully';

        $this->pay($params, $currencies, $expectedResult, $expectedMsg);

    }

    public function test_should_pass_with_braintree_payment()
    {
        //Braintree SANDBOX CC Number
        $braintreeCCNumber = '4111111111111111';
        $params = array(
            'credit-card-number' => $braintreeCCNumber,
            'expiration-date-month' => '09',
            'expiration-date-year' => '2020',
            'payment_method_nonce'=>\Braintree_Test_Nonces::$transactable
        );

        $params = array_merge($this->defaultParams, $params);

        $currencies = ['THB', 'HKD', 'SGD'];

        $expectedResult = 'success';
        $expectedMsg = 'Successfully';

        $this->pay($params, $currencies, $expectedResult, $expectedMsg);

    }

    private function pay($params, $currencies, $result = 'errors', $expectedMsg)
    {

        foreach ($currencies as $currency) {
            $params = array_merge($params, array('currency' => $currency));

            $this->client->post('/', $params);

            $slimSession = json_decode($this->client->response->cookies->get('slim_session')['value'], true);

            $actualMessage = '';

            if ($result == 'errors') {
                $actualMessage = $slimSession['slim.flash']['errors'][0];
            } else if ('success') {
                $actualMessage = $slimSession['slim.flash']['success'];
            }
            $this->assertContains($expectedMsg, $actualMessage);
        }
    }

}