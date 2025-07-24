# --- ESTÁGIO 1: Builder ---
# Apenas a palavra "as" foi alterada para "AS" para corrigir o aviso (warning).
FROM php:8.2-fpm AS builder

# Instala dependências do sistema e extensões PHP
RUN apt-get update && \
    apt-get install -y git curl unzip libpq-dev libpng-dev libonig-dev libxml2-dev && \
    docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd && \
    pecl install redis && docker-php-ext-enable redis && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

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
FROM php:8.2-fpm

# Instalar apenas runtime dependencies
RUN apt-get update && \
    apt-get install -y libpq-dev libpng-dev libonig-dev libxml2-dev && \
    docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd && \
    pecl install redis && docker-php-ext-enable redis && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Configurações de segurança do PHP
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" && \
    sed -i 's/expose_php = On/expose_php = Off/' "$PHP_INI_DIR/php.ini"

WORKDIR /var/www

COPY --from=builder /var/www .

# Ajuste de permissões seguro
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && \
    find /var/www -type d -exec chmod 755 {} \; && \
    find /var/www -type f -exec chmod 644 {} \;

# Healthcheck para o FPM
HEALTHCHECK --interval=30s --timeout=3s \
    CMD curl -f http://localhost:9000/ping || exit 1

EXPOSE 9000
CMD ["php-fpm"]
