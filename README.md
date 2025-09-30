# Sistema de Gerenciamento de Tarefas

Sistema de gerenciamento de tarefas desenvolvido com Laravel Lumen, implementando uma API RESTful completa com arquitetura baseada nos princípios SOLID.

## 🚀 Características

- **API RESTful** completa para gerenciamento de tarefas
- **Arquitetura SOLID** com separação clara de responsabilidades
- **Banco de dados híbrido**: MySQL para tarefas, MongoDB para logs
- **Segurança robusta**: API Key, Rate Limiting, Headers de Segurança
- **Documentação Swagger** automática
- **Testes unitários** e de integração
- **Containerização Docker** completa

## 📋 Funcionalidades

### Endpoints de Tarefas
- `POST /tasks` - Criar nova tarefa
- `GET /tasks` - Listar tarefas (com filtros por status)
- `GET /tasks/{id}` - Buscar tarefa por ID
- `PUT /tasks/{id}` - Atualizar tarefa
- `DELETE /tasks/{id}` - Excluir tarefa

### Endpoints de Logs
- `GET /logs` - Listar logs recentes (últimos 30)
- `GET /logs?id={id}` - Buscar log específico por ID

### Endpoints do Sistema
- `GET /health` - Health check da API
- `GET /info` - Informações da API
- `GET /swagger.json` - Documentação Swagger
- `GET /swagger.html` - Interface Swagger UI

## 🛠️ Tecnologias Utilizadas

- **PHP 8.2+** - Linguagem de programação
- **Laravel Lumen 11+** - Framework PHP
- **MySQL 8.0** - Banco de dados relacional para tarefas
- **MongoDB 7.0** - Banco de dados não-relacional para logs
- **Docker** - Containerização
- **Swagger/OpenAPI** - Documentação da API

## 📦 Instalação

### Pré-requisitos

- Docker e Docker Compose
- Git

### 1. Clone o repositório

```bash
git clone <repository-url>
cd task-management-system
```

### 2. Configure as variáveis de ambiente

```bash
cp env.example .env
```

Edite o arquivo `.env` com suas configurações:

```env
# API Security
API_KEY=sua-chave-api-aqui

# Database MySQL
DB_PASSWORD=sua-senha-mysql-aqui

# Database MongoDB
MONGODB_PASSWORD=sua-senha-mongodb-aqui


### 3. Execute com Docker Compose

#### Desenvolvimento
```bash
docker-compose up -d
```

#### Produção
```bash
docker-compose -f docker-compose.prod.yml up -d
```

### 4. Execute as migrações

```bash
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```

## 🚀 Uso da API

### Autenticação

A API utiliza autenticação por API Key. Inclua o cabeçalho `X-API-KEY` em todas as requisições que modificam dados (POST, PUT, DELETE).

```bash
curl -H "X-API-KEY: sua-chave-api" \
     -H "Content-Type: application/json" \
     -d '{"title":"Nova Tarefa","description":"Descrição da tarefa","status":"pending"}' \
     http://localhost:8000/tasks
```

### Exemplos de Uso

#### Criar uma tarefa
```bash
curl -X POST http://localhost:8000/tasks \
  -H "Content-Type: application/json" \
  -H "X-API-KEY: sua-chave-api" \
  -d '{
    "title": "Implementar autenticação",
    "description": "Criar sistema de autenticação usando API Key",
    "status": "pending"
  }'
```

#### Listar tarefas
```bash
curl http://localhost:8000/tasks
```

#### Listar tarefas por status
```bash
curl "http://localhost:8000/tasks?status=pending"
```

#### Atualizar uma tarefa
```bash
curl -X PUT http://localhost:8000/tasks/1 \
  -H "Content-Type: application/json" \
  -H "X-API-KEY: sua-chave-api" \
  -d '{
    "title": "Implementar autenticação",
    "status": "in_progress"
  }'
```

#### Excluir uma tarefa
```bash
curl -X DELETE http://localhost:8000/tasks/1 \
  -H "X-API-KEY: sua-chave-api"
```

#### Listar logs recentes
```bash
curl http://localhost:8000/logs
```

#### Buscar log específico
```bash
curl "http://localhost:8000/logs?id=507f1f77bcf86cd799439011"
```

## 📚 Documentação

### Swagger UI

Acesse a documentação interativa da API em:
- **Desenvolvimento**: http://localhost:8000/swagger.html
- **Produção**: https://seu-dominio.com/swagger.html

### Endpoints de Informação

- **Health Check**: `GET /health`
- **Informações da API**: `GET /info`
- **Documentação Swagger**: `GET /swagger.json`

## 🧪 Testes

### Executar todos os testes
```bash
docker-compose exec app vendor/bin/phpunit
```

### Executar testes específicos
```bash
# Testes unitários
docker-compose exec app vendor/bin/phpunit tests/Unit

# Testes de integração
docker-compose exec app vendor/bin/phpunit tests/Feature

# Teste específico
docker-compose exec app vendor/bin/phpunit tests/Feature/TaskControllerTest.php
```

### Cobertura de testes
```bash
docker-compose exec app vendor/bin/phpunit --coverage-html coverage
```

## 🏗️ Arquitetura

### Princípios SOLID Implementados

1. **Single Responsibility Principle (SRP)**
   - Cada classe tem uma única responsabilidade
   - Controllers apenas recebem requisições
   - Services contêm lógica de negócio
   - Repositories gerenciam persistência

2. **Open/Closed Principle (OCP)**
   - Interfaces permitem extensão sem modificação
   - Novos tipos de logs podem ser adicionados facilmente

3. **Liskov Substitution Principle (LSP)**
   - Implementações podem ser substituídas por suas interfaces
   - Repositories podem ser trocados sem afetar Services

4. **Interface Segregation Principle (ISP)**
   - Interfaces específicas para cada responsabilidade
   - TaskRepositoryInterface e LogRepositoryInterface separadas

5. **Dependency Inversion Principle (DIP)**
   - Dependências são injetadas via interfaces
   - Services dependem de abstrações, não de implementações


## 🔒 Segurança

### Middlewares Implementados

1. **ApiKeyMiddleware**
   - Autenticação via API Key
   - Protege endpoints que modificam dados

2. **ThrottleRequestsMiddleware**
   - Rate limiting (100 req/min por IP)
   - Proteção contra ataques de força bruta

3. **TrimStringsMiddleware**
   - Limpeza e higienização de dados
   - Remove espaços e converte strings vazias para null

4. **SecurityHeadersMiddleware**
   - Headers de segurança HTTP
   - Proteção contra clickjacking e XSS

### Configurações de Segurança

- **Princípio do Mínimo Privilégio**: Usuários específicos para cada banco
- **Conexões TLS/SSL**: Criptografia em trânsito (produção)
- **Validação de Dados**: Validação rigorosa de entrada
- **Logs de Segurança**: Registro de tentativas de acesso

## 📊 Monitoramento

### Health Check
```bash
curl http://localhost:8000/health
```

### Logs da Aplicação
```bash
# Logs do container da aplicação
docker-compose logs -f app

```

### Redis - Cache e Rate Limiting
```bash
# Ver todas as chaves do Redis
docker-compose exec redis redis-cli keys "*"

# Ver chaves de rate limiting
docker-compose exec redis redis-cli keys "throttle:*"

# Ver valor de uma chave específica
docker-compose exec redis redis-cli get "task_managementthrottle:172.18.0.1"

# Ver TTL (tempo de vida) de uma chave
docker-compose exec redis redis-cli ttl "task_managementthrottle:172.18.0.1"

# Contar total de chaves
docker-compose exec redis redis-cli dbsize

# Monitorar comandos em tempo real
docker-compose exec redis redis-cli monitor

# Limpar todas as chaves
docker-compose exec redis redis-cli flushall
```

**Configuração do Rate Limiting:**
- **Limite:** 100 requisições por minuto por IP
- **Janela:** 1 minuto
- **Chave:** `throttle:{ip}`
- **TTL:** 1 minuto

### Métricas de Performance
- **Response Time**: Incluído nos headers de resposta
- **Rate Limiting**: Headers X-RateLimit-*
- **Cache Hit Rate**: Monitorado via Redis

## 🚀 Deploy em Produção

### 1. Configure variáveis de ambiente
```bash
cp env.example .env.production
```

### 2. Configure SSL/TLS
```bash
# Coloque seus certificados em docker/nginx/ssl/
cp cert.pem docker/nginx/ssl/
cp key.pem docker/nginx/ssl/
```

### 3. Execute em produção
```bash
docker-compose -f docker-compose.prod.yml up -d
```

### 4. Configure backup
```bash
# Backup do MySQL
docker-compose exec mysql mysqldump -u root -p task_management > backup.sql

# Backup do MongoDB
docker-compose exec mongodb mongodump --db task_logs --out /backup
```




