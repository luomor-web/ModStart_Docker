<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Exception\ExceptionReportHandleTrait;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    use ExceptionReportHandleTrait;

    
    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
        BizException::class,
    ];

    
    public function report(Exception $exception)
    {
        $this->errorReportCheck($exception);
        parent::report($exception);
    }

    
    public function render($request, Exception $e)
    {
        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        }
        
        $t = $this->getExceptionResponse($e);
        if (null !== $t) {
            return $t;
        }

        return parent::render($request, $e);
    }

    
    protected function convertExceptionToResponse(Exception $e)
    {
        $t = $this->getExceptionResponse($e);
        if (null !== $t) {
            return $t;
        }
        if (config('env.APP_DEBUG', true)) {
            return parent::convertExceptionToResponse($e);
        }
        return response()->view('errors.500', ['exception' => $e], 500);
    }
}
