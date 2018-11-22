<?php
namespace App\Model;

interface UserModel
{
    public function idExists($id);

    public function addUser($username, $password, $role);

    public function getRole($id);
    public function getMessageById($id);
    public function getUsername($username);
    public function getPassword($username);
    public function setPicture($id, $content);
    public function removePicture($id);
    public function getPicture($id);
}
