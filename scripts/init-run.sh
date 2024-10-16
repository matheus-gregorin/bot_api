#!/bin/bash

# Iniciar o supervisord em segundo plano
supervisord -c /etc/supervisor/supervisord.conf &

# Iniciar o servidor Laravel
php artisan serve --host=0.0.0.0 --port=8000