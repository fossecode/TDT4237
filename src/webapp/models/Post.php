<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 26.08.2015
 * Time: 01:04
 */

namespace tdt4237\webapp\models;

class Post
{
    protected $postId;
    protected $user;
    protected $title;
    protected $content;
    protected $date;
    protected $answeredByDoc;
    protected $paidQuestion;

    public function getPostId() {
        return $this->postId;

    }

    public function setPostId($postId) {
        $this->postId = $postId;
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

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function getTitle() {
        return $this->title;
    }

    public function isPaymentPost(){
        if($this->user->getAccountNumber() == null || $this->user->getAccountNumber() == "")
            return false;
        return true;
    }

    public function setAnsweredByDoc($bool){
        $this->answeredByDoc = $bool;
        return $this;
    }

    public function isAnsweredByDoc(){
        return $this->answeredByDoc;
    }

    public function setPaidQuestion($bool){
        $this->paidQuestion = $bool;
        return $this;
    }

    public function isPaidQuestion(){
        return $this->paidQuestion;
    }

}