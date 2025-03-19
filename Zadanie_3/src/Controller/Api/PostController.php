<?php

namespace App\Controller\Api;

use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/posts', name: 'posts')]
class PostController extends ApiController
{
    public function __construct(protected PostRepository $postRepository) {}

    #[Route('/{userId}')]
    public function search(int $userId): JsonResponse
    {
        $posts = $this->postRepository->getPostsForUser($userId);
        return $this->makeResponse($posts);
    }
}