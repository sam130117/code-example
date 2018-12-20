<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($request->wantsJson() || $request->expectsJson()) {
            switch (get_class($exception)) {
                case AuthenticationException::class :
                    return response()->json([
                        'code'    => Response::HTTP_UNAUTHORIZED,
                        'message' => 'Unauthorized.',
                    ], Response::HTTP_UNAUTHORIZED);

                case ModelNotFoundException::class :
                    return response()->json([
                        'code'    => Response::HTTP_NOT_FOUND,
                        'message' => 'Resource not found.',
                    ], Response::HTTP_NOT_FOUND);

                case ValidationException::class :
                    return response()->json([
                        'code'    => Response::HTTP_UNPROCESSABLE_ENTITY,
                        'message' => 'The given data was invalid.',
                        'errors'  => $exception->errors(),
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);

                default:
                    return response()->json([
                        'code'    => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => $exception->getMessage(),
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

        }
        return parent::render($request, $exception);
    }
}
