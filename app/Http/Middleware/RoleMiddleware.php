<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
   */
  public function handle(Request $request, Closure $next, $role)
  {
    // Check if the user is authenticated and has the required role
    if (Auth::check() && Auth::user()->type === $role) {
      return $next($request);
    }

    // Redirect or abort if the user doesn't have the required role
    return redirect('/')->with('error', 'You do not have access to this page.');
  }
}
