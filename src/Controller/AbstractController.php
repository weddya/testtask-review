<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Raketa\BackendTestTask\Core\Http\JsonResponse;
use Raketa\BackendTestTask\Exception;

readonly class AbstractController
{
    protected function parseRequestBody(RequestInterface $request): array
    {
        $content = $request->getBody()->getContents();
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception\ValidationException('Invalid JSON format');
        }

        return $data ?? [];
    }

    protected function createSuccessResponse(array $data): JsonResponse
    {
        $response = new JsonResponse();
        $response->getBody()->write(
            json_encode([
                'status' => 'success',
                ...$data,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus(200);
    }

    protected function createErrorResponse(string $message, int $statusCode): JsonResponse
    {
        $response = new JsonResponse();
        $response->getBody()->write(
            json_encode([
                'status' => 'error',
                'message' => $message,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus($statusCode);
    }
}