<?php
namespace App\Model;

use App\model\SecurityToken;
use \PDO;

class PDOUserModel implements UserModel
{

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function idExists($id)
    {
        $this->validateId($id);
        $pdo = $this->connection->getPDO();

        $statement = $pdo->prepare('SELECT * FROM user WHERE id=:id');
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->execute();

        $exists = false;
        if ($statement->fetch(\PDO::FETCH_BOUND)) {
            $exists = true;
        }
        return $exists;
    }

    public function addUser($username, $password, $role)
    {
        $this->validateLength($username);
        $this->validateLength($password);
        $this->validateLength($role);

        if ($this->getUsername($username) == false) {
            $hashedPassword = $this->hashPassword($password);
            $pdo = $this->connection->getPDO();
            $statement = $pdo->prepare('INSERT INTO user (role, username, password) VALUES (?, ?, ?)');
            $statement->execute([$role, $username, $hashedPassword]);
        }
    }

    public function getRole($id)
    {
        $this->validateId($id);
        $pdo = $this->connection->getPDO();

        $statement = $pdo->prepare('SELECT role FROM user WHERE id=:id');
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->execute();

        $role = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $role;
    }

    public function getMessageById($id)
    {
        $this->validateId($id);
        $pdo = $this->connection->getPDO();

        $statement = $pdo->prepare('SELECT * FROM message WHERE user_id=:id');
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->execute();

        $message = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $message;
    }

    public function getUsername($username)
    {
        $this->validateLength($username);
        $pdo = $this->connection->getPDO();

        $statement = $pdo->prepare('SELECT * FROM user WHERE username=:username');
        $statement->bindParam(':username', $username, \PDO::PARAM_STR);
        $statement->execute();

        $exists = false;
        if ($statement->fetch(\PDO::FETCH_BOUND)) {
            $exists = true;
        }
        return $exists;
    }

    public function getPassword($username)
    {
        $this->validateLength($username);
        $pdo = $this->connection->getPDO();

        $statement = $pdo->prepare('SELECT password FROM user WHERE username=:username');
        $statement->bindParam(':username', $username, \PDO::PARAM_STR);
        $statement->execute();

        $password = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $password;
    }

    private function validateId($id)
    {
        if (!(is_string($id) && preg_match('/^[0-9]+$/', $id) && (int)$id > 0)) {
            throw new \InvalidArgumentException('id moet een int > 0 bevatten');
        }
    }

    private function validateLength($name)
    {
        if (!(is_string($name) && strlen($name) >= 2)) {
            throw new \InvalidArgumentException('moet minstens 2 karakters zijn');
        }
    }

    private function hashPassword($password)
    {
        $options = ['cost' => 10];
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, $options);

        return $hashedPassword;
    }
}
