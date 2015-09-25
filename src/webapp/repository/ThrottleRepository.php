<?php
namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\ThrottleEntry;

class ThrottleRepository
{
    const FIND_BY_ID = "SELECT * FROM throttling WHERE ip = ?";

    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function makeThrottleEntryFromRow(array $row)
    {
        return new ThrottleEntry($row['id'], $row['ip']);
    }

    public function findByIP($ip)
    {
        $stmt = $this->pdo->prepare(self::FIND_BY_IP);
        $stmt->execute(array($ip));
        $row = $stmt->fetch();
        if ($row === false) {
            return false;
        }
        return $this->makeThrottleEntryFromRow($row);
    }

}
