<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redirect;

class AdminCheckMiddleware
{

     /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!auth()->guard('admin')->check() || !auth()->guard('admin')->user()->hasRole('admin')){
            return Redirect::route('admin.login');
        }

        return $next($request);
    }

}
