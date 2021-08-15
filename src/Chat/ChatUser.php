<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat;


class ChatUser
{
    /**
     * @param string $name
     * @param int $id
     */
    public function __construct(private int $id, private string $name) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function changeName(string $newName): void
    {
        $this->name = $newName;
    }
}
