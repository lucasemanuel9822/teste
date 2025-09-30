<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Carbon as CarbonHelper;

/**
 * ThrottleRequestsMiddleware
 * 
 * Middleware para rate limiting (limitação de requisições).
 * Implementa o segundo pilar de segurança: Proteção contra ataques.
 * 
 * Este middleware limita o número de requisições que um cliente
 * pode fazer em um período de tempo, protegendo contra ataques
 * de força bruta e DDoS.
 */
class ThrottleRequestsMiddleware
{
    /**
     * Número máximo de requisições por minuto
     * 
     * @var int
     */
    protected int $maxRequests = 100;

    /**
     * Período de tempo em minutos
     * 
     * @var int
     */
    protected int $timeWindow = 1;

    /**
     * Manipula uma requisição HTTP
     * 
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {
        // Obtém o IP do cliente
        $clientIp = $this->getClientIp($request);
        
        // Gera a chave do cache baseada no IP
        $cacheKey = "throttle:{$clientIp}";
        
        // Obtém o número atual de requisições
        $currentRequests = Cache::get($cacheKey, 0);
        
        // Verifica se o limite foi excedido
        if ($currentRequests >= $this->maxRequests) {
            // Log da tentativa de rate limiting
            Log::warning('Rate limit excedido', [
                'ip' => $clientIp,
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'current_requests' => $currentRequests,
                'max_requests' => $this->maxRequests,
            ]);

            return response()->json([
                'error' => 'Muitas requisições',
                'message' => 'Você excedeu o limite de requisições. Tente novamente em alguns minutos.',
                'code' => 'RATE_LIMIT_EXCEEDED',
                'retry_after' => $this->timeWindow * 60 // em segundos
            ], 429)->header('Retry-After', $this->timeWindow * 60);
        }
        
        // Incrementa o contador de requisições
        Cache::put($cacheKey, $currentRequests + 1, CarbonHelper::now()->addMinutes($this->timeWindow));
        
        // Continua com a requisição
        $response = $next($request);
        
        // Adiciona cabeçalhos informativos
        $response->headers->set('X-RateLimit-Limit', $this->maxRequests);
        $response->headers->set('X-RateLimit-Remaining', max(0, $this->maxRequests - $currentRequests - 1));
        $response->headers->set('X-RateLimit-Reset', CarbonHelper::now()->addMinutes($this->timeWindow)->timestamp);
        
        return $response;
    }

    /**
     * Obtém o IP real do cliente
     * 
     * @param Request $request
     * @return string
     */
    protected function getClientIp(Request $request): string
    {
        // Verifica cabeçalhos de proxy primeiro
        $ip = $request->header('X-Forwarded-For');
        if ($ip) {
            return explode(',', $ip)[0];
        }
        
        $ip = $request->header('X-Real-IP');
        if ($ip) {
            return $ip;
        }
        
        // Fallback para o IP direto
        return $request->ip();
    }
}


