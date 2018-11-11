<?php
namespace App\Model;

interface ReactionModel
{

    public function addReaction($messageId, $content);

    public function generateToken();

    public function getContent($id);
    public function getToken($id);
    public function getMessageById($id);
}
