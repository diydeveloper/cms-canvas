<?php 

namespace CmsCanvas\Exceptions;

use Exception, App, Response;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Handler extends ExceptionHandler {

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        'Symfony\Component\HttpKernel\Exception\HttpException'
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof \CmsCanvas\Exceptions\ExceptionDisplayInterface) {
            $view = $e->getView();

            if ($e instanceof HttpExceptionInterface) {
                return Response::make($view, $e->getStatusCode());
            } else {
                return $view;
            }
        }

        if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return Response::make(
                App::make('\CmsCanvas\Http\Controllers\PageController')->callAction('showPage', [$e]),
                $e->getStatusCode()
            );
        }

        return parent::render($request, $e);
    }

}
