<?php

declare(strict_types=1);

namespace Kisoty\WebSocketChat\unit\MessageParser;

use Kisoty\WebSocketChat\Chat\ChatUser;
use Kisoty\WebSocketChat\Chat\ChatUserInMemoryStorage;
use Kisoty\WebSocketChat\Chat\MessageDispatcher;
use Kisoty\WebSocketChat\Chat\RequestFoundation\MessageParser\MessageParser;
use Kisoty\WebSocketChat\Chat\Receivers\AllChatUsers;
use Kisoty\WebSocketChat\Chat\Receivers\ChatUserBatch;

class MessageParserTest extends \PHPUnit\Framework\TestCase
{
    public function testGetMessageMethod(): void
    {
        $userStorage = $this->createStub(ChatUserInMemoryStorage::class);

        $parser = new MessageParser($userStorage);
        $message = file_get_contents(__DIR__ . '/resources/messageForThreeReceivers.json');
        $parser->setMessage($message);

        $this->assertEquals('message', $parser->getMethod());
    }

    public function testGetChangeNameMethod(): void
    {
        $userStorage = $this->createStub(ChatUserInMemoryStorage::class);

        $parser = new MessageParser($userStorage);
        $message = file_get_contents(__DIR__ . '/resources/changeName.json');
        $parser->setMessage($message);

        $this->assertEquals('changeName', $parser->getMethod());
    }

    public function testGetBatchReceiversFromChat(): void
    {
        $user = $this->createStub(ChatUser::class);
        $userStorage = $this->createStub(ChatUserInMemoryStorage::class);
        $userStorage->method('getByConnectionId')->willReturn($user);

        $parser = new MessageParser($userStorage);
        $message = file_get_contents(__DIR__ . '/resources/messageForThreeReceivers.json');
        $chat = $this->createMock(MessageDispatcher::class);

        $parser->setMessage($message);

        $this->assertInstanceOf(ChatUserBatch::class, $parser->getReceiversFromChat($chat));
    }

    public function testGetAllUsersReceiversFromChat(): void
    {
        $userStorage = $this->createStub(ChatUserInMemoryStorage::class);

        $parser = new MessageParser($userStorage);
        $message = file_get_contents(__DIR__ . '/resources/messageForAllChatUsers.json');
        $chat = $this->createMock(MessageDispatcher::class);

        $parser->setMessage($message);

        $this->assertInstanceOf(AllChatUsers::class, $parser->getReceiversFromChat($chat));
    }

    public function testGetMessageDataWithMessage(): void
    {
        $userStorage = $this->createStub(ChatUserInMemoryStorage::class);

        $parser = new MessageParser($userStorage);
        $message = file_get_contents(__DIR__ . '/resources/messageForAllChatUsers.json');

        $parser->setMessage($message);

        $this->assertEquals(['message' => 'Some test message'], $parser->getMessageData());
    }

    public function testGetMessageDataWithNewName(): void
    {
        $userStorage = $this->createStub(ChatUserInMemoryStorage::class);

        $parser = new MessageParser($userStorage);
        $message = file_get_contents(__DIR__ . '/resources/changeName.json');

        $parser->setMessage($message);

        $this->assertEquals(['newName' => 'AwesomeName'], $parser->getMessageData());
    }
}
