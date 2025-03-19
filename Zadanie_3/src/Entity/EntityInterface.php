<?php

namespace App\Entity;

interface EntityInterface
{
    public static function createFromArray(array $data): self;
}