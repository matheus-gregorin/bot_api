#!/bin/bash

# Atualizar pacotes e instalar dependências básicas
sudo apt update -y
sudo apt upgrade -y
sudo apt-get install -y \
    ca-certificates \
    curl \
    gnupg \
    vim \
    lsb-release \
    apt-transport-https \ 
    software-properties-common

# Instalar PHP 8.1
echo "Instalando PHP 8.1 e extensoes..."
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update -y
sudo apt install -y php8.1 php8.1-cli php8.1-fpm php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip php8.1-mysql php8.1-intl php8.1-gd php8.1-bcmath

# Instalar extensões adicionais do PHP conforme necessario
sudo apt install -y php8.1-soap php8.1-xmlrpc php8.1-opcache php8.1-readline php8.1-json

# Instalar Composer
echo "Instalando Composer..."
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Instalar Docker
echo "Instalando Docker..."
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
sudo apt update -y
sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

sudo usermod -aG docker $USER

# Instalar Docker Compose
echo "Instalando Docker Compose..."
sudo apt install -y docker-compose-plugin

echo "Instalação completa!"

