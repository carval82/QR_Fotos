<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminBasicAuth
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expectedUser = (string) config('admin.user');
        $expectedPassword = (string) config('admin.password');

        $user = (string) $request->getUser();
        $password = (string) $request->getPassword();

        if ($user !== $expectedUser || $password !== $expectedPassword) {
            return response('Unauthorized', 401)->header('WWW-Authenticate', 'Basic realm="Admin"');
        }

        return $next($request);
    }
}
