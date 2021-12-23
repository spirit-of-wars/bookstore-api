<?php

namespace App\Interfaces;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Interface ErrorResponseProviderInterface
 * @package App\Interfaces
 */
interface ErrorResponseProviderInterface
{
    /**
     * @return JsonResponse
     */
    public function getErrorResponse() : JsonResponse;
}
