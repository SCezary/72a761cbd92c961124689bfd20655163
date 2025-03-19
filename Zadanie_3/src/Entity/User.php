<?php

namespace App\Entity;

use JsonSerializable;

class User implements EntityInterface, JsonSerializable
{
    private string $id;
    private string $name;
    private string $email;
    private string $gender;
    private string $status;
    private array $posts;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getGender(): string
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender(string $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return array
     */
    public function getPosts(): array
    {
        return $this->posts;
    }

    /**
     * @param array $posts
     */
    public function setPosts(array $posts): void
    {
        $this->posts = $posts;
    }

    public static function createFromArray(array $data): User
    {
        $user = new User();
        $user->id = $data['id'] ?? '';
        $user->name = $data['name'] ?? '';
        $user->email = $data['email'] ?? '';
        $user->gender = $data['gender'] ?? '';
        $user->status = $data['status'] ?? '';
        $user->posts = $data['posts'] ?? [];

        return $user;
    }

    public function jsonSerialize(): array
    {
        return [
            'id'      => $this->getId(),
            'name'    => $this->getName(),
            'email'   => $this->getEmail(),
            'gender'  => $this->getGender(),
            'status'  => $this->getStatus(),
            'posts'   => $this->getPosts(),
        ];
    }
}