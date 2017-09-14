<?php

namespace App\Exceptions;

use App\Generic;
use App\Resources\Generic as GenericResource;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        switch (true) {
            case $e instanceof ValidationException:
                return new GenericResource(new Generic([
                    'http' => Response::HTTP_BAD_REQUEST,
                    'message' => 'Invalid input received. Please look at the errors object for more information',
                    'errors' => $e->errors(),
                    'code' => 5
                ]));
                break;
            case $e instanceof ModelNotFoundException:
                return new GenericResource(new Generic([
                    'http' => Response::HTTP_NOT_FOUND,
                    'message' => 'Requested resource not found',
                    'code' => 404
                ]));
                break;
            default:
                if (env('APP_DEBUG') || config('app.debug')) {
                    return parent::render($request, $e);
                } else {
                    return new GenericResource(new Generic([
                        'http' => Response::HTTP_SERVICE_UNAVAILABLE,
                        'code' => 999
                    ]));
                }

                break;
        }
    }
}
