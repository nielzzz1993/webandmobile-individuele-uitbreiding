<?php
namespace App\Model;

use App\model\SecurityToken;
use \PDO;

class PDOReactionModel implements ReactionModel
{
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function addReaction($messageId, $content)
    {
        try {
            $this->validateLength($content);
            $this->validateId($messageId);

            $pdo = $this->connection->getPDO();
            $token = $this->generateToken();

            $statement = $pdo->prepare('INSERT INTO reaction (reaction, token, message_id) VALUES (?, ?, ?)');
            $statement->execute([$content, $token , $messageId]);

            return $token;
        } catch (\InvalidArgumentException $e) {
            return $e->getMessage();
        }
    }

    public function generateToken()
    {
        $token = '';
        $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codeAlphabet.= 'abcdefghijklmnopqrstuvwxyz';
        $codeAlphabet.= '0123456789';
        $max = strlen($codeAlphabet);

        for ($index = 0; $index < 245; $index++) {
            $token .= $codeAlphabet[random_int(0, $max-1)];
        }

        $token .= uniqid();

        return $token;
    }

    public function getContent($id)
    {
        $this->validateId($id);
        $pdo = $this->connection->getPDO();

        $statement = $pdo->prepare('SELECT id, reaction FROM reaction WHERE message_id=:id');
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->execute();

        $content = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $content;
    }

    public function getToken($id)
    {
        $this->validateId($id);
        $pdo = $this->connection->getPDO();

        $statement = $pdo->prepare('SELECT token FROM reaction WHERE id=:id');
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->execute();

        $token = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $token;
    }

    public function getMessageById($id)
    {
        $this->validateId($id);
        $pdo = $this->connection->getPDO();

        $statement = $pdo->prepare('SELECT message_id FROM reaction WHERE id=:id');
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->execute();

        $messageId = $statement->fetchAll(PDO::FETCH_ASSOC);

        $statement = $pdo->prepare('SELECT * FROM message WHERE id=:id');
        $statement->bindParam(':id', $messageId[0]['message_id'], \PDO::PARAM_INT);
        $statement->execute();

        $message = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $message;
    }

    private function validateLength($name)
    {
        if (!(is_string($name) && strlen($name) >= 2)) {
            throw new \InvalidArgumentException('moet minstens 2 karakters zijn');
        }
    }

    private function validateId($id)
    {
        if (!(is_string($id) && preg_match('/^[0-9]+$/', $id) && (int)$id > 0)) {
            throw new \InvalidArgumentException('id moet een int > 0 bevatten');
        }
    }
}
