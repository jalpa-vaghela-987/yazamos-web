#!/bin/sh

# Run Laravel commands at container startup
#php artisan migrate --force --no-interaction
#php artisan passport:install --no-interaction
#php artisan key:generate --no-interaction
#php artisan optimize:clear --no-interaction
php artisan storage:link --no-interaction
#php artisan queue:restart --no-interaction

# Ensure permissions are correct
touch /var/www/html/storage/logs/laravel.log 
chmod 777 -R /var/www/html/storage/
chmod 777 -R  /var/www/html/public/storage/
chmod 777 -R /var/www/html/storage/logs/
chown -R www-data:www-data /var/www/
chown -R www-data:www-data /var/www/html/storage/logs/

#/usr/bin/supervisord -c /etc/supervisor/supervisord.conf
# Start querying queue in backround
#while true; do php artisan queue:work --timeout=0 --tries=3 --daemon > /dev/null 2>&1; done &

# Start Apache in the foreground
a2enmod rewrite
apache2-foreground
