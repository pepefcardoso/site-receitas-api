# Usa uma imagem base oficial do PHP 8.2 com FPM
FROM php:8.2-fpm

# Define o diretório de trabalho
WORKDIR /var/www

# Instala dependências do sistema e extensões PHP necessárias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    librdkafka-dev

# Instala as extensões PHP
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Instala a extensão Redis via PECL
RUN pecl install redis && docker-php-ext-enable redis

# Instala o Composer (gerenciador de dependências do PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia os arquivos da aplicação para dentro do container
COPY . /var/www

# Ajusta as permissões da pasta de storage
RUN chown -R www-data:www-data /var/www/storage

# Expõe a porta 9000 para o FPM
EXPOSE 9000