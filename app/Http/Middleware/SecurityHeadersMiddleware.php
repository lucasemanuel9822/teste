<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * SecurityHeadersMiddleware
 * 
 * Middleware para adicionar cabeçalhos de segurança HTTP.
 * Implementa o quarto pilar de segurança: Boas Práticas HTTP.
 * 
 * Este middleware adiciona cabeçalhos de segurança que previnem
 * ataques comuns como clickjacking e melhoram a segurança geral.
 */
class SecurityHeadersMiddleware
{
    /**
     * Manipula uma requisição HTTP
     * 
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $response = $next($request);

        // Adiciona cabeçalhos de segurança
        $this->addSecurityHeaders($response);

        return $response;
    }

    /**
     * Adiciona cabeçalhos de segurança à resposta
     * 
     * @param SymfonyResponse $response
     * @return void
     */
    protected function addSecurityHeaders(SymfonyResponse $response): void
    {
        // Previne clickjacking
        $response->headers->set('X-Frame-Options', 'DENY');

        // Previne MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Política de segurança de conteúdo
        $response->headers->set('Content-Security-Policy', 
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline'; " .
            "style-src 'self' 'unsafe-inline'; " .
            "img-src 'self' data: https:; " .
            "font-src 'self'; " .
            "connect-src 'self'; " .
            "frame-ancestors 'none';"
        );

        // Força HTTPS em produção
        if (env('APP_ENV') === 'production') {
            $response->headers->set('Strict-Transport-Security', 
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // Controle de referrer
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissões de recursos
        $response->headers->set('Permissions-Policy', 
            'geolocation=(), ' .
            'microphone=(), ' .
            'camera=(), ' .
            'payment=(), ' .
            'usb=(), ' .
            'magnetometer=(), ' .
            'gyroscope=(), ' .
            'speaker=(), ' .
            'vibrate=(), ' .
            'fullscreen=(self), ' .
            'sync-xhr=()'
        );

        // Remove cabeçalho X-Powered-By
        $response->headers->remove('X-Powered-By');

        // Adiciona cabeçalho personalizado
        $response->headers->set('X-API-Version', '1.0');
        
        // Calcula tempo de resposta (LARAVEL_START é definida no bootstrap)
        if (defined('LARAVEL_START')) {
            $response->headers->set('X-Response-Time', microtime(true) - LARAVEL_START);
        }
    }
}


