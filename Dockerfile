# --- ESTÁGIO 1: Builder ---
# Apenas a palavra "as" foi alterada para "AS" para corrigir o aviso (warning).
FROM php:8.2-fpm AS builder

# Instala dependências do sistema e extensões PHP
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd \
    && pecl install redis && docker-php-ext-enable redis

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www

# Copia apenas os ficheiros do composer
COPY composer.json composer.lock ./

# Instala as dependências SEM EXECUTAR SCRIPTS
# -------------------- MUDANÇA 1 --------------------
RUN composer install --no-interaction --no-dev --optimize-autoloader --no-scripts

# Copia o restante do código da aplicação
COPY . .

# Agora, executa os scripts do composer e as otimizações do Laravel
# -------------------- MUDANÇA 2 --------------------
RUN composer dump-autoload --no-dev --optimize && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache


# --- ESTÁGIO 2: Final ---
# Esta parte permanece igual.
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y libpq-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd \
    && pecl install redis && docker-php-ext-enable redis

WORKDIR /var/www

COPY --from=builder /var/www .

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
