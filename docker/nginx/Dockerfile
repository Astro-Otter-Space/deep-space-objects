FROM nginx:latest

ARG NGINX_HOST
ARG UID

RUN echo "NginxHost: ${NGINX_HOST}"
RUN echo "Uid: ${UID}"

MAINTAINER HamHamFonFon <balistik.fonfon@gmail.com>

# Instal certbot for SSL
#RUN apt-get install certbot python-certbot-nginx -t stretch-backports

# Configure Nginx
RUN [ -d /etc/nginx/sites-enabled ] || mkdir /etc/nginx/sites-enabled
COPY ./nginx.conf /etc/nginx/
COPY ./symfony.conf /etc/nginx/sites-available/
RUN sed "/server_name nginx_host;/c\    server_name ${NGINX_HOST};" -i /etc/nginx/sites-available/symfony.conf
RUN echo "upstream php-upstream { server php:9000; }" > /etc/nginx/conf.d/upstream.conf
# Configure the virtual host
RUN ln -s /etc/nginx/sites-available/symfony.conf /etc/nginx/sites-enabled/
RUN #rm /etc/nginx/sites-enabled/default

# Add certificate SSL
#RUN certbot --nginx certonly
RUN usermod -u ${UID} www-data

# Expose ports
EXPOSE 80
EXPOSE 443

# Run Nginx
CMD ["nginx", "-g", "daemon off;"]

# generate certificate
#RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/nginx/ssl/localhost.key -out /etc/nginx/ssl/localhost.crt -subj "/C=FR/ST=Montpellier/L=Montpellier/O=Global Security/OU=IT Department/CN=example.com"

#
#RUN openssl req -x509 -out localhost.crt -keyout localhost.key \
#      -newkey rsa:2048 -nodes -days 365 -sha256 \
#      -subj '/CN=localhost'


