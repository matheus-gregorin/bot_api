#!/bin/bash

# Definir variáveis
CONF_DIR="/etc/supervisor/conf.d"
CONF_FILE="laravel-rabbitmq-worker.conf"
CONF_PATH="${CONF_DIR}/${CONF_FILE}"

# Verificar e matar processos nas portas 5672 e 9002
for PORT in 5672 9002; do
  PID=$(lsof -ti :$PORT)
  if [ -n "$PID" ]; then
    echo "Matando processos na porta $PORT (PID: $PID)..."
    kill -9 $PID
  else
    echo "Nenhum processo encontrado na porta $PORT."
  fi
done

# Verificar se o arquivo de configuração já existe
if [ ! -f "$CONF_PATH" ]; then
  echo "Arquivo de configuração não encontrado. Criando o arquivo ${CONF_FILE}..."
  
# Criar o arquivo de configuração com o conteúdo fornecido
cat <<EOF > "$CONF_PATH"
[program:laravel-rabbitmq-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan start:listener
autostart=true
autorestart=true
numprocs=1
user=root
redirect_stderr=false
# stdout_logfile=/var/www/html/listener-rabbit.log
# stderr_logfile=/var/www/html/listener-rabbit.log
EOF

  echo "Arquivo de configuração ${CONF_FILE} criado com sucesso."
else
  echo "Arquivo de configuração ${CONF_FILE} já existe."
fi

# Executar comandos do Supervisor
echo "Atualizando e reiniciando o Supervisor..."

# Executar comandos do Supervisor e capturar a saída
echo "Executando supervisorctl reread..."
READOUT=$(supervisorctl reread 2>&1)
echo "$READOUT"

echo "Executando supervisorctl update..."
UPDATEOUT=$(supervisorctl update 2>&1)
echo "$UPDATEOUT"

# Iniciar o supervisord em segundo plano
supervisord -c /etc/supervisor/supervisord.conf &
echo "Supervisor executado"
sleep 3

# Inicializando o Worker em background para escutar o serviço de mensageria RabbitMq
echo "Executando supervisorctl start all..."
STARTOUT=$(supervisorctl start all 2>&1)
echo "$STARTOUT"
sleep 3

echo "Processos do Supervisor atualizados e reiniciados com sucesso."