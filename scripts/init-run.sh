#!/bin/bash

systemctl status supervisor
systemctl enable supervisor

for PORT in 9001; do
  PID=$(lsof -ti :$PORT)
  if [ -n "$PID" ]; then
    echo "Matando processos na porta $PORT (PID: $PID)..."
    kill -9 $PID
  else
    echo "Nenhum processo encontrado na porta $PORT."
  fi
done

supervisorctl reread
supervisorctl update

systemctl restart supervisor

# Iniciar o supervisord em segundo plano
#supervisord -c /etc/supervisor/supervisord.conf &

# Iniciar o servidor Laravel
php artisan serve --host=0.0.0.0 --port=8000