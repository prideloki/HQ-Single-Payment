<?php 
namespace App\Payment;

interface PaymentAdapter {
	public function pay($info);
}