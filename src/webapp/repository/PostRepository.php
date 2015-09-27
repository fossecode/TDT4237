<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\Post;
use tdt4237\webapp\models\PostCollection;

class PostRepository
{
    const SELECT_POST   = "SELECT * FROM posts NATURAL JOIN users WHERE postId = ?;";
    const ALL_POSTS     = "SELECT * FROM posts NATURAL JOIN users;";
    const DELETE_POST   = "DELETE FROM posts WHERE postId = ?;";
    const INSERT_POST   = "INSERT INTO posts (title, userId, content, timestamp) VALUES (?,?,?,?)";

    /**
     * @var PDO
     */
    private $pdo;
    private $userRepository;

    public function __construct(PDO $pdo, UserRepository $userRepository)
    {
        $this->pdo = $pdo;
        $this->userRepository = $userRepository;
    }
    
    public static function create($id, $title, $content, $date, $user)
    {
        $post = new Post;
        
        return $post
            ->setPostId($id)
            ->setUser($user)
            ->setTitle($title)
            ->setContent($content)
            ->setDate($date);
    }

    public function find($postId)
    {
        $stmt = $this->pdo->prepare(self::SELECT_POST);
        $stmt->execute(array($postId));
        $rows = $stmt->fetch();

        if($rows === false) {
            return false;
        }
        return $this->makeFromRow($rows);
    }

    public function all()
    {
        $stmt = $this->pdo->prepare(self::ALL_POSTS);
        $stmt->execute(array());
        $rows = $stmt->fetchAll();

        if($rows === false) {
            return [];
            throw new \Exception('PDO error in posts all()');
        }

        if(count($rows) == 0) {
            return false;
        }

        return new PostCollection(
            array_map([$this, 'makeFromRow'], $rows)
        );
    }

    public function makeFromRow($row)
    {
        return static::create(
            $row['postId'],
            $row['title'],
            $row['content'],
            $row['timestamp'],
            $this->userRepository->findByUserId($row['userId'])
        );
    }

    public function deleteByPostid($postId)
    {
        $stmt = $this->pdo->prepare(self::DELETE_POST);
        return $stmt->execute(array($postId));
    }


    public function save(Post $post)
    {
        $title   = $post->getTitle();
        $userId = $post->getUserId();
        $content = $post->getContent();
        $date    = $post->getDate();

        if ($post->getPostId() === null) {
            $stmt = $this->pdo->prepare(self::INSERT_POST);
            $stmt->execute(array($title, $userId, $content, $date));
        }
        return $this->pdo->lastInsertId();
    }
}
