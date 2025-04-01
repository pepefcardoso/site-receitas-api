<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], 400);
        }

        return parent::render($request, $exception);
    }
}
