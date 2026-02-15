#!/bin/bash

# === Feature Toggles ===
FEATURE_SETUP=true
FEATURE_COMPOSER_INSTALL=true
FEATURE_RUN_TESTS=false
FEATURE_GENERATE_DOCUMENTATION=true
FEATURE_BUILD_PROD=false
FEATURE_BUILD_IMAGE=false
# =======================

set -e

CONTAINER_NAME="php-build-container"
PROJECT_PATH="D:\\workspace-phpstorm\\webfiles-framework-php"

# --- Docker Netzwerk Setup ---
docker network create webfiles-net || true

# --- PHP Build Container starten ---
echo "Starting Docker container for build..."
if [ "$(docker ps -aq -f name=$CONTAINER_NAME)" ]; then
    if [ "$(docker ps -q -f name=$CONTAINER_NAME)" ]; then
        echo "Container $CONTAINER_NAME is already running."
    else
        echo "Starting existing container $CONTAINER_NAME..."
        docker start $CONTAINER_NAME
    fi
else
    echo "Creating new container $CONTAINER_NAME..."
    docker run -dit --name $CONTAINER_NAME --network webfiles-net -v "$PROJECT_PATH":/app php:8.2-cli bash
fi

# --- Feature: Setup ---
if [ "$FEATURE_SETUP" = true ]; then
    echo "Running setup in container..."
    docker exec $CONTAINER_NAME bash -c "
    if [ ! -f /root/.setup_done ]; then
        apt-get update &&
        apt-get install -y git libpng-dev libzip-dev unzip wget &&
        pecl install pcov &&
        docker-php-ext-enable pcov &&
        docker-php-ext-install gd exif mysqli zip &&
        php -r \"copy('https://getcomposer.org/installer', 'composer-setup.php');\" &&
        php composer-setup.php --install-dir=/usr/local/bin --filename=composer &&
        rm composer-setup.php &&
        touch /root/.setup_done
    else
        echo 'Setup already done. Skipping setup steps.'
    fi"
fi  

# --- Feature: Composer Install ---
if [ "$FEATURE_COMPOSER_INSTALL" = true ]; then
    echo "Running composer install/update in container..."
    docker exec $CONTAINER_NAME bash -c "
    composer --version &&
    cd /app &&
    export COMPOSER_PROCESS_TIMEOUT=900 &&
    composer update --with-all-dependencies &&
    composer install --optimize-autoloader
    "
fi


MYSQL_CONTAINER_NAME="webfiles-mysql"

# --- MySQL Container starten ---
echo "Starting MySQL container for build..."
if [ "$(docker ps -aq -f name=$MYSQL_CONTAINER_NAME)" ]; then
    if [ "$(docker ps -q -f name=$MYSQL_CONTAINER_NAME)" ]; then
        echo "MySQL container $MYSQL_CONTAINER_NAME is already running."
    else
        echo "Starting existing MySQL container $MYSQL_CONTAINER_NAME..."
        docker start $MYSQL_CONTAINER_NAME
    fi
else
    echo "Creating new MySQL container $MYSQL_CONTAINER_NAME..."
    docker run -dit \
        --name $MYSQL_CONTAINER_NAME \
        --network webfiles-net \
        -e MYSQL_ROOT_PASSWORD=root \
        -e MYSQL_DATABASE=webfiles-mysql \
        -e MYSQL_USER=webfiles \
        -e MYSQL_PASSWORD=webfiles \
        -p 3306:3306 \
        --restart unless-stopped \
        mysql:8.0
fi

# --- MySQL Readiness Check ---
echo "Waiting for MySQL to be ready..."
docker exec $MYSQL_CONTAINER_NAME bash -c '
for i in {1..30}; do
    if mysqladmin ping -h"localhost" -uroot -proot --silent; then
        echo "MySQL is up!"
        exit 0
    fi
    echo "Waiting for MySQL... ($i)"
    sleep 2
done
echo "MySQL did not become available in time."
exit 1
'

# --- Feature: Tests ausführen ---
if [ "$FEATURE_RUN_TESTS" = true ]; then
    echo "Running PHPUnit tests in Docker container..."
    docker exec $CONTAINER_NAME bash -c "\
        cd /app && \
        if [ -f vendor/bin/phpunit ]; then \
            vendor/bin/phpunit  --coverage-html build/coverage --coverage-clover build/logs/clover.xml --debug; \
        else \
            echo 'PHPUnit not found. Please ensure it is installed as a dev dependency.'; \
            exit 1; \
        fi"
fi

if [ "$FEATURE_GENERATE_DOCUMENTATION" = true ]; then
    echo "Generating API documentation in Docker container..."
    docker exec $CONTAINER_NAME bash -c "\
        cd /app && \
        rm -f apigen.phar && \
        wget https://github.com/ApiGen/ApiGen/releases/latest/download/apigen.phar && \
        php apigen.phar source --output ./gh-pages --workers 1 \

    "
fi


# --- Feature: Production Build ---
if [ "$FEATURE_BUILD_PROD" = true ]; then
    echo "Building PHP source code (production)..."
    docker exec $CONTAINER_NAME bash -c "cd /app && composer install --no-dev --optimize-autoloader"
fi


# --- Feature: Docker Image bauen ---
if [ "$FEATURE_BUILD_IMAGE" = true ]; then
    echo "Building Docker image..."
    docker build -t my-php-app .
    echo "Build complete. Docker image 'my-php-app' created."
fi

# Optional: Container entfernen
# docker rm -f $CONTAINER_NAME