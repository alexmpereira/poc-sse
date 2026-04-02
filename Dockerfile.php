FROM php:8.2-fpm-alpine

# Injeta configurações no PHP para garantir que nenhum buffer atrapalhe o SSE
RUN echo "output_buffering = Off" > /usr/local/etc/php/conf.d/sse-custom.ini && \
    echo "zlib.output_compression = Off" >> /usr/local/etc/php/conf.d/sse-custom.ini && \
    echo "implicit_flush = On" >> /usr/local/etc/php/conf.d/sse-custom.ini