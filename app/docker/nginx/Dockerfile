FROM debian:jessie-slim
MAINTAINER Matthieu YK <matthieuy@riseup.net>

# Install nginx
RUN apt-get update && apt-get install -y --no-install-recommends \
        nginx \
    && rm -rf /var/lib/apt/lists/*

# Configure nginx
ADD nginx.conf /etc/nginx/
ADD battleship.conf /etc/nginx/sites-available/
RUN ln -s /etc/nginx/sites-available/battleship.conf /etc/nginx/sites-enabled/battleship
RUN rm /etc/nginx/sites-enabled/default
RUN echo "upstream php-upstream { server php:9000; }" > /etc/nginx/conf.d/upstream.conf

# Other
RUN usermod -u 1000 www-data
CMD ["nginx"]
EXPOSE 80
