FROM nginx

COPY nginx.conf /etc/nginx/
RUN rm -f /etc/nginx/conf.d/*


COPY php.conf /etc/nginx/conf.d/

VOLUME ["/var/log/nginx", "/var/www/html"]

EXPOSE 80 443