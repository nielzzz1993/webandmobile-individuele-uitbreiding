<?php

namespace App\Tests\Util;

use App\Model\PDOMessageModel;
use App\Model\Connection;
use PHPUnit\Framework\TestCase;

class PDOMessageModelTest extends TestCase
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
            ['id'=>'1','content'=>'dit is testcontent 1',
            'category'=>'news','user_id'=>'3','upvotes'=>'5','downvotes'=>'7'],

            ['id'=>'2','content'=>'dit is testcontent 2',
            'category'=>'games','user_id'=>'2','upvotes'=>'15','downvotes'=>'5'],

            ['id'=>'3','content'=>'dit is testcontent 3',
            'category'=>'tech','user_id'=>'1','upvotes'=>'23','downvotes'=>'3']
        ];
    }

    public function providerUsers()
    {
        return [
            ['id'=>'1','role'=>'anon','username'=>'user1','password'=>'pass1'],
            ['id'=>'2','role'=>'anon','username'=>'user2','password'=>'pass2'],
            ['id'=>'3','role'=>'mod','username'=>'user3','password'=>'pass3']
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
        return [['1'], ['2'], ['3'],['100'],['1000']];
    }

    public function providerInvalidIds()
    {
        return [['0'], ['-1'], ['1.2'], ['aaa'], [12], [1.2]];
    }

    public function providerValidExistingKeywords()
    {
        return [['testerino,testcontent'], ['1'], ['2'], ['3']];
    }

    public function providerValidUnexistingKeywords()
    {
        return [['hallo'], ['testerino'], ['werkt'], ['dit?']];
    }

    public function providerInvalidKeywords()
    {
        return [[''], [1.2], [12]];
    }

    public function providerInvalidIdAddMessage()
    {
        return [
            ['', 'test', 'test'],
            [-1, 'test', 'test'],
            [1.2, 'test', 'test'],
            [' ', 'test', 'test']
        ];
    }

    public function providerInvalidContentAddMessage()
    {
        return [
            ['1', '', 'test'],
            ['4', ' ', 'test'],
            ['2', 1, 'test'],
            ['2', -1, 'test']
        ];
    }

    public function providerInvalidCategoryAddMessage()
    {
        return [
            ['1', 'test', 1.2],
            ['4', 'test', ''],
            ['2', 'test', 12],
            ['3', 'test', 'a']
        ];
    }

    public function providerValidAddMessage()
    {
        return [
            ['1', 'aa', 'aa'],
            ['2', 'Aa', 'Aa'],
            ['100', 'aa11', 'aa11'],
            ['1000', '11AA', '11AA']
        ];
    }

    public function providerInvalidCategoryAndKeywords()
    {
        return [
            ['t', ''],
            ['2', 12],
            [1, 123],
            [100, '']
        ];
    }

    public function providerValidExistingCategoryAndKeywords()
    {
        return [
            ['news', 'test'],
            ['games', '2'],
            ['tech', 'dit'],
            ['news', 'is']
        ];
    }

    public function providerValidUnexistingCategoryAndKeywords()
    {
        return [
            ['news', '2'],
            ['test', 'test'],
            ['tech', '2'],
            ['dit', 'dit']
        ];
    }

    public function testGetAllMessages_messagesInDatabase_ArrayMessages()
    {
        $messageModel = new PDOMessageModel($this->connection);
        $actualMessages = $messageModel->getAllMessages();
        $expectedMessages = $this->providerMessages();
        
        $this->assertEquals('array', gettype($actualMessages));
        $this->assertEquals(count($expectedMessages), count($actualMessages));

        //fwrite(STDERR, print_r($expectedMessages, TRUE));
        foreach ($actualMessages as $actualMessage) {
            //fwrite(STDERR, print_r($actualMessage, TRUE));
            $this->assertContains($actualMessage, $expectedMessages);
        }
    }

    public function testGetAllMessages_noMessagesInDatabase_EmptyArray()
    {
        $this->connection->getPDO()->exec('DROP TABLE message');

        $this->connection->getPDO()->exec('CREATE TABLE message (
            id INT,
            content VARCHAR(1000),
            category VARCHAR(100),
            user_id INT,
            upvotes INT,
            downvotes INT,
            PRIMARY KEY (id)
       )');

        $messageModel = new PDOMessageModel($this->connection);
        $actualMessages = $messageModel->getAllMessages();
        $this->assertEquals('array', gettype($actualMessages));
        $this->assertEquals(0, count($actualMessages));
    }

    /**
     * @expectedException \PDOException
     **/
    public function testGetAllMessages_noTableMessage_PDOException()
    {
         $this->connection->getPDO()->exec('DROP TABLE message');
         $messageModel = new PDOMessageModel($this->connection);
         $messageModel->getAllMessages();
    }

     /**
     * @dataProvider providerInvalidIdAddMessage
     **/
    public function testAddMessageByUserIdContentCategory_invalidId_validContent_validCategory_InvalidArgumentException(
        $userId,
        $content,
        $category
    ) {
        $messageModel = new PDOMessageModel($this->connection);
        $this->assertContains('id moet een int > 0 bevatten', $messageModel->addMessage($userId, $content, $category));
    }

    /**
     * @dataProvider providerInvalidContentAddMessage
     **/
    public function testAddMessageByUserIdContentCategory_validId_invalidContent_validCategory_InvalidArgumentException(
        $userId,
        $content,
        $category
    ) {
        $messageModel = new PDOMessageModel($this->connection);
        $this->assertContains(
            'moet minstens 2 karakters zijn',
            $messageModel->addMessage($userId, $content, $category)
        );
    }

    /**
     * @dataProvider providerInvalidCategoryAddMessage
     **/
    public function testAddMessageByUserIdContentCategory_validId_validContent_invalidCategory_InvalidArgumentException(
        $userId,
        $content,
        $category
    ) {
        $messageModel = new PDOMessageModel($this->connection);
        $this->assertContains(
            'moet minstens 2 karakters zijn',
            $messageModel->addMessage($userId, $content, $category)
        );
    }

    /**
     * @dataProvider providerValidAddMessage
     **/
    public function testAddMessageByUserIdContentCategory_validId_validContent_validCategory(
        $userId,
        $content,
        $category
    ) {
        $messageModel = new PDOMessageModel($this->connection);
        try {
            $messageModel->addMessage($userId, $content, $category);
        } catch (InvalidArgumentException $notExpected) {
             $this->fail();
        }
        $this->assertTrue(true);
    }

     /**
     * @expectedException \InvalidArgumentException
     * @dataProvider providerInvalidIds
     **/
    public function testIdExists_invalidId_InvalidArgumentException($id)
    {
        $messageModel = new PDOMessageModel($this->connection);
        $messageModel->idExists($id);
    }

    /**
     * @dataProvider providerValidExistingIds
     **/
    public function testIdExists_existingId_True($id)
    {
         $messageModel = new PDOMessageModel($this->connection);
         $this->assertTrue($messageModel->idExists($id));
    }

     /**
     * @dataProvider providerValidUnexistingIds
     **/
    public function testIdExists_unexistingId_False($id)
    {
         $messageModel = new PDOMessageModel($this->connection);
         $this->assertFalse($messageModel->idExists($id));
    }

     /**
     * @expectedException \InvalidArgumentException
     * @dataProvider providerInvalidKeywords
     **/
    public function testGetMessagesByKeywords_invalidKeywords_InvalidArgumentException($keywords)
    {
        $messageModel = new PDOMessageModel($this->connection);
        $messageModel->getMessagesByKeywords($keywords);
    }

     /**
     * @dataProvider providerValidExistingKeywords
     **/
    public function testGetMessagesByKeywords_existingKeywords($keywords)
    {
        $messageModel = new PDOMessageModel($this->connection);
        $this->assertNotEmpty($messageModel->getMessagesByKeywords($keywords));
    }

     /**
     * @dataProvider providerValidUnexistingKeywords
     **/
    public function testGetMessagesByKeywords_unexistingKeywords($keywords)
    {
        $messageModel = new PDOMessageModel($this->connection);
        $this->assertEmpty($messageModel->getMessagesByKeywords($keywords));
    }

     /**
     * @expectedException \InvalidArgumentException
     * @dataProvider providerInvalidCategoryAndKeywords
     **/
    public function testGetMessagesByCategoryAndKeywords_invalidCategory_invalidKeywords_invalidArgumentException(
        $category,
        $keywords
    ) {
        $messageModel = new PDOMessageModel($this->connection);
        $messageModel->getMessagesByCategoryAndKeywords($category, $keywords);
    }

     /**
     * @dataProvider providerValidExistingCategoryAndKeywords
     **/
    public function testGetMessagesByCategoryAndKeywords_validExistingCategory_validExistingKeywords(
        $category,
        $keywords
    ) {
        $messageModel = new PDOMessageModel($this->connection);
        $this->assertNotEmpty($messageModel->getMessagesByCategoryAndKeywords($category, $keywords));
    }

     /**
     * @dataProvider providerValidUnexistingCategoryAndKeywords
     **/
    public function testGetMessagesByCategoryAndKeywords_validUnexistingCategory_validUnexistingKeywords(
        $category,
        $keywords
    ) {
        $messageModel = new PDOMessageModel($this->connection);
        $this->assertEmpty($messageModel->getMessagesByCategoryAndKeywords($category, $keywords));
    }

     /**
     * @expectedException \InvalidArgumentException
     * @dataProvider providerInvalidIds
     **/
    public function testGetContent_invalidId_InvalidArgumentException($id)
    {
        $messageModel = new PDOMessageModel($this->connection);
        $messageModel->getContent($id);
    }

    public function testGetContent_validExistingId()
    {
        $messageModel = new PDOMessageModel($this->connection);

        $this->assertContains(['content'=>'dit is testcontent 1'], $messageModel->getContent('1'));
        $this->assertContains(['content'=>'dit is testcontent 2'], $messageModel->getContent('2'));
        $this->assertContains(['content'=>'dit is testcontent 3'], $messageModel->getContent('3'));
    }

     /**
     * @dataProvider providerValidUnexistingIds
     **/
    public function testGetContent_validUnexistingId($id)
    {
        $messageModel = new PDOMessageModel($this->connection);
        $this->assertEmpty($messageModel->getContent($id));
    }

     /**
     * @expectedException \InvalidArgumentException
     * @dataProvider providerInvalidIds
     **/
    public function testGetCategory_invalidId_InvalidArgumentException($id)
    {
        $messageModel = new PDOMessageModel($this->connection);
        $messageModel->getCategory($id);
    }

    public function testGetCategory_validExistingId()
    {
        $messageModel = new PDOMessageModel($this->connection);

        $this->assertContains(['category'=>'news'], $messageModel->getCategory('1'));
        $this->assertContains(['category'=>'games'], $messageModel->getCategory('2'));
        $this->assertContains(['category'=>'tech'], $messageModel->getCategory('3'));
    }

     /**
     * @dataProvider providerValidUnexistingIds
     **/
    public function testGetCategory_validUnexistingId($id)
    {
        $messageModel = new PDOMessageModel($this->connection);
        $this->assertEmpty($messageModel->getCategory($id));
    }

     /**
     * @expectedException \InvalidArgumentException
     * @dataProvider providerInvalidIds
     **/
    public function testGetUserById_invalidId_InvalidArgumentException($id)
    {
        $messageModel = new PDOMessageModel($this->connection);
        $messageModel->getUserById($id);
    }

    public function testGetUserById_validExistingId()
    {
        $messageModel = new PDOMessageModel($this->connection);
        $this->assertContains(['id'=>'3', 'role'=>'mod', 'username'=>'user3',
          'password'=>'pass3'], $messageModel->getUserById('1'));

        $this->assertContains(['id'=>'2', 'role'=>'anon', 'username'=>'user2',
          'password'=>'pass2'], $messageModel->getUserById('2'));

        $this->assertContains(['id'=>'1', 'role'=>'anon', 'username'=>'user1',
          'password'=>'pass1'], $messageModel->getUserById('3'));
    }

     /**
     * @dataProvider providerValidUnexistingIds
     **/
    public function testGetUserById_validUnexistingId($id)
    {
        $messageModel = new PDOMessageModel($this->connection);
        $this->assertEmpty($messageModel->getUserById($id));
    }

     /**
     * @expectedException \InvalidArgumentException
     * @dataProvider providerInvalidIds
     **/
    public function testGetUpvotes_invalidId_InvalidArgumentException($id)
    {
        $messageModel = new PDOMessageModel($this->connection);
        $messageModel->getUpvotes($id);
    }

    public function testGetUpvotes_validExistingId()
    {
        $messageModel = new PDOMessageModel($this->connection);
        $this->assertContains(['upvotes'=>'5'], $messageModel->getUpvotes('1'));
        $this->assertContains(['upvotes'=>'15'], $messageModel->getUpvotes('2'));
        $this->assertContains(['upvotes'=>'23'], $messageModel->getUpvotes('3'));
    }

     /**
     * @dataProvider providerValidUnexistingIds
     **/
    public function testGetUpvotes_validUnexistingId($id)
    {
        $messageModel = new PDOMessageModel($this->connection);
        $this->assertEmpty($messageModel->getUpvotes($id));
    }

     /**
     * @expectedException \InvalidArgumentException
     * @dataProvider providerInvalidIds
     **/
    public function testGetDownvotes_invalidId_InvalidArgumentException($id)
    {
        $messageModel = new PDOMessageModel($this->connection);
        $messageModel->getDownvotes($id);
    }

    public function testGetDownvotes_validExistingId()
    {
        $messageModel = new PDOMessageModel($this->connection);
        $this->assertContains(['downvotes'=>'7'], $messageModel->getDownvotes('1'));
        $this->assertContains(['downvotes'=>'5'], $messageModel->getDownvotes('2'));
        $this->assertContains(['downvotes'=>'3'], $messageModel->getDownvotes('3'));
    }

     /**
     * @dataProvider providerValidUnexistingIds
     **/
    public function testGetDownvotes_validUnexistingId($id)
    {
        $messageModel = new PDOMessageModel($this->connection);
        $this->assertEmpty($messageModel->getDownvotes($id));
    }

     /**
     * @expectedException \InvalidArgumentException
     * @dataProvider providerInvalidIds
     **/
    public function testIncreaseUpvotes_invalidId_InvalidArgumentException($id)
    {
        $messageModel = new PDOMessageModel($this->connection);
        $messageModel->increaseUpvotes($id);
    }

    public function testIncreaseUpvotes_validExistingId()
    {
        $messageModel = new PDOMessageModel($this->connection);
        $messageModel->increaseUpvotes('1');
        $this->assertContains(['upvotes'=>'6'], $messageModel->getUpvotes('1'));
        $messageModel->increaseUpvotes('2');
        $this->assertContains(['upvotes'=>'16'], $messageModel->getUpvotes('2'));
        $messageModel->increaseUpvotes('3');
        $this->assertContains(['upvotes'=>'24'], $messageModel->getUpvotes('3'));
        $messageModel->increaseUpvotes('1');
        $this->assertContains(['upvotes'=>'7'], $messageModel->getUpvotes('1'));
    }

     /**
     * @dataProvider providerValidUnexistingIds
     **/
    public function testIncreaseUpvotes_validUnexistingId($id)
    {
        $messageModel = new PDOMessageModel($this->connection);
        $this->assertEmpty($messageModel->getDownvotes($id));
    }

     /**
     * @expectedException \InvalidArgumentException
     * @dataProvider providerInvalidIds
     **/
    public function testIncreaseDownvotes_invalidId_InvalidArgumentException($id)
    {
        $messageModel = new PDOMessageModel($this->connection);
        $messageModel->increaseDownvotes($id);
    }

    public function testIncreaseDownvotes_validExistingId()
    {
        $messageModel = new PDOMessageModel($this->connection);
        $messageModel->increaseDownvotes('1');
        $this->assertContains(['downvotes'=>'8'], $messageModel->getDownvotes('1'));
        $messageModel->increaseDownvotes('2');
        $this->assertContains(['downvotes'=>'6'], $messageModel->getDownvotes('2'));
        $messageModel->increaseDownvotes('3');
        $this->assertContains(['downvotes'=>'4'], $messageModel->getDownvotes('3'));
        $messageModel->increaseDownvotes('1');
        $this->assertContains(['downvotes'=>'9'], $messageModel->getDownvotes('1'));
    }

    /**
    * @dataProvider providerValidUnexistingIds
    **/
    public function testIncreaseDownvotes_validUnexistingId($id)
    {
        $messageModel = new PDOMessageModel($this->connection);
        $this->assertEmpty($messageModel->getDownvotes($id));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider providerInvalidIds
     **/
    public function testGetMessageById_invalidId_InvalidArgumentException($id)
    {
        $messageModel = new PDOMessageModel($this->connection);
        $messageModel->getMessageById($id);
    }

    public function testGetMessagesById_validExistingId()
    {
        $messageModel = new PDOMessageModel($this->connection);
        $this->assertContains(['id'=>'1', 'content'=>'dit is testcontent 1', 'category'=>'news', 'user_id'=>'3',
        'upvotes'=>'5', 'downvotes'=>'7'], $messageModel->getMessageById('1'));
        $this->assertContains(['id'=>'2', 'content'=>'dit is testcontent 2', 'category'=>'games', 'user_id'=>'2',
        'upvotes'=>'15', 'downvotes'=>'5'], $messageModel->getMessageById('2'));
        $this->assertContains(['id'=>'3', 'content'=>'dit is testcontent 3', 'category'=>'tech', 'user_id'=>'1',
        'upvotes'=>'23', 'downvotes'=>'3'], $messageModel->getMessageById('3'));
    }

    /**
     * @dataProvider providerValidUnexistingIds
     **/
    public function testGetMessageById_validUnexistingId($id)
    {
        $messageModel = new PDOMessageModel($this->connection);
        $this->assertEmpty($messageModel->getMessageById($id));
    }
}
