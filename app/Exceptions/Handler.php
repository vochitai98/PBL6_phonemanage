<?php

namespace App\Exceptions;

use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\Access\AuthorizationException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (AuthorizationException $e,$request) {
            if($request->is('ipd/*')){
                return response()->json([
                    'message' => "Error login"
                ],404);
            }
        });
    }
}
