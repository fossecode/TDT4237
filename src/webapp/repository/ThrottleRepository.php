<?php
namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\ThrottleEntry;

class ThrottleRepository
{
    const INSERT_QUERY   = "INSERT INTO throttling(userId, ip, timestamp) VALUES(?, ?, ?)";
    const FIND_ALL_BY_IP = "SELECT * FROM throttling WHERE ip = ?";

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
        return new ThrottleEntry($row['userId'], $row['ip'], $row['timestamp']);
    }

    public function findAllByIP($ip)
    {
        $stmt = $this->pdo->prepare(self::FIND_ALL_BY_IP);
        $stmt->execute(array($ip));
        $rows = $stmt->fetchAll();
        if (!$rows)
            return [];
        else 
            return array_map([$this, 'makeThrottleEntryFromRow'], $rows);
    }

    public function saveNewEntry(ThrottleEntry $throttleEntry)
    {
        $stmt = $this->pdo->prepare(self::INSERT_QUERY);
        return $stmt->execute(array(
            $throttleEntry->getUserId(),
            $throttleEntry->getIP(),
            $throttleEntry->getTimestamp()
        ));
    }
}
