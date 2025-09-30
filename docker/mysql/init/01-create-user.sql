-- Script de inicialização do MySQL
-- Cria usuário específico para a aplicação com permissões limitadas
-- Cria usuário para a aplicação
CREATE USER IF NOT EXISTS 'task_app_user' @'%' IDENTIFIED BY 'secure_password_123';
-- Concede permissões específicas apenas nas tabelas necessárias
GRANT SELECT,
    INSERT,
    UPDATE,
    DELETE ON task_management.* TO 'task_app_user' @'%';
-- Aplica as permissões
FLUSH PRIVILEGES;
-- Cria tabela de jobs falhados se não existir
CREATE TABLE IF NOT EXISTS task_management.failed_jobs (
    id bigint unsigned NOT NULL AUTO_INCREMENT,
    uuid varchar(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload longtext NOT NULL,
    exception longtext NOT NULL,
    failed_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY failed_jobs_uuid_unique (uuid)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;


