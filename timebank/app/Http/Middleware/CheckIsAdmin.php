<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config; // Added for Config facade
use Symfony\Component\HttpFoundation\Response;

class CheckIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('login'); // Or abort(401) if API
        }

        // Fallback: Check against a list of admin emails from config if 'is_admin' column doesn't exist or migration failed
        $adminEmails = Config::get('app.admin_users', []);
        $userIsAdminByEmail = in_array(Auth::user()->email, $adminEmails);

        if ($userIsAdminByEmail) {
            return $next($request);
        }

        // Primary check: 'is_admin' database column (if migration was successful)
        // This part will effectively be skipped if the column doesn't exist,
        // but we leave it for future-proofing if the DB issue is resolved.
        if (Schema::hasColumn('users', 'is_admin') && Auth::user()->is_admin) {
             return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
