.PHONY: up cc rebuild down stop logs ssh 'run tests'

up:
	docker compose up -d
	docker compose exec frankenphp composer install
	docker compose exec frankenphp php bin/console doctrine:migrations:migrate --no-interaction
	docker compose exec frankenphp php bin/console cache:clear

cc:
	docker compose exec frankenphp php bin/console cache:clear

rebuild:
	docker compose up -d --build
	docker compose exec frankenphp composer install
	docker compose exec frankenphp php bin/console doctrine:migrations:migrate --no-interaction
	docker compose exec frankenphp php bin/console cache:clear

down:
	docker compose down

stop:
	docker compose stop

logs:
	docker compose logs -f

ssh:
	docker compose exec frankenphp bash

run tests:
	docker compose exec frankenphp env APP_ENV=test php bin/phpunit tests/
