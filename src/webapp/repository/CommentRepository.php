<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\Comment;

class CommentRepository
{
    const INSERT_COMMENT = "INSERT INTO comments (userId, text, timestamp, postId) VALUES (?,?,?,?);";
    const GET_COMMENTS = "SELECT * FROM comments NATURAL JOIN users WHERE postId = ?";

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

    public function save(Comment $comment)
    {
        $id = $comment->getCommentId();
        $userId = $comment->getUserId();
        $text = $comment->getText();
        $date = (string) $comment->getDate();
        $postid = $comment->getPost();

        if ($comment->getCommentId() === null) {
            $stmt = $this->pdo->prepare(self::INSERT_COMMENT);
            return $stmt->execute(array($userId, $text, $date, $postid));
        }
    }

    public function findByPostId($postId)
    {
        $stmt = $this->pdo->prepare(self::GET_COMMENTS);
        $stmt->execute(array($postId));
        $rows = $stmt->fetchAll();
        return array_map([$this, 'makeFromRow'], $rows);
    }

    public function makeFromRow($row)
    {
        $comment = new Comment;
        
        return $comment
            ->setCommentId($row['commentId'])
            ->setUser($this->userRepository->findByUserId($row['userId']))
            ->setText($row['text'])
            ->setDate($row['timestamp'])
            ->setPost($row['postId']);
    }
}
