<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\Comment;

class CommentRepository
{
    const INSERT_COMMENT = "INSERT INTO comments (authorId, text, date, postId) VALUES (?,?,?,?);";
    const GET_COMMENTS = "SELECT * FROM comments NATURAL JOIN users WHERE postId = ?";

    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {

        $this->pdo = $pdo;
    }

    public function save(Comment $comment)
    {
        print_r($comment);
        $id = $comment->getCommentId();
        $authorId = $comment->getAuthorId();
        $text = $comment->getText();
        $date = (string) $comment->getDate();
        $postid = $comment->getPost();

        if ($comment->getCommentId() === null) {
            $stmt = $this->pdo->prepare(self::INSERT_COMMENT);
            return $stmt->execute(array($authorId, $text, $date, $postid));
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
            ->setAuthor($row['user'])
            ->setText($row['text'])
            ->setDate($row['date'])
            ->setPost($row['postId']);
    }
}
