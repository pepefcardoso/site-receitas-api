<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Lê as origens permitidas do arquivo .env, separadas por vírgula.
        // Em produção, isso deve ser definido para a URL do seu frontend.
        // Ex: CORS_ALLOWED_ORIGINS=https://meuapp.com,https://www.meuapp.com
        $allowedOrigins = explode(',', env('CORS_ALLOWED_ORIGINS', ''));
        $origin = $request->headers->get('Origin');

        // Apenas adiciona os headers de CORS se a origem da requisição for permitida.
        if ($origin && in_array($origin, $allowedOrigins)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, PATCH, DELETE');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-Auth-Token, Origin, Authorization, Accept');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        }

        return $response;
    }
}