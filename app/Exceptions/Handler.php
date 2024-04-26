<?php

namespace App\Exceptions;

use App\Services\Backend\UserLogService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($request->ajax()) {
            $isInertiaJs = $request->header('X-Inertia', 'false') == 'true';
            if ($exception instanceof ValidationException) {
                (new UserLogService)->addLog(auth('users')->user(), null, false);
                if ($isInertiaJs)
                    return back()->withErrors($exception->errors());
                return response()->json(['code' => 500, 'message' => head(head($exception->errors()))]);
            }
            if ($exception instanceof NotFoundHttpException) {
                (new UserLogService)->addLog(auth('users')->user(), $exception->getMessage(), false);
                if ($isInertiaJs)
                    return back()->withErrors($exception->getMessage());
                return response()->json(['code' => 404, 'message' => $exception->getMessage()], 404);
            }
            if ($exception instanceof MethodNotAllowedHttpException) {
                (new UserLogService)->addLog(auth('users')->user(), null, false);
                if ($isInertiaJs)
                    return back()->withErrors($exception->getMessage());
                return response()->json(['code' => 404, 'message' => $exception->getMessage()], 404);
            }
        }
        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->ajax()) {
            (new UserLogService)->addLog(auth('users')->user(), null, false);
            return $this->shouldReturnJson($request, $exception)
                ? response()->json(['message' => $exception->getMessage(), 'code' => 401], 401)
                : redirect()->guest($exception->redirectTo() ?? route('login'));
        }
        (new UserLogService)->addLog(auth('users')->user(), null, false);
        return parent::unauthenticated($request, $exception);
    }
}
