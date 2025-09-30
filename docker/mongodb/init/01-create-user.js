// Script de inicialização do MongoDB
// Cria usuário específico para a aplicação com permissões limitadas

// Conecta ao banco de dados de logs
db = db.getSiblingDB('task_logs');

// Cria usuário para a aplicação
db.createUser({
  user: 'task_app_user',
  pwd: 'secure_password_123',
  roles: [
    {
      role: 'readWrite',
      db: 'task_logs'
    }
  ]
});

// Cria coleção de logs se não existir
db.createCollection('logs');

// Cria índices para otimização de consultas
db.logs.createIndex({ "entity_id": 1 });
db.logs.createIndex({ "entity_type": 1 });
db.logs.createIndex({ "created_at": -1 });
db.logs.createIndex({ "entity_type": 1, "entity_id": 1 });
db.logs.createIndex({ "action": 1 });

print('Usuário e índices criados com sucesso no MongoDB');



