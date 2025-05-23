# Use a imagem PHP CLI 8.3
FROM php:8.3-cli

# Sinalizando para usar o usuário root
USER root

# Instale dependências do sistema necessárias
RUN apt-get update && apt-get install -y \
    systemctl \
    lsof \
    libssl-dev \
    pkg-config \
    unzip \
    libcurl4-openssl-dev \
    supervisor \
    vim

# Instalando a extensão sockets do php
RUN docker-php-ext-install sockets

# Instale a extensão do MongoDB com suporte a SSL
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Instalar a extensão phpredis
RUN pecl install redis \
    && docker-php-ext-enable redis

# Instale o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Adicionar as linhas de configuração no arquivo supervisord.conf para habilitar o servidor dele na porta 9001
RUN echo "\n[inet_http_server]\nport=*:9001\nusername=bottSup@gmail.com\npassword=bottSup123456\n" >> /etc/supervisor/supervisord.conf

# Defina o diretório de trabalho como o diretório raiz do aplicativo
WORKDIR /var/www/html

# Copie o arquivo composer.lock e o arquivo composer.json para o contêiner
COPY composer.lock composer.json /

# Copie o restante dos arquivos do aplicativo para o contêiner
COPY . /var/www/html

# Exponha a porta 8000 para acessar o servidor Laravel
EXPOSE 8000

# Tornar o script executável
RUN chmod +x /var/www/html/scripts/init-listener-rabbit.sh

# Executar o script durante o processo de construção
RUN /var/www/html/scripts/init-listener-rabbit.sh

# Tornar o script executável
RUN chmod +x /var/www/html/scripts/init-queue.sh

# Executar o script durante o processo de construção
RUN /var/www/html/scripts/init-queue.sh

# Rodando o composer install
RUN composer install

RUN php artisan key:generate

# Comando para iniciar o servidor Laravel
CMD ["/var/www/html/scripts/init-run.sh"]