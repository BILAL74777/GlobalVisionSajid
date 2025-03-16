<?php
 
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
{
    if (!Auth::check()) {
        return redirect('/login');
    }

    $userRole = Auth::user()->role; // Get user's role
    \Log::info("User Role: $userRole"); // Log role for debugging

    // Fix role comparison
    if ($userRole === 'super admin') { // Ensure space matches DB
        return $next($request);
    }

    if (in_array($userRole, $roles)) {
        return $next($request);
    }

    abort(403, 'Unauthorized access.');
}

}

