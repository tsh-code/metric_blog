init:
		test -f .env || cp .env.dist .env
		test -f docker-compose.yaml || cp docker-compose.yaml.dist docker-compose.yaml
		docker-compose up -d
		docker-compose run --rm php-fpm composer install
		docker-compose run --rm php-fpm bin/console doctrine:database:drop --force --if-exists
		docker-compose run --rm php-fpm bin/console doctrine:database:create
		docker-compose run --rm php-fpm php bin/console doctrine:m:m --no-interaction
		docker-compose run --rm php-fpm bin/console doctrine:schema:update --force
		
start:
		docker-compose up -d
		docker-compose run --rm php-fpm composer install

bash:
		docker exec -it metric_art_php-fpm_1 bash
