<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;


use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Exception Handler
 * 
 * Handler global para exceções da aplicação.
 * Necessário para o funcionamento do queue worker.
 */
class Handler implements ExceptionHandlerContract
{
    /**
     * Lista de exceções que não devem ser reportadas
     *
     * @var array
     */
    protected $dontReport = [
        AuthenticationException::class,
        HttpException::class,
        ValidationException::class,
    ];

    /**
     * Reporta ou loga uma exceção
     *
     * @param Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        // Log da exceção se necessário
        if ($this->shouldReport($exception)) {
            error_log($exception->getMessage());
        }
    }

    /**
     * Renderiza uma exceção em uma resposta HTTP
     *
     * @param Request $request
     * @param Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof HttpResponseException) {
            return $exception->getResponse();
        }

        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }

        if ($exception instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($exception, $request);
        }

        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Recurso não encontrado',
                'code' => 'NOT_FOUND'
            ], 404);
        }

        if ($exception instanceof HttpException) {
            return response()->json([
                'error' => 'HTTP Error',
                'message' => $exception->getMessage(),
                'code' => 'HTTP_ERROR'
            ], $exception->getStatusCode());
        }

        // Para outras exceções, retorna erro genérico
        return response()->json([
            'error' => 'Internal Server Error',
            'message' => 'Erro interno do servidor',
            'code' => 'INTERNAL_ERROR'
        ], 500);
    }

    /**
     * Verifica se a exceção deve ser reportada
     *
     * @param Exception $exception
     * @return bool
     */
    public function shouldReport(Exception $exception)
    {
        foreach ($this->dontReport as $type) {
            if ($exception instanceof $type) {
                return false;
            }
        }

        return true;
    }

    /**
     * Converte exceção de autenticação em resposta JSON
     *
     * @param Request $request
     * @param AuthenticationException $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json([
            'error' => 'Unauthenticated',
            'message' => 'Token de autenticação inválido',
            'code' => 'UNAUTHENTICATED'
        ], 401);
    }

    /**
     * Converte exceção de validação em resposta JSON
     *
     * @param ValidationException $e
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        if ($e->response) {
            return $e->response;
        }

        return response()->json([
            'error' => 'Validation Error',
            'message' => 'Dados de entrada inválidos',
            'errors' => $e->errors(),
            'code' => 'VALIDATION_ERROR'
        ], 422);
    }
}
