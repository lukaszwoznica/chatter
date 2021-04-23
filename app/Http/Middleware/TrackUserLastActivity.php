<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackUserLastActivity
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->guest()) {
            return $next($request);
        }

        if (optional(auth()->user()->last_online_at)->diffInMinutes(now()) !== 0) {
            DB::table('users')
                ->where('id', auth()->id())
                ->update(['last_online_at' => now()]);
        }

        return $next($request);
    }
}
