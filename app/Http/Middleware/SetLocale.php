<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = (array) Config::get('app.supported_locales', [Config::get('app.locale')]);

        $sessionLocale = $request->session()->get('locale');
        $locale = is_string($sessionLocale) && in_array($sessionLocale, $supported, true)
            ? $sessionLocale
            : Config::get('app.locale');

        try {
            app()->setLocale($locale);
            if (method_exists($request, 'setRequestLocale')) {
                $request->setRequestLocale($locale);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return $next($request);
    }
}
