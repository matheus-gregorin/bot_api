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

# Define diretório de configuração do Supervisor
ENV CONF_DIR="/etc/supervisor/conf.d"

# Cria o diretório de configuração do Supervisor
RUN mkdir -p "$CONF_DIR"

# Adiciona o arquivo de configuração do Supervisor para o Laravel RabbitMQ Worker
RUN echo "[program:laravel-rabbitmq-worker] \n\
process_name=%(program_name)s_%(process_num)02d \n\
command=php /var/www/html/artisan start:listener \n\
autostart=true \n\
autorestart=true \n\
numprocs=1 \n\
user=root \n\
redirect_stderr=true" > "$CONF_DIR/laravel-rabbitmq-worker.conf"

# Adiciona o arquivo de configuração do Supervisor para o Laravel Queue Worker
RUN echo "[program:laravel-queue-worker] \n\
process_name=%(program_name)s_%(process_num)02d \n\
command=php /var/www/html/artisan queue:work redis --verbose --tries=3 \n\
autostart=true \n\
autorestart=true \n\
numprocs=1 \n\
user=root \n\
redirect_stderr=true \n\
stdout_logfile=/dev/stdout \n\
stderr_logfile=/dev/stderr" > "$CONF_DIR/laravel-queue-worker.conf"

# Rodando o composer install
RUN composer install

#Generate key
RUN php artisan key:generate

# Adiciona script de inicialização para configurar e iniciar os serviços
RUN echo "#!/bin/bash \n\
# Buscando status do Supervisor \n\
systemctl status supervisor \n\
# Habilitando o Supervisor \n\
systemctl enable supervisor \n\
# Verificando se há algo rodando na porta 9001 e matando se necessário \n\
for PORT in 9001; do \n\
  PID=\$(lsof -ti :\$PORT) \n\
  if [ -n \"\$PID\" ]; then \n\
    echo \"Matando processos na porta \$PORT (PID: \$PID)...\" \n\
    kill -9 \$PID \n\
  else \n\
    echo \"Nenhum processo encontrado na porta \$PORT.\" \n\
  fi \n\
done \n\
# Recarrega as configurações do Supervisor \n\
supervisorctl reread \n\
supervisorctl update \n\
# Reinicia o serviço do Supervisor \n\
systemctl restart supervisor \n\
# Inicia o Laravel \n\
php artisan serve --host=0.0.0.0 --port=8000" > /usr/local/bin/start-container.sh

# Dá permissão de execução ao script
RUN chmod +x /usr/local/bin/start-container.sh

# Define o comando de inicialização do container
CMD ["/usr/local/bin/start-container.sh"]
