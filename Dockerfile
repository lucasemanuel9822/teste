# Dockerfile para aplicação Laravel Lumen
# 
# Este Dockerfile cria uma imagem otimizada para produção
# com PHP 8.2, extensões necessárias e configurações de segurança.

# Usa imagem oficial do PHP com Apache
FROM php:8.2-apache

# Define variáveis de ambiente
ENV PHP_FPM_USER=www-data
ENV PHP_FPM_GROUP=www-data

# Instala dependências do sistema
RUN apt-get update && apt-get install -y \
    wget \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    libicu-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config \
    libsasl2-dev \
    libmongoc-dev \
    default-mysql-client \
    curl \
    netcat-openbsd \
    dos2unix \
    && rm -rf /var/lib/apt/lists/*

# Instala extensões PHP necessárias
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    curl \
    opcache

# INSTALAÇÃO DO DRIVER MONGODB
# 1. Instala o driver via PECL (versão específica compatível)
RUN pecl install mongodb-1.21.0 \
    # 2. Habilita a extensão
    && docker-php-ext-enable mongodb \
    # 3. Limpa o cache para reduzir o tamanho da imagem
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configura Apache
RUN a2enmod rewrite
RUN echo '<Directory /var/www/html/public>' >> /etc/apache2/apache2.conf \
    && echo '    AllowOverride All' >> /etc/apache2/apache2.conf \
    && echo '</Directory>' >> /etc/apache2/apache2.conf

# Configura DocumentRoot para Laravel/Lumen
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Cria diretório da aplicação
WORKDIR /var/www/html

# Copiar script de inicialização primeiro
COPY init-app.sh /usr/local/bin/init-app.sh
RUN chmod +x /usr/local/bin/init-app.sh && \
    dos2unix /usr/local/bin/init-app.sh && \
    ls -la /usr/local/bin/init-app.sh && \
    file /usr/local/bin/init-app.sh

# Copia arquivos de configuração
COPY composer.json ./

# Instala dependências PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copia código da aplicação
COPY . .

# Cria diretórios necessários
RUN mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/bootstrap/cache

# Define permissões corretas
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Configura PHP para produção
RUN echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/docker-php-memory.ini \
    && echo "upload_max_filesize = 64M" >> /usr/local/etc/php/conf.d/docker-php-upload.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/docker-php-upload.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/docker-php-execution.ini

# Configura OPcache para melhor performance
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/docker-php-opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/docker-php-opcache.ini \
    && echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/docker-php-opcache.ini \
    && echo "opcache.max_accelerated_files=4000" >> /usr/local/etc/php/conf.d/docker-php-opcache.ini \
    && echo "opcache.revalidate_freq=2" >> /usr/local/etc/php/conf.d/docker-php-opcache.ini \
    && echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/docker-php-opcache.ini

# Expõe porta 80 (Apache)
EXPOSE 80

# Comando de inicialização
CMD ["/usr/local/bin/init-app.sh"]


