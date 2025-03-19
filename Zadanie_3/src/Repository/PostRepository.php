<?php

namespace App\Repository;

use App\Entity\Post;
use App\Service\GoRestService;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class PostRepository
{
    protected GoRestService $goRestService;

    public function __construct(GoRestService $goRestService)
    {
        $this->goRestService = $goRestService;
    }

    /**
     * @param int $userId
     * @return Post[]
     */
    public function getPostsForUser(int $userId): array
    {
        $responseData = $this->goRestService->makePostsRequest(['user_id' => $userId]);
        if (!empty($responseData['content']) && $responseData['success']) {
            return array_map(function ($data) {
                return Post::createFromArray($data);
            }, $responseData['content']);
        } else {
            return [];
        }
    }
}