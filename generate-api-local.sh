#piGen.phar

rm apigen.phar 
wget http://www.apigen.org/apigen.phar

# Generate Api
php apigen.phar generate -s source -d ../gh-pages
