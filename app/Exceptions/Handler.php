<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {  
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if($request->is('api/*')){
                return response()->json(['message' => 'Data not found'], 404,);
            }
        });

        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            if($request->is('api/*')){
                return response()->json( ['message' => 'Method is not allowed for the requested route',], 405 );
            }            
        });
    }
}
