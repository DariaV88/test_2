Команды, которые необходимо ввести после скачивания репозитория:
docker compose up -d
docker exec -it laravel_app composer install
docker exec -it laravel_app cp .env.example .env
docker exec -it laravel_app chown -R www-data:www-data storage bootstrap/cache
docker exec -it laravel_app php artisan migrate
