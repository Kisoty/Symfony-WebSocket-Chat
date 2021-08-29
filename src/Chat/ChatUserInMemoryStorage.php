<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat;

class ChatUserInMemoryStorage
{
    /**
     * [ connection_id => ChatUser ]
     * @var array<int, ChatUser>
     */
    private array $users = [];

    public function add(int $connectionId, ChatUser $user): void
    {
        $this->users[$connectionId] = $user;
    }

    public function remove(int $connectionId): void
    {
        unset($this->users[$connectionId]);
    }

    public function getByConnectionId(int $connectionId): ?ChatUser
    {
        return $this->users[$connectionId] ?? null;
    }
}
