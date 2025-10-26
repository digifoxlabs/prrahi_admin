<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{

     public function handle($request, Closure $next, $permission)
        {
        $user = Auth::guard('admin')->user();

        if ($user && $user->hasPermission($permission)) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
