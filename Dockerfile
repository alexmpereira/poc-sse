# Usa a imagem oficial do PHP 8.2 com Apache
FROM php:8.2-apache

# Habilita o mod_headers do Apache (recomendado para manipulação de cache e conexões)
RUN a2enmod headers

# Copia os arquivos da pasta src para o diretório padrão do Apache
COPY src/ /var/www/html/

# Expõe a porta 80 do container
EXPOSE 80