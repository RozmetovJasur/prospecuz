FROM php:8.2-fpm

# Скачиваем скрипт установки PHP расширений
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Обновляем пакеты и устанавливаем зависимости
RUN apt-get update --fix-missing && \
    apt-get install -y git postgresql-client unzip && \
    apt-get install -y netcat-traditional nano supervisor && \
    apt-get install -y python3 python3-pip python3-setuptools python3-dev && \
    chmod +x /usr/local/bin/install-php-extensions && \
    sync && \
    install-php-extensions gd zip soap pdo_pgsql redis pcntl && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка Node.js 18.x и npm
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Копируем Composer из официального образа
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Устанавливаем рабочую директорию
WORKDIR /var/www/default

# Команда для запуска PHP-FPM
CMD ["php-fpm"]
