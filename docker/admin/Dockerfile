FROM php:8.2-apache

# Enable mod_rewrite and set custom DocumentRoot
RUN a2enmod rewrite

# Set DocumentRoot to /var/www/admin/public
ENV APACHE_DOCUMENT_ROOT=/var/www/admin/public

# Update Apache config to use the new DocumentRoot and allow .htaccess overrides
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

WORKDIR /var/www/admin
