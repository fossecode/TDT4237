<?php
namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\ThrottleEntry;
use tdt4237\webapp\repository\RepositoryInterface;
use tdt4237\webapp\WAF;
use tdt4237\webapp\Client;

class ThrottleRepository implements RepositoryInterface
{
    const INSERT_QUERY = "INSERT INTO banned(ip) VALUES(?)";
    const FIND_ALL = "SELECT * FROM banned";

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

    public function makeBannedEntryFromRow(array $row)
    {
        return new Client($row['ip']);
    }

    public function findAll()
    {
        $stmt = $this->pdo->prepare(self::FIND_ALL);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        if (!$rows)
            return [];
        else 
            return array_map([$this, 'makeBannedEntryFromRow'], $rows);
    }

    public function saveNewEntry(Client $bannedEntry)
    {
        $stmt = $this->pdo->prepare(self::INSERT_QUERY);
        return $stmt->execute(array(
            $bannedEntry->ip,
        ));
    }
}
