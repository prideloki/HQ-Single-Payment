<?php
/**
 * Created by PhpStorm.
 * User: thanakom
 * Date: 8/30/15 AD
 * Time: 7:20 PM
 */

namespace App;


class SQLiteHelper
{
    private $pdo;

    public function __construct($filePath)
    {
        try {
            $this->pdo = new \PDO('sqlite:' . $filePath);
        } catch (\PDOException $e) {
            $logger = \Slim\Slim::getInstance()->getLog();
            $logger->error($e->getMessage());
        }

    }

    public function insert($fullName, $amount, $currency, $response)
    {
        $tableName = 'payment_responses';

        $sql = "INSERT INTO {$tableName} (full_name,amount,currency,response) VALUES (:fullName,:amount,:currency,:response)";
        $query = $this->pdo->prepare($sql);
        $rows = $query->execute(array(
            'fullName' => $fullName,
            'amount' => $amount,
            'currency' => $currency,
            'response' => $response
        ));

        return $rows;

    }
}