<?php

namespace App\Http\Middleware;

use App\Http\Middleware\Concerns\PreventsBackHistory;
use Closure;
use Illuminate\Http\Request;

class StudentAuth
{
    use PreventsBackHistory;

    public function handle(Request $request, Closure $next)
    {
        if (!auth('student')->check()) {
            return redirect()->route('student.login');
        }
        return $this->withNoCacheHeaders($next($request));
    }
}
