<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Auth;
use Illuminate\Session\TokenMismatchException;
use Intervention\Image\Exception\NotReadableException;
use League\Flysystem\FileNotFoundException;
use Session;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
        if ($exception instanceof NotFoundHttpException) {
            return response()->view('errors.404', [], 500);
        }
        if( $exception instanceof MethodNotAllowedHttpException){
            return response()->view('errors.404', [], 500);
        }
       if($exception instanceof  TokenMismatchException ||$exception instanceof  FileNotFoundException || $exception instanceof AuthenticationException || $exception instanceof AuthorizationException || $exception instanceof FatalThrowableError) {
        }else{
           $supported_image = array( 'gif','jpg','jpeg','png','map','svg');
           $src_file_name = $request->getRequestUri();
           $ext = strtolower(pathinfo($src_file_name, PATHINFO_EXTENSION));
           if (in_array($ext, $supported_image)) {

           } else {
//               Mail::send('emails.exception', ['error' => parent::render($request, $exception)->getContent(), 'request' => $request], function ($m) {
//                   $m->to('mail@dileep.com.np', 'Dileep')->subject('Error');
//                   $m->cc('ashikchalise875@gmail.com');
//               });
           }
        }

        if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
            return redirect()
                ->guest(route('login'))
                ->withInput($request->except(['password', 'password_confirmation']))
                ->with('error', 'The form has expired due to inactivity. Please try again');
        }

        return parent::render($request, $exception);

        if(Session::get('company_id')==1){
            return parent::render($request, $exception);
        }else{
            // 404 page when a model is not found
            if ($exception instanceof ModelNotFoundException) {
                return response()->view('errors.404', [], 404);
            }

            // custom error message
            if ($exception instanceof \ErrorException) {
                return response()->view('errors.404', [], 500);
            }

            if ($request->expectsJson()) {
                return view('dashboard/errorpage');
            }else{
                return redirect()->guest(route('login'));
            }
        }
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }
}