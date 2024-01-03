<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddUserDataToResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (auth()->check()) {
            // If a user is authenticated, you can access their data here
            $user = auth()->user();
            $user->roles;
            $user->permissions;
            // Modify the response to include user data
            $response->setData([
                'user' => $user,
                'data' => $response->getData(),
            ]);
        }

        return $response;
    }
}
