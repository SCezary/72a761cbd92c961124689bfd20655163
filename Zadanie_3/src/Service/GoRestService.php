<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class GoRestService
{
    private string $cachePrefix = 'go_rest-';
    public int $cacheExpire = 30; // seconds

    public bool $cached = true;

    public function __construct(
        private readonly HttpClientInterface $client,
        private CacheInterface $cache,
        private LoggerInterface $logger,
        private string $authToken,
        private string $apiUrl
    ) {}

    public function makeUsersRequest(array $query, string $method = 'GET', array $body = [], string $path = ''): array
    {
        return $this->makeRequest($method, "/users{$path}" . (!empty($query) ? '?' . http_build_query($query) : ''), [
            'json' => $body,
        ]);
    }

    public function makePostsRequest(array $query, string $method = 'GET', array $body = [], string $path = ''): array
    {
        return $this->makeRequest($method, "/posts{$path}" . (!empty($query) ? '?' . http_build_query($query) : ''), [
            'json' => $body,
        ]);
    }

    public function makeRequest($method, $path, $options): array
    {
        if (strtoupper($method) === 'GET' && $this->cached) {
            $cacheKey = $this->cachePrefix . md5($path);
            return $this->cache->get($cacheKey, function (ItemInterface $item) use ($method, $path, $options) {
                $item->expiresAfter($this->cacheExpire);

                try {
                    $response = $this->apiRequest($method, $this->apiUrl . $path, $options);
                    $content = $response->toArray(false);
                    return $this->makeResponse($response->getStatusCode(), $content);
                } catch (ExceptionInterface $e) {
                    $this->logger->error('GoRest Error: ' . $e->getMessage());
                    $item->expiresAfter(5);

                    return $this->makeResponse($e->getCode(), [], $e->getMessage());
                }
            });
        }

        try {
            $response = $this->apiRequest($method, $this->apiUrl . $path, $options);
            $content = $response->toArray(false);
            return $this->makeResponse($response->getStatusCode(), $content);
        } catch (ExceptionInterface $e) {
            $this->logger->error('GoRest Error: ' . $e->getMessage());
            return $this->makeResponse($e->getCode(), [], $e->getMessage());
        }
    }

    protected function makeResponse(int $code, array $content = [], string $message = ''): array
    {
        return [
            'code' => $code,
            'success' => $code >= 200 && $code < 300,
            'content' => $content,
            'message' => $message
        ];
    }

    /**
     * @throws TransportExceptionInterface
     */
    protected function apiRequest(string $method, string $url, array $options = []): ResponseInterface
    {
        return $this->client->request($method, $url , [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->authToken
            ],
            ...$options
        ]);
    }
}