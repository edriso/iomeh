<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Inertia\Inertia;
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

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Handle ModelNotFoundException specifically for 404s
        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Resource not found'], 404);
            }
            return Inertia::render('errors/404');
        }

        // Handle 404 errors even in debug mode for better UX
        if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Resource not found'], 404);
            }
            return Inertia::render('errors/404');
        }

        // Handle CSRF token mismatch (419)
        if ($e instanceof \Illuminate\Session\TokenMismatchException) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'CSRF token mismatch'], 419);
            }
            return Inertia::render('errors/419', [
                'title' => 'Session Expired',
                'message' => 'Your session has expired. Please refresh the page to continue.',
            ]);
        }

        // Handle authentication errors (401)
        if ($e instanceof \Illuminate\Auth\AuthenticationException) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
            return Inertia::render('errors/401', [
                'title' => 'Authentication Required',
                'message' => 'You need to be logged in to access this page.',
            ]);
        }

        $response = parent::render($request, $e);
        $status = $response->getStatusCode();

        if ($request->expectsJson() || $request->is('api/*')) {
            return $response;
        }

        // Handle specific error pages
        if ($status === 401) {
            return Inertia::render('errors/401', [
                'title' => 'Authentication Required',
                'message' => 'You need to be logged in to access this page.',
            ]);
        }

        if ($status === 404) {
            return Inertia::render('errors/404');
        }

        if ($status === 419) {
            return Inertia::render('errors/419', [
                'title' => 'Session Expired',
                'message' => 'Your session has expired. Please refresh the page to continue.',
            ]);
        }

        if ($status === 500) {
            return Inertia::render('errors/500');
        }

        // Handle other error codes
        if ($status >= 400 && $status < 600) {
            return Inertia::render('errors/error', [
                'status' => $status,
                'message' => $e->getMessage() ?: 'An error occurred',
            ]);
        }

        return $response;
    }
}
