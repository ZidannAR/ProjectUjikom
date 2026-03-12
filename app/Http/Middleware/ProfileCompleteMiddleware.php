<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileCompleteMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->employee_id) {
            $employee = $user->employee;

            if ($employee && !$employee->isProfileComplete()) {
                return response()->json([
                    'message' => 'Profil belum lengkap',
                    'redirect' => '/profile/complete',
                    'profile_incomplete' => true,
                ], 403);
            }
        }

        return $next($request);
    }
}
