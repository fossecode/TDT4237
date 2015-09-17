<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\Comment;

class CommentRepository
{

    /**
     * @var PDO
     */
    private $db;

    const SELECT_BY_ID = "SELECT * FROM moviereviews WHERE id = %s";

    public function __construct(PDO $db)
    {

        $this->db = $db;
    }

    public function save(Comment $comment)
    {
        $id = $comment->getCommentId();
        $author  = $comment->getAuthor();
        $text    = $comment->getText();
        $date = (string) $comment->getDate();
        $postid = $comment->getPost();



        if ($comment->getCommentId() === null) {
            $query = "INSERT INTO comments (author, text, date, belongs_to_post) "
                . "VALUES ('$author', '$text', '$date', '$postid')";
            return $this->db->exec($query);
        }
    }

    public function findByPostId($postId)
    {
        $query   = "SELECT * FROM comments WHERE belongs_to_post = $postId";
        $rows = $this->db->query($query)->fetchAll();

        return array_map([$this, 'makeFromRow'], $rows);
    }

    public function makeFromRow($row)
    {
        $comment = new Comment;
        
        return $comment
            ->setCommentId($row['commentId'])
            ->setAuthor($row['author'])
            ->setText($row['text'])
            ->setDate($row['date'])
            ->setPost($row['belongs_to_post']);
    }
}
