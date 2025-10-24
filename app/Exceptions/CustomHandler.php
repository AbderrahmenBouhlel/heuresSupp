<?php

namespace App\Exceptions;


use Throwable;
use App\Exceptions\ResponseError;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CustomHandler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        $responseError = new ResponseError();
        error_log($exception);

        switch (true) {
            case $exception instanceof \Illuminate\Validation\ValidationException:
                $responseError->setStatusCode(422);
                $responseError->setPayload([
                    'status' => 'error',
                    'message' => 'Validation Error',
                    'errors' => $exception->errors(),
                ]);
                break;

            case $exception instanceof \Illuminate\Auth\AuthenticationException:
                $responseError->setStatusCode(401);
                $responseError->setPayload([
                    'status' => 'error',
                    'message' => 'Unauthenticated',
                    'errors' => [],
                ]);
                break;

            case $exception instanceof \Illuminate\Auth\Access\AuthorizationException:
                $responseError->setStatusCode(403);
                $responseError->setPayload([
                    'status' => 'error',
                    'message' => $exception->getMessage() ?: 'Forbidden',
                    'errors' => [],
                ]);
                break;

            case $exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException:
                $responseError->setStatusCode(404);
                $responseError->setPayload([
                    'status' => 'error',
                    'message' => 'Resource not found',
                    'errors' => [],
                ]);
                break;

            case $exception instanceof HttpException:
                $responseError->setStatusCode($exception->getStatusCode());
                $responseError->setPayload([
                    'status' => 'error',
                    'message' => $exception->getMessage() ?: 'Http Error',
                    'errors' => [],
                ]);
                break;

            default:
                $responseError->setStatusCode(500);
                $responseError->setPayload([
                    'status' => 'error',
                    'message' => 'Server Error',
                    'errors' => [],
                ]);
                break;
        }

        return response()->json($responseError->getPayload(), $responseError->getStatusCode());
    }
}
