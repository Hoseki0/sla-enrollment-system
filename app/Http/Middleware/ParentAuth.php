<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ParentAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth('parent')->check()) {
            return redirect()->route('parent.login');
        }
        return $next($request);
    }
}
