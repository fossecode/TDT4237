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
    protected $user;
    protected $text;
    protected $date;
    protected $postId;


    public function getCommentId() {
        return $this->commentId;

    }

    public function setCommentId($postId) {
        $this->commentId = $postId;
        return $this;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser($user) {
        $this->user = $user;
        return $this;
    }

    public function getUserId() {
        return $this->user->getUserId();
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
        return $this->postId;
    }

    public function setPost($postId) {
        $this->postId = $postId;
        return $this;

    }
}