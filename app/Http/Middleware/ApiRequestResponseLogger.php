<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ApiRequestResponseLogger
{
    public function handle(Request $request, Closure $next)
    {
        $requestId = (string) Str::uuid();
        $startedAt = microtime(true);
        $channels = $this->resolveChannels($request);
        $requestContext = $this->buildRequestContext($request, $requestId);

        $this->logToChannels($channels, 'info', 'API request received', $requestContext);

        try {
            $response = $next($request);
        } catch (Throwable $exception) {
            $durationMs = (int) ((microtime(true) - $startedAt) * 1000);

            $this->logToChannels($channels, 'error', 'API request failed', array_merge(
                $requestContext,
                $this->buildExceptionContext($exception, $durationMs)
            ));

            throw $exception;
        }

        $durationMs = (int) ((microtime(true) - $startedAt) * 1000);

        $this->logToChannels($channels, 'info', 'API response sent', array_merge(
            $requestContext,
            $this->buildResponseContext($response, $durationMs)
        ));

        return $response;
    }

    private function resolveChannels(Request $request): array
    {
        $channels = ['api_requests'];
        $actionName = (string) optional($request->route())->getActionName();

        if (str_contains($actionName, '\\Api\\Sales\\') || $request->is('api/sales/*')) {
            $channels[] = 'api_sales';
        }

        if (str_contains($actionName, '\\Api\\Distributor\\') || $request->is('api/distributor/*')) {
            $channels[] = 'api_distributor';
        }

        return $channels;
    }

    private function buildRequestContext(Request $request, string $requestId): array
    {
        return [
            'request_id' => $requestId,
            'method' => $request->method(),
            'path' => $request->path(),
            'full_url' => $request->fullUrl(),
            'route_name' => optional($request->route())->getName(),
            'action' => optional($request->route())->getActionName(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'guard' => $this->resolveGuard($request),
            'actor_id' => $this->resolveActorId($request),
            'headers' => $this->sanitizeHeaders($request->headers->all()),
            'query' => $this->sanitizeArray($request->query()),
            'body' => $this->sanitizeArray($request->except([])),
            'files' => array_keys($request->allFiles()),
        ];
    }

    private function buildResponseContext($response, int $durationMs): array
    {
        return [
            'duration_ms' => $durationMs,
            'status' => $response->getStatusCode(),
            'response' => $this->extractResponseBody($response),
        ];
    }

    private function buildExceptionContext(Throwable $exception, int $durationMs): array
    {
        $status = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : ($exception instanceof ValidationException ? 422 : 500);

        $context = [
            'duration_ms' => $durationMs,
            'status' => $status,
            'exception' => get_class($exception),
            'error' => $exception->getMessage(),
        ];

        if ($exception instanceof ValidationException) {
            $context['errors'] = $exception->errors();
        }

        return $context;
    }

    private function extractResponseBody($response): mixed
    {
        if (! method_exists($response, 'getContent')) {
            return '[non-standard response]';
        }

        $content = $response->getContent();

        if ($content === false || $content === null || $content === '') {
            return null;
        }

        $decoded = json_decode($content, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return Str::limit($content, 4000);
    }

    private function sanitizeHeaders(array $headers): array
    {
        $sanitized = [];

        foreach ($headers as $key => $values) {
            if (in_array(strtolower($key), ['authorization', 'cookie', 'x-csrf-token', 'x-xsrf-token'], true)) {
                $sanitized[$key] = ['[redacted]'];
                continue;
            }

            $sanitized[$key] = $values;
        }

        return $sanitized;
    }

    private function sanitizeArray(array $data): array
    {
        $sensitiveKeys = [
            'password',
            'password_confirmation',
            'current_password',
            'token',
            'access_token',
            'refresh_token',
        ];

        foreach ($data as $key => $value) {
            if (in_array(strtolower((string) $key), $sensitiveKeys, true)) {
                $data[$key] = '[redacted]';
                continue;
            }

            if (is_array($value)) {
                $data[$key] = $this->sanitizeArray($value);
            }
        }

        return $data;
    }

    private function resolveGuard(Request $request): ?string
    {
        $actionName = (string) optional($request->route())->getActionName();

        if (str_contains($actionName, '\\Api\\Sales\\') || $request->is('api/login/sales') || $request->is('api/logout/sales')) {
            return 'sales_api';
        }

        if (str_contains($actionName, '\\Api\\Distributor\\') || $request->is('api/login/distributor') || $request->is('api/logout/distributor')) {
            return 'distributor_api';
        }

        return null;
    }

    private function resolveActorId(Request $request): mixed
    {
        $guard = $this->resolveGuard($request);

        return $guard ? optional(auth($guard)->user())->getAuthIdentifier() : null;
    }

    private function logToChannels(array $channels, string $level, string $message, array $context): void
    {
        foreach (array_unique($channels) as $channel) {
            Log::channel($channel)->{$level}($message, $context);
        }
    }
}
