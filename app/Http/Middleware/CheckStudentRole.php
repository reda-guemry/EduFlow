<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStudentRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('api')->user();

        if (!$user || $user->role !== 'student') {
            return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only students can access this resource.',
                    'code' => 403,
                ],403);
        }

        return $next($request);
    }

}
