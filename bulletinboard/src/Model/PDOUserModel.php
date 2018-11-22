<?php
namespace App\Model;

use App\model\SecurityToken;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gaufrette\Filesystem;
use Gaufrette\Adapter\Local as LocalAdapter;
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

    public function setPicture($id, $content)
    {
        $this->validateId($id);

        $adapter = new LocalAdapter('/var/images');
        $filesystem = new Filesystem($adapter);
        
        $pdo = $this->connection->getPDO();

        $statement = $pdo->prepare('SELECT image_id FROM user WHERE id=:id');
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->execute();

        $user = $statement->fetchAll(PDO::FETCH_ASSOC);

        do{
            $name = uniqid('image');
        }while($filesystem->has($name));

        $statement = $pdo->prepare("INSERT INTO image (picture) VALUES (:image)");
        $statement->bindParam(':image', $name);
        $statement->execute();

        $statement = $pdo->prepare('SELECT LAST_INSERT_ID()');
        $statement->execute();

        $lastId = $statement->fetchColumn();

        $statement = $pdo->prepare('UPDATE user SET image_id =:imageId WHERE id=:id');
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->bindParam(':imageId', $lastId, \PDO::PARAM_INT);
        $statement->execute();

        $filesystem->write($name, $content);

        return $lastId;
    }

    public function removePicture($id){
        $this->validateId($id);
        
        $pdo = $this->connection->getPDO();

        $statement = $pdo->prepare('SELECT image_id FROM user WHERE id=:id');
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->execute();

        $user = $statement->fetchColumn(0);

        $statement = $pdo->prepare('UPDATE user SET image_id = 1 WHERE id=:id');
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->execute();

        if($user!=1)
        {
            $statement = $pdo->prepare("DELETE FROM image WHERE id=:imageId");
            $statement->bindParam(':imageId', $user, \PDO::PARAM_INT);
            $statement->execute();
        }

        return "";
    }

    public function getPicture($id)
    {
        $this->validateId($id);

        $adapter = new LocalAdapter('/var/images');
        $filesystem = new Filesystem($adapter);

        $pdo = $this->connection->getPDO();

        $statement = $pdo->prepare('SELECT image_id FROM user WHERE id=:id');
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->execute();

        $user = $statement->fetchColumn(0);

        $statement = $pdo->prepare("Select picture FROM image WHERE id=:imageId");
        $statement->bindParam(':imageId', $user, \PDO::PARAM_INT);
        $statement->execute();

        $fileName = $statement->fetchColumn(0);

        return $filesystem->read($fileName);
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
