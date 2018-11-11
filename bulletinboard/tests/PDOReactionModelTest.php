<?php

namespace App\Tests\Util;

use App\Model\PDOReactionModel;
use App\Model\Connection;
use PHPUnit\Framework\TestCase;

class PDOReactionModelTest extends TestCase
{
    public function setUp()
    {
        $this->connection = new Connection('sqlite::memory:');
        //var_dump( $this->connection->getPDO()); die();
        $this->connection->getPDO()->exec('CREATE TABLE reaction (
                        id INT,
                        content VARCHAR(1000),
                        token VARCHAR(255),
                        message_id INT,
                        PRIMARY KEY (id)
                   )');

        $this->connection->getPDO()->exec('CREATE TABLE message (
                        id INT,
                        content VARCHAR(1000),
                        category VARCHAR(100),
                        user_id INT,
                        upvotes INT,
                        downvotes INT,
                        PRIMARY KEY (id)
                   )');

        $reactions = $this->providerReactions();
        $messages  = $this->providerMessages();

        foreach ($messages as $message) {
            $this->connection->getPDO()->exec(
                "INSERT INTO message (id, content, category, user_id, upvotes, downvotes)
                VALUES (".$message['id'].", '".$message['content']."', '".$message['category']."',
                '".$message['user_id']."', '".$message['upvotes']."', '".$message['downvotes']."');"
            );
        }

        foreach ($reactions as $reaction) {
            $this->connection->getPDO()->exec(
                "INSERT INTO reaction (id, content, token, message_id)
                VALUES (".$reaction['id'].", '".$reaction['content']."', '".$reaction['token']."',
                '".$reaction['message_id']."');"
            );
        }
    }
    public function providerValidUnexistingIds()
    {
        return [['4'], ['100'], ['1000']];
    }

    public function providerInvalidIds()
    {
        return [['0'], ['-1'], ['1.2'], ['aaa'], [12], [1.2]];
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

    public function providerReactions()
    {
        return [
          ['id'=>'1', 'content'=>'Cool message!', 'token'=>'5bb224d890554', 'message_id'=>'1'],
          ['id'=>'2', 'content'=>'Meh... OK message.', 'token'=>'5bb224fbc024a', 'message_id'=>'2'],
          ['id'=>'3', 'content'=>'I do not support this message.', 'token'=>'5bb22518154ff', 'message_id'=>'3']
        ];
    }

    public function providerValidAddReaction()
    {
        return [
            ['1', 'Nice post!'],
            ['2', 'Nice copypasta...'],
            ['3', 'What a dumb post.']
        ];
    }

    public function providerInvalidMessageIdAddReaction()
    {
        return [
            [0, 'test'],
            [-1, 'test'],
            [1.2, 'test'],
            ['aaa', 'test']
        ];
    }

    public function providerInvalidContentAddReaction()
    {
        return [
            [2, ''],
            [4, 0],
            [2, 'a'],
            [1, -1]
        ];
    }

    /**
    * @dataProvider providerInvalidMessageIdAddReaction
    **/
    public function testAddReactionByMessageIdContent_invalidMessageId_validContent_InvalidArgumentException(
        $messageId,
        $content
    ) {
        $reactionModel = new PDOReactionModel($this->connection);

        $this->assertContains('id moet een int > 0 bevatten', $reactionModel->addReaction($messageId, $content));
    }

    /**
    * @dataProvider providerInvalidContentAddReaction
    **/
    public function testAddReactionByMessageIdContent_validMessageId_invalidContent_InvalidArgumentException(
        $messageId,
        $content
    ) {
        $reactionModel = new PDOReactionModel($this->connection);

        $this->assertContains('moet minstens 2 karakters zijn', $reactionModel->addReaction($messageId, $content));
    }

     /**
      * @dataProvider providerValidAddReaction
      **/
    public function testAddMessageByUserIdContentCategory_validId_validContent_validCategory(
        $messageId,
        $content
    ) {
        $reactionModel = new PDOReactionModel($this->connection);
        try {
            $reactionModel->addReaction($messageId, $content);
        } catch (InvalidArgumentException $notExpected) {
            $this->fail();
        }
        $this->assertTrue(true);
    }

    /**
    * @expectedException \InvalidArgumentException
    * @dataProvider providerInvalidIds
    **/
    public function testGetContent_invalidId_InvalidArgumentException($id)
    {
        $reactionModel = new PDOReactionModel($this->connection);
        $reactionModel->getContent($id);
    }

    public function testGetContent_validExistingId()
    {
        $reactionModel = new PDOReactionModel($this->connection);

        $this->assertContains(['content'=>'Cool message!'], $reactionModel->getContent('1'));
        $this->assertContains(['content'=>'Meh... OK message.'], $reactionModel->getContent('2'));
        $this->assertContains(['content'=>'I do not support this message.'], $reactionModel->getContent('3'));
    }

    /**
    * @dataProvider providerValidUnexistingIds
    **/
    public function testGetContent_validUnexistingId($id)
    {
        $reactionModel = new PDOReactionModel($this->connection);
        $this->assertEmpty($reactionModel->getContent($id));
    }

    /**
    * @expectedException \InvalidArgumentException
    * @dataProvider providerInvalidIds
    **/
    public function testGetToken_invalidId_InvalidArgumentException($id)
    {
        $reactionModel = new PDOReactionModel($this->connection);
        $reactionModel->getContent($id);
    }

    public function testGetToken_validExistingId()
    {
        $reactionModel = new PDOReactionModel($this->connection);
        $this->assertContains(['token'=>'5bb224d890554'], $reactionModel->getToken('1'));
        $this->assertContains(['token'=>'5bb224fbc024a'], $reactionModel->getToken('2'));
        $this->assertContains(['token'=>'5bb22518154ff'], $reactionModel->getToken('3'));
    }

    /**
    * @dataProvider providerValidUnexistingIds
    **/
    public function testGetToken_validUnexistingId($id)
    {
        $reactionModel = new PDOReactionModel($this->connection);
        $this->assertEmpty($reactionModel->getContent($id));
    }

    /**
    * @expectedException \InvalidArgumentException
    * @dataProvider providerInvalidIds
    **/
    public function testGetMessageById_invalidId_InvalidArgumentException($id)
    {
        $reactionModel = new PDOReactionModel($this->connection);
        $reactionModel->getContent($id);
    }

    public function testGetMessageById_validExistingId()
    {
        $reactionModel = new PDOReactionModel($this->connection);

        $this->assertContains(['id'=>'1', 'content'=>'dit is testcontent 1',
        'category'=>'news', 'user_id'=>'3', 'upvotes'=>'5', 'downvotes'=>'7'], $reactionModel->getMessageById('1'));

        $this->assertContains(['id'=>'2','content'=>'dit is testcontent 2',
        'category'=>'games', 'user_id'=>'2', 'upvotes'=>'15', 'downvotes'=>'5'], $reactionModel->getMessageById('2'));

        $this->assertContains(['id'=>'3', 'content'=>'dit is testcontent 3',
        'category'=>'tech', 'user_id'=>'1', 'upvotes'=>'23', 'downvotes'=>'3'], $reactionModel->getMessageById('3'));
    }

    /**
    * @dataProvider providerValidUnexistingIds
    **/
    public function testMessageById_validUnexistingId($id)
    {
        $reactionModel = new PDOReactionModel($this->connection);
        $this->assertEmpty($reactionModel->getContent($id));
    }
}
