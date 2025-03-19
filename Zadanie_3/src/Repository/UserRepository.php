<?php

namespace App\Repository;

use App\Entity\User;
use App\Request\CreateUserRequest;
use App\Request\UpdateUserRequest;
use App\Service\GoRestService;

class UserRepository
{
    public function __construct(
        protected GoRestService $goRestService,
    ) {}

    /**
     * @param array $queryFilters
     * @return User[]
     */
    public function getUsers(array $queryFilters = []): array
    {
        $filters = [];
        if (!empty($queryFilters['search'])) {
            $filters['name'] = $queryFilters['search'];
        }

        if (!empty($queryFilters['page'])) {
            $filters['page'] = $queryFilters['page'];
        } else {
            $filters['page'] = 1;
        }

        if (!empty($queryFilters['per-page'])) {
            $filters['per_page'] = $queryFilters['per-page'];
        } else {
            $filters['per_page'] = 10;
        }

        $responseData = $this->goRestService->makeUsersRequest($filters);
        if ($responseData['success'] && !empty($responseData['content'])) {
            return array_map(function ($data) {
                return User::createFromArray($data);
            }, $responseData['content']);
        }

        return [];
    }

    public function getUserById(int $id): ?User
    {
        $this->goRestService->cached = false;
        $responseData = $this->goRestService->makeUsersRequest(['id' => $id]);
        if ($responseData['success'] && !empty($responseData['content'])) {
            return User::createFromArray($responseData['content'][0]);
        }

        return null;
    }

    public function create(CreateUserRequest $request): bool
    {
        $responseData = $this->goRestService->makeUsersRequest([], 'POST', [
            'name' => $request->name,
            'email' => $request->email,
            'status' => $request->status,
            'gender' => $request->gender
        ]);

        return $responseData['success'];
    }

    public function update(UpdateUserRequest $request, int $id): bool
    {
        $responseData = $this->goRestService->makeUsersRequest([], 'PUT', [
            'name' => $request->name,
            'email' => $request->email,
            'status' => $request->status,
            'gender' => $request->gender
        ], "/{$id}");

        return $responseData['success'];
    }
}