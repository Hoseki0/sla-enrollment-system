<?php

namespace App\Http\Middleware;

use App\Http\Middleware\Concerns\PreventsBackHistory;
use Closure;
use Illuminate\Http\Request;

class ParentAuth
{
    use PreventsBackHistory;

    public function handle(Request $request, Closure $next)
    {
        if (!auth('parent')->check()) {
            return redirect()->route('parent.login');
        }
        return $this->withNoCacheHeaders($next($request));
    }
}
