<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTeacherRole
{
    /**
     * Vérifie si l'utilisateur authentifié a le rôle "teacher".
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('api')->user();

        if (!$user || $user->role !== 'teacher') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only teachers can access this resource.',
                'code' => 403,
            ], 403);
        }

        return $next($request);
    }
}
