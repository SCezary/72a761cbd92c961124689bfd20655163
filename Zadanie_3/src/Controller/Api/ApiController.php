<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ApiController extends AbstractController
{
    protected function makeResponse(mixed $data = [], int $status = 200, string $message = ''): JsonResponse
    {
        return $this->json([
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function makeEmptyResponse(int $status = 200): JsonResponse
    {
        return $this->makeResponse([], $status);
    }

    protected function makeErrorResponse($message, $errors = [], int $status = 422): JsonResponse
    {
        return $this->json([
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    protected function mapErrorsForResponse(ConstraintViolationListInterface $constraintViolationList, string $message = ''): JsonResponse
    {
        if (!count($constraintViolationList)) return $this->makeErrorResponse($message);

        $responseErrors = [];
        foreach ($constraintViolationList as $error) {
            $responseErrors[$error->getPropertyPath()] = $error->getMessage();
        }

        return $this->makeErrorResponse($message, $responseErrors);
    }
}
