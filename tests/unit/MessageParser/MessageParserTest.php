<?php

declare(strict_types=1);

namespace Kisoty\WebSocketChat\unit\MessageParser;

use Kisoty\WebSocketChat\Chat\Chat;
use Kisoty\WebSocketChat\Chat\MessageParser\MessageParser;
use Kisoty\WebSocketChat\Chat\Receivers\AllChatUsers;
use Kisoty\WebSocketChat\Chat\Receivers\ChatUserBatch;

class MessageParserTest extends \PHPUnit\Framework\TestCase
{
    public function testGetMessageMethod(): void
    {
        $parser = new MessageParser();
        $message = file_get_contents(__DIR__ . '/resources/messageForThreeReceivers.json');
        $parser->setMessage($message);

        $this->assertEquals('message', $parser->getMethod());
    }

    public function testGetChangeNameMethod(): void
    {
        $parser = new MessageParser();
        $message = file_get_contents(__DIR__ . '/resources/changeName.json');
        $parser->setMessage($message);

        $this->assertEquals('changeName', $parser->getMethod());
    }

    public function testGetBatchReceiversFromChat(): void
    {
        $parser = new MessageParser();
        $message = file_get_contents(__DIR__ . '/resources/messageForThreeReceivers.json');
        $chat = $this->createMock(Chat::class);

        $parser->setMessage($message);

        $this->assertInstanceOf(ChatUserBatch::class, $parser->getReceiversFromChat($chat));
    }

    public function testGetAllUsersReceiversFromChat(): void
    {
        $parser = new MessageParser();
        $message = file_get_contents(__DIR__ . '/resources/messageForAllChatUsers.json');
        $chat = $this->createMock(Chat::class);

        $parser->setMessage($message);

        $this->assertInstanceOf(AllChatUsers::class, $parser->getReceiversFromChat($chat));
    }

    public function testGetMessageDataWithMessage(): void
    {
        $parser = new MessageParser();
        $message = file_get_contents(__DIR__ . '/resources/messageForAllChatUsers.json');

        $parser->setMessage($message);

        $this->assertEquals(['message' => 'Some test message'], $parser->getMessageData());
    }

    public function testGetMessageDataWithNewName(): void
    {
        $parser = new MessageParser();
        $message = file_get_contents(__DIR__ . '/resources/changeName.json');

        $parser->setMessage($message);
        var_dump($parser->getMessageData());

        $this->assertEquals(['newName' => 'AwesomeName'], $parser->getMessageData());
    }
}
