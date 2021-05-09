<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;

class MustNotBeLegacy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {        
        $user = $request->user();

        if ($user && $user->legacy) {
            Auth::logout();
            return redirect()->route('inactive');
        }

        return $next($request);
    }
}
