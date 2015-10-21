<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\Age;
use tdt4237\webapp\models\Email;
use tdt4237\webapp\models\NullUser;
use tdt4237\webapp\models\User;

class UserRepository
{
    const INSERT_QUERY      = "INSERT INTO users(username, password, email, age, bio, isadmin, fullname, address, postcode) VALUES(?,?,?,?,?,?,?,?,?)";
    const UPDATE_QUERY      = "UPDATE users SET email=?, age=?, bio=?, isadmin=?, fullname =?, address = ?, postcode = ?, accountNumber = ?, password = ? WHERE userId=?";
    const FIND_BY_ID        = "SELECT * FROM users WHERE userId=?";
    const FIND_BY_USERNAME  = "SELECT * FROM users WHERE username=?";
    const DELETE_BY_ID      = "DELETE FROM users WHERE userId=?";
    const SELECT_ALL        = "SELECT * FROM users";
    const FIND_FULL_NAME    = "SELECT * FROM users WHERE userId=?";
    const SET_DOCTOR       = "UPDATE users SET isdoctor =? WHERE userId =?";

    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function makeUserFromRow(array $row)
    {
        $user = new User($row['username'], $row['password'], $row['fullname'], $row['address'], $row['postcode']);
        $user->setUserId($row['userId']);
        $user->setFullname($row['fullname']);
        $user->setAddress(($row['address']));
        $user->setPostcode((($row['postcode'])));
        $user->setBio($row['bio']);
        $user->setIsAdmin($row['isadmin']);
        $user->setIsDoctor($row['isdoctor']);
        $decryptedAccountNumber = self::decrypt("brannmann2",$row['accountNumber']); 
        $user->setAccountNumber($decryptedAccountNumber);


        if (!empty($row['email'])) {
            $user->setEmail(new Email($row['email']));
        }

        if (!empty($row['age'])) {
            $user->setAge(new Age($row['age']));
        }

        return $user;
    }

    public function getNameByUserId($userId)
    {
        $stmt = $this->pdo->prepare(self::FIND_FULL_NAME);
        $stmt->execute(array($userId));
        $row = $stmt->fetch();
        return $row['fullname'];
    }

    public function findByUserId($userId)
    {
        try{
            $userId = (int) $userId;
        }
        catch(Exception $e) {
            return false;
        }

        $stmt = $this->pdo->prepare(self::FIND_BY_ID);
        $stmt->execute(array($userId));
        $row = $stmt->fetch();
        
        if ($row === false) {
            return false;
        }

        return $this->makeUserFromRow($row);
    }

    public function findByUsername($username)
    {
        $stmt = $this->pdo->prepare(self::FIND_BY_USERNAME);
        $stmt->execute(array($username));
        $row = $stmt->fetch();
        
        if ($row === false) {
            return false;
        }

        return $this->makeUserFromRow($row);
    }

    public function deleteByUserId($userId)
    {
        $stmt = $this->pdo->prepare(self::DELETE_BY_ID);
        return $stmt->execute(array($userId));
    }



    public function all()
    {
        $stmt = $this->pdo->prepare(self::SELECT_ALL);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        
        if ($rows === null) {
            return [];
            throw new \Exception('PDO error in all()'); //never called?
        }

        return array_map([$this, 'makeUserFromRow'], $rows);
    }

    public function save(User $user)
    {
        if ($user->getUserId() === null) {
            return $this->saveNewUser($user);
        }

        $this->saveExistingUser($user);
    }

    public function saveNewUser(User $user)
    {
        $stmt = $this->pdo->prepare(self::INSERT_QUERY);
        return $stmt->execute(array($user->getUsername(), $user->getHash(), $user->getEmail(), $user->getAge(), $user->getBio(), $user->isAdmin(), $user->getFullname(), $user->getAddress(), $user->getPostcode()));
    }

    public function saveExistingUser(User $user)
    {
        $stmt = $this->pdo->prepare(self::UPDATE_QUERY);
        $encryptedAccountNumber = self::encrypt("brannmann2",$user->getAccountNumber());
        return $stmt->execute(array($user->getEmail(), $user->getAge(), $user->getBio(), $user->isAdmin(), $user->getFullname(), $user->getAddress(), $user->getPostcode(), $encryptedAccountNumber, $user->getHash(), $user->getUserId()));
    }


    public function makeDoctor($userId)
    {
        $stmt = $this->pdo->prepare(self::SET_DOCTOR);
        return $stmt->execute(array(1,$userId));
    }

    public function removeDoctor($userId)
    {
        $stmt = $this->pdo->prepare(self::SET_DOCTOR);
        return $stmt->execute(array(0,$userId));
    }

    public static function encrypt($key, $decrypted){
        return openssl_encrypt($decrypted, 'AES-128-CBC', $key);
    }

    public static function decrypt($key, $encrypted){
        if ($encrypted)
            return openssl_decrypt($encrypted, 'AES-128-CBC', $key);
        else 
            return "";
    }

}
