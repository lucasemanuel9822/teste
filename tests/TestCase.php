<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * TestCase
 * 
 * Classe base para todos os testes da aplicação.
 * Configura o ambiente de teste e fornece métodos auxiliares.
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Configuração inicial dos testes
     * 
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Configurações específicas para testes
        $this->setUpTestEnvironment();
    }

    /**
     * Configura o ambiente de teste
     * 
     * @return void
     */
    protected function setUpTestEnvironment(): void
    {
        // Define variáveis de ambiente para testes
        putenv('APP_ENV=testing');
        putenv('APP_DEBUG=true');
        putenv('DB_CONNECTION=sqlite');
        putenv('DB_DATABASE=:memory:');
        putenv('CACHE_DRIVER=array');
        putenv('QUEUE_CONNECTION=sync');
        putenv('API_KEY=test-api-key-123');
    }

    /**
     * Cria uma instância da aplicação
     * 
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }
}



