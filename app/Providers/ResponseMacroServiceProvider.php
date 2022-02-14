<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseMacroServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $responseHeaders = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'deny',
            'Content-Security-Policy' => 'default-src \'none\'',
        ];

        Response::macro(
            'success',
            function (
                $data = [],
                int $status = 200,
                array $headers = [],
                int $options = 0
            ) use ($responseHeaders): JsonResponse {
                $responseHeaders = array_merge($responseHeaders, $headers);
                return new JsonResponse($data, $status, $responseHeaders, $options);
            }
        );

        $errorResponse = function (
            $data = [],
            int $status = 400,
            array $headers = [],
            int $options = 0
        ) use ($responseHeaders): JsonResponse {
            if (!is_array($data)) {
                $data = ['data' => $data];
            }
            $responseHeaders = array_merge($responseHeaders, $headers);

            return new JsonResponse($data, $status, $responseHeaders, $options);
        };

        Response::macro('error', $errorResponse);

        Response::macro(
            'errorWithLog',
            function (
                $data = '',
                int $status = 400,
                array $context = [],
                array $headers = [],
                int $options = 0
            ) use (
                $responseHeaders,
                $errorResponse
            ): JsonResponse {
                Log::error($data, $context);
                return $errorResponse($data, $status, $headers, $options);
            }
        );
    }
}
