Ve hasta la carpeta backend desde la terminal y escribe:

composer install -n --prefer-dist
cp .env.ci .env
php artisan key:generate

Después pon:

php artisan serve

Si todo va bien, configura el frontend poniendo:

npm install
npm run dev



