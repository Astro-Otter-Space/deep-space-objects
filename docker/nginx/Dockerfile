FROM debian:jessie
ARG NGINX_HOST

MAINTAINER HamHamFonFon <balistik.fonfon@gmail.com>

# Install nginx
RUN apt-get update && apt-get install -y nginx

# Configure Nginx
ADD nginx.conf /etc/nginx/

ADD symfony.conf /etc/nginx/sites-available/
#RUN envsubst "${NGINX_HOST}" < /etc/nginx/sites-available/default.template > /etc/nginx/sites-available/symfony.conf && nginx -g 'daemon off;'
RUN sed "/server_name nginx_host;/c\    server_name ${NGINX_HOST};" -i /etc/nginx/sites-available/symfony.conf
RUN echo "upstream php-upstream { server php:9000; }" > /etc/nginx/conf.d/upstream.conf
RUN usermod -u 1000 www-data

# Configure the virtual host
RUN ln -s /etc/nginx/sites-available/symfony.conf /etc/nginx/sites-enabled/symfony
RUN rm /etc/nginx/sites-enabled/default


# Install OpenSSL
RUN apt-get update \
  && apt-get install -y --no-install-recommends openssl

# Add certificate SSL

# Run Nginx
CMD ["nginx"]

# Expose ports
EXPOSE 80
EXPOSE 443

# generate certificate
#RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/nginx/ssl/localhost.key -out /etc/nginx/ssl/localhost.crt -subj "/C=FR/ST=Montpellier/L=Montpellier/O=Global Security/OU=IT Department/CN=example.com"

#
#RUN openssl req -x509 -out localhost.crt -keyout localhost.key \
#      -newkey rsa:2048 -nodes -days 365 -sha256 \
#      -subj '/CN=localhost'
