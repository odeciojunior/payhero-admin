<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Facades\Whitelabel;

class WhitelabelMiddleware
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
        // Check if client parameter is present in the request
        $clientParam = config('whitelabel.client_detection.parameter_name', 'client');
        
        if ($request->has($clientParam)) {
            $client = $request->get($clientParam);
            Whitelabel::setClient($client);
        }

        // Share whitelabel data with all views
        view()->share('whitelabel', app('whitelabel'));
        view()->share('whitelabelClient', Whitelabel::getCurrentClient());
        view()->share('whitelabelConfig', Whitelabel::getCurrentClientConfig());
        
        // Add whitelabel CSS link to response headers for better performance
        $response = $next($request);
        
        // Only add CSS link for HTML responses
        if ($response->headers->get('Content-Type') && 
            str_contains($response->headers->get('Content-Type'), 'text/html')) {
            
            $cssUrl = route('whitelabel.css');
            $response->headers->set('Link', "<{$cssUrl}>; rel=preload; as=style", false);
        }

        return $response;
    }
}