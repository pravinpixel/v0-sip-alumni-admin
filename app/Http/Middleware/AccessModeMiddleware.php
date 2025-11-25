<?php

namespace App\Http\Middleware;

use Closure;

class AccessModeMiddleware
{
    public function handle($request, Closure $next, $type)
    {
        $mode = env('ACCESS_TYPE', 'both'); // default = both

        // If both allowed → skip restrictions
        if ($mode === 'both') {
            return $next($request);
        }
        
        if ($mode === 'admin' && $request->is('/')) {
            return redirect('/admin');
        }

        // If the route type does NOT match the mode → block
        if ($mode !== $type) {
            return abort(404, "Page not found");
        }

        return $next($request);
    }
}
