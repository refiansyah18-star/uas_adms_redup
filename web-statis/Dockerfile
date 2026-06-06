FROM nginx:alpine

# Copy web files
COPY . /usr/share/nginx/html

# Copy custom Nginx configuration for Reverse Proxy
COPY default.conf /etc/nginx/conf.d/default.conf

EXPOSE 80