<?php
namespace tdt4237\webapp\repository;

require dirname('.').'/src/webapp/repository/RepositoryInterface.php';

use PDO;
use tdt4237\webapp\models\ThrottleEntry;
use tdt4237\webapp\repository\RepositoryInterface;

class BannedRepository implements RepositoryInterface
{
    const INSERT_QUERY = "INSERT INTO banned(ip) VALUES(?)";
    const FIND_BY_IP = "SELECT * FROM banned WHERE ip = ?";

    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function find($id) { }
    public function save($bannedEntry) { }
    public function remove($id) { }

    public function findAll()
    {
        $stmt = $this->pdo->prepare(self::FIND_ALL);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        if (!$rows)
            return [];
        else 
            return $rows;
    }

    public function findByIp($ip)
    {
        $stmt = $this->pdo->prepare(self::FIND_BY_IP);
        $stmt->execute(array($ip));
        return $stmt->fetchAll();
    }

    public function saveNewEntry($ip)
    {
        $stmt = $this->pdo->prepare(self::INSERT_QUERY);
        return $stmt->execute(array(
            $ip,
        ));
    }
}
