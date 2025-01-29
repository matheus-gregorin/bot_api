#!/bin/bash

# Definir variáveis
CONF_DIR="/etc/supervisor/conf.d"
CONF_FILE="laravel-queue-worker.conf"
CONF_PATH="${CONF_DIR}/${CONF_FILE}"

# Verificar se o arquivo de configuração já existe
if [ ! -f "$CONF_PATH" ]; then
  echo "Arquivo de configuração não encontrado. Criando o arquivo ${CONF_FILE}..."
  
# Criar o arquivo de configuração com o conteúdo fornecido
cat <<EOF > "$CONF_PATH"
[program:laravel-queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan queue:work redis --verbose --tries=3
autostart=true
autorestart=true
numprocs=1
user=root
redirect_stderr=true
stdout_logfile=/dev/stdout
stderr_logfile=/dev/stderr
EOF

  echo "Arquivo de configuração ${CONF_FILE} criado com sucesso."
else
  echo "Arquivo de configuração ${CONF_FILE} já existe."
fi