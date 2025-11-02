#piGen.phar

CONTAINER_NAME="php-build-container"
PROJECT_PATH="D:\\workspace-phpstorm\\webfiles-framework-php"



docker run php-build-container --network webfiles-net -v "$PROJECT_PATH":/app php:8.2-cli bash -c "
cd /app &&
rm -f apigen.phar &&
wget http://www.apigen.org/apigen.phar &&
php apigen.phar generate -s source -d ./gh-pages
"