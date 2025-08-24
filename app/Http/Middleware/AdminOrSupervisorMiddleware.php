<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminOrSupervisorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || (!Auth::user()->hasType('admin') && !Auth::user()->hasType('supervisor'))) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized. Admin or Supervisor access required.'], 403);
            }
            
            return redirect()->route('home')->with('error', 'Unauthorized. Admin or Supervisor access required.');
        }
        
        return $next($request);
    }
}
