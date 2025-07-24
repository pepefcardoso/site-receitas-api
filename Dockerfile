# --- ESTÁGIO 1: Builder ---
# Usamos uma imagem completa para instalar dependências
FROM php:8.2-fpm as builder

# Instala dependências do sistema e extensões PHP
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd \
    && pecl install redis && docker-php-ext-enable redis

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www

# Copia apenas os ficheiros do composer para aproveitar o cache do Docker
COPY composer.json composer.lock ./

# Instala as dependências de produção e otimiza o autoloader
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Copia o restante do código da aplicação
COPY . .

# Gera as otimizações de cache do Laravel
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache


# --- ESTÁGIO 2: Final ---
# Usamos uma imagem limpa apenas com o necessário para rodar
FROM php:8.2-fpm

# Instala apenas as extensões PHP necessárias para rodar a aplicação
RUN apt-get update && apt-get install -y libpq-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd \
    && pecl install redis && docker-php-ext-enable redis

# Define o diretório de trabalho
WORKDIR /var/www

# Copia o código e as dependências já instaladas do estágio "builder"
COPY --from=builder /var/www .

# Ajusta as permissões das pastas de storage e cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expõe a porta 9000 para o FPM
EXPOSE 9000

# Comando para iniciar o PHP-FPM
CMD ["php-fpm"]
