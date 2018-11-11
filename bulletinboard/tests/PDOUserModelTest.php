<?php

namespace App\Tests\Util;

use App\Model\PDOUserModel;
use App\Model\Connection;
use PHPUnit\Framework\TestCase;
use \PDO;

class PDOUserModelTest extends TestCase
{
    public function setUp()
    {
        $this->connection = new Connection('sqlite::memory:');
        $this->connection->getPDO()->exec('CREATE TABLE message (
                        id INT,
                        content VARCHAR(1000),
                        category VARCHAR(100),
                        user_id INT,
                        upvotes INT,
                        downvotes INT,
                        PRIMARY KEY (id)
                   )');
        $this->connection->getPDO()->exec('CREATE TABLE user (
                        id INT,
                        role VARCHAR(30),
                        username VARCHAR(255),
                        password VARCHAR(1000)
                    )');

        $users=$this->providerUsers();
        $messages=$this->providerMessages();

        foreach ($messages as $message) {
            $this->connection->getPDO()->exec(
                "INSERT INTO message (id, content, category, user_id, upvotes, downvotes)
                VALUES (".$message['id'].", '".$message['content']."', '".$message['category']."',
                '".$message['user_id']."', '".$message['upvotes']."', '".$message['downvotes']."');"
            );
        }

        foreach ($users as $user) {
            $this->connection->getPDO()->exec("INSERT INTO user (id, role, username, password) VALUES
            (".$user['id'].", '".$user['role']."', '".$user['username']."', '".$user['password']."');");
        }
    }

    public function providerMessages()
    {
        return [
            ['id'=>'1', 'content'=>'dit is testcontent 1',
            'category'=>'news', 'user_id'=>'3', 'upvotes'=>'5', 'downvotes'=>'7'],

            ['id'=>'2', 'content'=>'dit is testcontent 2',
            'category'=>'games', 'user_id'=>'2', 'upvotes'=>'15', 'downvotes'=>'5'],

            ['id'=>'3', 'content'=>'dit is testcontent 3',
            'category'=>'tech', 'user_id'=>'1', 'upvotes'=>'23', 'downvotes'=>'3']
        ];
    }

    public function providerUsers()
    {
        return [
            ['id'=>'1', 'role'=>'anon', 'username'=>'user1', 'password'=>'pass1'],
            ['id'=>'2', 'role'=>'anon', 'username'=>'user2', 'password'=>'pass2'],
            ['id'=>'3', 'role'=>'mod', 'username'=>'user3', 'password'=>'pass3']
        ];
    }

    public function tearDown()
    {
        $this->connection = null;
    }

    public function providerValidExistingIds()
    {
        return [['1'], ['2'], ['3']];
    }

    public function providerValidUnexistingIds()
    {
        return [['4'], ['100'], ['1000']];
    }

    public function providerValidIds()
    {
        return [['1'], ['2'], ['3'], ['100'], ['1000']];
    }

    public function providerInvalidIds()
    {
        return [['0'], ['-1'], ['1.2'], ['aaa'], [12], [1.2]];
    }

    public function providerInvalidAddUser()
    {
        return [
            [0, '', 1.2],
            [-1, 1.2, ''],
            [1.2, 'a', 12],
            ['aaa', 12, 'a']
        ];
    }

    public function providerValidAddUser()
    {
        return [
            ['tes1', 'aa', 'aa'],
            ['rr2', 'Aea', 'Aa'],
            ['100', 'aa11', 'aa11'],
            ['1000', '11AA', '11AA']
        ];
    }

    public function providerInvalidUsernames()
    {
        return [[''], ['1'], [1], [1.2]];
    }

    public function providerValidUnexistingUsernames()
    {
        return [['user4'], ['user123'], ['lala'], ['test123']];
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider providerInvalidIds
     **/
    public function testIdExists_invalidId_InvalidArgumentException($id)
    {
        $userModel = new PDOUserModel($this->connection);
        $userModel->idExists($id);
    }

    /**
     * @dataProvider providerValidExistingIds
     **/
    public function testIdExists_existingId_True($id)
    {
        $userModel = new PDOUserModel($this->connection);
        $this->assertTrue($userModel->idExists($id));
    }

    /**
    * @dataProvider providerValidUnexistingIds
    **/
    public function testIdExists_unexistingId_False($id)
    {
        $userModel = new PDOUserModel($this->connection);
        $this->assertFalse($userModel->idExists($id));
    }

    /**
    * @expectedException \InvalidArgumentException
    * @dataProvider providerInvalidAddUser
    **/
    public function testAddUser_invalidUsername_invalidPassword_invalidRole_InvalidArgumentException($username, $password, $role)
    {
        $userModel = new PDOUserModel($this->connection);
        $userModel->addUser($username, $password, $role);
    }

    /**
    * @dataProvider providerValidAddUser
    **/
    public function testAddUser_validUsername_validPassword_validRole($username, $password, $role)
    {
        $userModel = new PDOUserModel($this->connection);
        try {
            $userModel->addUser($username, $password, $role);
        } catch (InvalidArgumentException $notExpected) {
             $this->fail();
        }
        $this->assertTrue(true);
    }

    /**
    * @expectedException \InvalidArgumentException
    * @dataProvider providerInvalidIds
    **/
    public function testGetRole_invalidId_InvalidArgumentException($id)
    {
        $userModel = new PDOUserModel($this->connection);
        $userModel->getRole($id);
    }

    public function testGetRole_validExistingId()
    {
        $userModel = new PDOUserModel($this->connection);
        $this->assertContains(['role'=>'anon'], $userModel->getRole('1'));
        $this->assertContains(['role'=>'anon'], $userModel->getRole('2'));
        $this->assertContains(['role'=>'mod'], $userModel->getRole('3'));
    }

    /**
     * @dataProvider providerValidUnexistingIds
     **/
    public function testGetRole_validUnexistingId($id)
    {
        $userModel = new PDOUserModel($this->connection);
        $this->assertEmpty($userModel->getRole($id));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider providerInvalidIds
     **/
    public function testGetMessageById_invalidId_InvalidArgumentException($id)
    {
        $userModel = new PDOUserModel($this->connection);
        $userModel->getMessageById($id);
    }

    public function testGetMessageById_validExistingId()
    {
        $userModel = new PDOUserModel($this->connection);
        $this->assertContains(['id'=>'3', 'content'=>'dit is testcontent 3', 'category'=>'tech', 'user_id'=>'1',
        'upvotes'=>'23', 'downvotes'=>'3'], $userModel->getMessageById('1'));

        $this->assertContains(['id'=>'2', 'content'=>'dit is testcontent 2', 'category'=>'games', 'user_id'=>'2',
        'upvotes'=>'15', 'downvotes'=>'5'], $userModel->getMessageById('2'));

        $this->assertContains(['id'=>'1', 'content'=>'dit is testcontent 1', 'category'=>'news', 'user_id'=>'3',
        'upvotes'=>'5', 'downvotes'=>'7'], $userModel->getMessageById('3'));
    }

    /**
    * @dataProvider providerValidUnexistingIds
    **/
    public function testGetMessageById_validUnexistingId($id)
    {
        $userModel = new PDOUserModel($this->connection);
        $this->assertEmpty($userModel->getMessageById($id));
    }

    /**
    * @expectedException \InvalidArgumentException
    * @dataProvider providerInvalidUsernames
    **/
    public function testGetUsername_invalidUsername_InvalidArgumentException($username)
    {
        $userModel = new PDOUserModel($this->connection);
        $userModel->getUsername($username);
    }

    public function testGetUsername_validExistingUsername()
    {
        $userModel = new PDOUserModel($this->connection);
        $this->assertEquals(true, $userModel->getUsername('user1'));
        $this->assertEquals(true, $userModel->getUsername('user2'));
        $this->assertEquals(true, $userModel->getUsername('user3'));
    }

    /**
    * @dataProvider providerValidUnexistingUsernames
    **/
    public function testGetUsername_validUnexistingUsername($username)
    {
        $userModel = new PDOUserModel($this->connection);
        $this->assertEquals(false, $userModel->getUsername($username));
    }

    /**
    * @expectedException \InvalidArgumentException
    * @dataProvider providerInvalidUsernames
    **/
    public function testGetPassword_invalidUsername_InvalidArgumentException($username)
    {
        $userModel = new PDOUserModel($this->connection);
        $userModel->getPassword($username);
    }

    public function testGetPassword_validExistingUsername()
    {
        $userModel = new PDOUserModel($this->connection);
        $this->assertContains(['password'=>'pass1'], $userModel->getPassword('user1'));
        $this->assertContains(['password'=>'pass2'], $userModel->getPassword('user2'));
        $this->assertContains(['password'=>'pass3'], $userModel->getPassword('user3'));
    }

    /**
    * @dataProvider providerValidUnexistingUsernames
    **/
    public function testGetPassword_validUnexistingUsername($username)
    {
        $userModel = new PDOUserModel($this->connection);
        $this->assertEmpty($userModel->getPassword($username));
    }
}
