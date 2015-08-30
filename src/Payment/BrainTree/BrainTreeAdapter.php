<?php 
namespace App\Payment\BrainTree;

class BrainTreeAdapter implements \App\Payment\PaymentAdapter
{
    const AMOUNT = 'amount';
    const NONCE = 'nonce';
	private $brainTree;
	function __construct(BrainTree $brainTree) {
		$this->brainTree = $brainTree;
	}
	public function generateToken(){
        $token = $this->brainTree->generateClientToken();
        return $token;
    }
	public function pay($info){
        $amount = $info[self::AMOUNT];
        $nonce = $info[self::NONCE];
        $payment = $this->brainTree->createTransaction($amount,$nonce);
        return $payment;
	}
}