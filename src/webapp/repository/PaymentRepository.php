<?php

namespace tdt4237\webapp\repository;

use PDO;

use tdt4237\webapp\models\User;

class PaymentRepository
{
    const DOCTOR_ANSWER_COUNT     = "SELECT count(*) FROM payments WHERE doctorId = ?";
    const USER_PAYMENT_COUNT      = "SELECT count(*) FROM payments NATURAL JOIN posts WHERE userId = ?";
    const INSERT_PAYMENT          = "INSERT OR IGNORE INTO payments (doctorId, postId) VALUES (?,?)";
   

    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getDoctorPayments ($doctorId){
    	$stmt = $this->pdo->prepare(self::DOCTOR_ANSWER_COUNT);
    	$stmt->execute(array($doctorId));
        $result = $stmt->fetchColumn();
        return ($result *7); # every answer is 7$.
    }

    public function getUserPayments ($userId){
    	$stmt = $this->pdo->prepare(self::USER_PAYMENT_COUNT);
    	$stmt->execute(array($userId));
        $result = $stmt->fetchColumn();
        return ($result * 10); # every question cost 10$
    }

    public function insertPayment ($doctorId, $postId){
        try{
            $stmt = $this->pdo->prepare(self::INSERT_PAYMENT);
            $stmt->execute(array($doctorId, $postId));
        }
        catch(PDOException $e){
            print_r("User has already paid for doctor answer.");
        }
    }
}
