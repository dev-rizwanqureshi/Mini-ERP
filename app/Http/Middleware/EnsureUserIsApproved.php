<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isApproved()) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Your account is on hold until an admin assigns a role.');
        }

        return $next($request);
    }
}
