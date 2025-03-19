<?php

namespace App\Entity;

use JsonSerializable;

class Post implements EntityInterface, JsonSerializable
{
    private string $id;
    private string $userId;
    private string $title;

    private string $body;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userID): void
    {
        $this->userId = $userID;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public static function createFromArray(array $data): Post
    {
        $post = new Post();
        $post->body = $data['body'] ?? '';
        $post->title = $data['title'] ?? '';
        $post->id = $data['id'] ?? '';
        $post->userId = $data['user_id'] ?? '';
        return $post;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'user_id' => $this->userId
        ];
    }
}