<?php

namespace App\Http\Middleware;

use App\Http\Middleware\Concerns\PreventsBackHistory;
use Closure;
use Illuminate\Http\Request;

class AdminAuth
{
    use PreventsBackHistory;

    public function handle(Request $request, Closure $next)
    {
        if (!auth('admin')->check()) {
            return redirect()->route('admin.login');
        }
        return $this->withNoCacheHeaders($next($request));
    }
}
