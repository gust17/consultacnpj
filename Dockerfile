FROM php:8.2-apache

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    libzip-dev \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    gnupg

# Limpar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar Node.js e npm
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash - && \
    apt-get install -y nodejs

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar o diretório de trabalho
WORKDIR /var/www

# Copiar arquivos do projeto para o diretório de trabalho
COPY . /var/www

# Dar permissão ao diretório
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# Habilitar o mod_rewrite do Apache
RUN a2enmod rewrite

# Copiar arquivo de configuração do Apache
COPY ./docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Instalar dependências do PHP
RUN composer install

# Instalar dependências do npm
RUN npm install

# Expôr a porta 80
EXPOSE 80

# Comando para iniciar o Apache
CMD ["apache2-foreground"]
