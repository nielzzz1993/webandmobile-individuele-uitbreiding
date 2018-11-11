<?php
namespace App\Model;

use App\model\Connection;
use App\model\SecurityToken;
use \PDO;

class PDOMessageModel implements MessageModel
{
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getAllMessages()
    {
        $pdo = $this->connection->getPDO();
        $statement = $pdo->prepare('SELECT * FROM message');
        $statement->execute();
        $message = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $message;
    }

    public function getMessagesByKeywords($keyword)
    {
        $this->validateKeywords($keyword);
        $pdo = $this->connection->getPDO();
        $sql = $this->splitKeyword($keyword);

        $statement = $pdo->prepare('SELECT * FROM message WHERE (content LIKE '.$sql.') ');
        $statement->execute();
        $message = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $message;
    }

    public function idExists($id)
    {
        $this->validateId($id);
        $pdo = $this->connection->getPDO();
        $statement = $pdo->prepare('SELECT * FROM message WHERE id=:id');
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->execute();
        $exists = false;

        if ($statement->fetch(\PDO::FETCH_BOUND)) {
            $exists = true;
        }

        return $exists;
    }

    public function addMessage($userId, $title, $content, $category)
    {
        try {
            $this->validateId($userId);
            $this->validateLength($title);
            $this->validateLength($content);
            $this->validateLength($category);
            $pdo = $this->connection->getPDO();

            $statement = $pdo->prepare('INSERT INTO message (
            content, title, category, user_id, upvotes, downvotes)
            VALUES (?, ?, ?, ?, 0, 0)');

            $statement->execute([$content, $title, $category, $userId]);
        } catch (\InvalidArgumentException $e) {
            return $e->getMessage();
        }
    }

    public function getMessagesByCategoryAndKeywords($category, $keyword)
    {
        $this->validateLength($category);
        $this->validateKeywords($keyword);
        $pdo = $this->connection->getPDO();
        $sql = $this->splitKeyword($keyword);
        $statement = $pdo->prepare('SELECT *
          FROM message WHERE category=:category
          AND (content LIKE '.$sql.') ');
        $statement->bindParam(':category', $category, \PDO::PARAM_STR);
        $statement->execute();
        $message = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $message;
    }

    public function getContent($id)
    {
        $this->validateId($id);
        $pdo = $this->connection->getPDO();
        $statement = $pdo->prepare('SELECT content FROM message WHERE id=:id');

        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->execute();
        $content = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $content;
    }

    public function getCategory($id)
    {
        $this->validateId($id);
        $pdo = $this->connection->getPDO();
        $statement = $pdo->prepare('SELECT category FROM message WHERE id=:id');

        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->execute();
        $category = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $category;
    }

    public function getUserById($id)
    {
        $this->validateId($id);
        $pdo = $this->connection->getPDO();

        $statement = $pdo->prepare('SELECT user_id FROM message WHERE id=:id');
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->execute();

        $userId = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement = $pdo->prepare('SELECT * FROM user WHERE id=:id');
        $statement->bindParam(':id', $userId[0]['user_id'], \PDO::PARAM_INT);
        $statement->execute();

        $user = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $user;
    }

    public function getUpvotes($id)
    {
        $this->validateId($id);
        $pdo = $this->connection->getPDO();

        $statement = $pdo->prepare('SELECT upvotes FROM message WHERE id=:id');
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->execute();

        $upvotes = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $upvotes;
    }

    public function getDownvotes($id)
    {
        $this->validateId($id);
        $pdo = $this->connection->getPDO();

        $statement = $pdo->prepare('SELECT downvotes FROM message WHERE id=:id');
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->execute();

        $downvotes = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $downvotes;
    }

    public function increaseUpvotes($id)
    {
        $this->validateId($id);
        $pdo = $this->connection->getPDO();

        $statement = $pdo->prepare('UPDATE message
          SET upvotes = upvotes+1 WHERE id=:id');
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }

    public function increaseDownvotes($id)
    {
        $this->validateId($id);
        $pdo = $this->connection->getPDO();

        $statement = $pdo->prepare('UPDATE message
          SET downvotes = downvotes+1 WHERE id=:id');
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }

    public function getMessageById($id)
    {
        $this->validateId($id);
        $pdo = $this->connection->getPDO();

        $statement = $pdo->prepare('SELECT * FROM message WHERE id=:id');
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->execute();
        $message = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $message;
    }

    private function validateId($id)
    {
        if (!(is_string($id) && preg_match('/^[0-9]+$/', $id) && (int)$id > 0)) {
            throw new \InvalidArgumentException('id moet een int > 0 bevatten');
        }
    }

    private function validateLength($name)
    {
        if (!(is_string($name) && preg_match('/^[a-zA-Z0-9,?\s]+$/', $name) && strlen($name) >= 2)) {
            throw new \InvalidArgumentException('moet minstens 2 karakters zijn');
        }
    }

    private function validateKeywords($keywords)
    {
        if (!(is_string($keywords) && preg_match('/^[a-zA-Z0-9,?\s]+$/', $keywords) && strlen($keywords) >= 1)) {
            throw new \InvalidArgumentException('keywords mag niet leeg zijn');
        }
    }

    private function splitKeyword($keyword)
    {
        $sql = '';
        $keywords = explode(',', $keyword);

        $sql .= '"%' . $keywords[0] . '%"';
        for ($index = 1; $index < (count($keywords) ); $index++) {
            $singleKeyword = $keywords[$index];
            $sql .= ' OR content LIKE "%' . $singleKeyword . '%"';
        }
        return $sql;
    }
}
