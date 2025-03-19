<?php

namespace App\Controller\Api;

use App\Repository\UserRepository;
use App\Request\CreateUserRequest;
use App\Request\UpdateUserRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/users', name: 'user')]
class UserController extends ApiController
{
    public function __construct(
        protected UserRepository $userRepository,
        protected ValidatorInterface $validator,
        protected SerializerInterface $serializer
    ) {}

    #[Route('/', name: 'search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $filters = $request->query->all();
        $users = $this->userRepository->getUsers($filters);
        return $this->makeResponse($users);
    }

    #[Route('/create',  name: 'create', methods: ['POST'])]
    public function create(Request$request): JsonResponse
    {
        $userRequest = $this->serializer->deserialize($request->getContent(), CreateUserRequest::class, 'json');
        $errors = $this->validator->validate($userRequest);

        if (count($errors) > 0) {
            return $this->mapErrorsForResponse($errors, 'Incorrect Form Data');
        }

        if (!$this->userRepository->create($userRequest)) {
            return $this->makeErrorResponse('Something went wrong');
        };

        return $this->makeEmptyResponse(201);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $userRequest = $this->serializer->deserialize($request->getContent(), UpdateUserRequest::class, 'json');
        $errors = $this->validator->validate($userRequest);

        if (count($errors) > 0) {
            return $this->mapErrorsForResponse($errors, 'Incorrect Form Data');
        }

        if (!$this->userRepository->update($userRequest, $id)) {
            return $this->makeErrorResponse('Something went wrong');
        };

        return $this->makeEmptyResponse(201);
    }
}