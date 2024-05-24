<?php

namespace App\Dto;

use App\Entity\Chat as EntityChat;

class Message
{
    private string $content;
    private EntityChat $chat;

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getChat(): EntityChat
    {
        return $this->chat;
    }

    public function setChat(EntityChat $chat): self
    {
        $this->chat = $chat;

        return $this;
    }
}
