<?php

namespace App\Http\Middleware;

use Closure;

class XSS
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
            $host = $request->getHost();
            // if($host=="127.0.0.1"){
                $userInput = $request->all();
                array_walk_recursive($userInput, function (&$userInput) {
                    $userInput = strip_tags($userInput);
                });
                $request->merge($userInput);
                return $next($request);
            // }else abort(503);
    }
}
