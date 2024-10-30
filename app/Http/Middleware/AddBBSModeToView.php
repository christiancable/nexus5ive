<?php

namespace App\Http\Middleware;

use App\Models\Mode;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class AddBBSModeToView
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // share bbs mode to all views
        // wrap in try because this isn't aways run when we have a db
        // @todo make this bit suck less
        try {
            $seconds_to_cache = 3600;
            $mode = Cache::remember('bbs_mode', $seconds_to_cache, function () {
                return Mode::active()->with('theme')->first();
            });
        } catch (\Throwable $th) {
            Log::info('Failed to fetch mode '.print_r($th, true));
            $mode = null;
        }
        View::share('mode', $mode);

        return $next($request);
    }
}
