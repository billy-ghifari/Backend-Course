<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class checkRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        foreach ($roles as $jabatan) {
            // Check if user has the role This check will depend on how your roles are set up
            if ($user->role == $jabatan) {
                return $next($request);
            }
        }
        return response()->json([
            'message' => 'Anda tidak dapat mengakses fitur ini',
            'warning' => 'Yang dapat mengakses',
            'role' => $roles
        ], 403);
    }
}
