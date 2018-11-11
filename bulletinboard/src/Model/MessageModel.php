<?php
namespace App\Model;

interface MessageModel
{
    public function idExists($id);

    public function addMessage($userId, $content, $title, $category);

    public function getAllMessages();
    public function getMessagesByKeywords($keywords);
    public function getMessagesByCategoryAndKeywords($category, $keywords);
    public function getContent($id);
    public function getCategory($id);
    public function getUserById($id);
    public function getUpvotes($id);
    public function getDownvotes($id);

    public function increaseUpvotes($id);
    public function increaseDownvotes($id);
}
