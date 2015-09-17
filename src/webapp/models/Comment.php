<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 26.08.2015
 * Time: 01:04
 */

namespace tdt4237\webapp\models;

class Comment
{
    protected $commentId;
    protected $author;
    protected $text;
    protected $date;
    protected $belongs_to_post;


    public function getCommentId() {
        return $this->commentId;

    }

    public function setCommentId($postId) {
        $this->commentId = $postId;
        return $this;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function setAuthor($author) {
        $this->author = $author;
        return $this;
    }

    public function getDate() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
        return $this;
    }

    public function getText() {
        return $this->text;
    }

    public function setText($text) {
        $this->text = $text;
        return $this;
    }

    public function getPost() {
        return $this->belongs_to_post;
    }

    public function setPost($postId) {
        $this->belongs_to_post = $postId;
        return $this;

    }






}