build: ## build development environment
	cp .env.example .env
	docker-compose build
js:
	docker-compose run --rm php npx mix
php:
	docker-compose run --rm php composer install
	docker-compose run --rm php npm install
serve:
	docker-compose up -d
stop:
	docker-compose stop
down:
	docker-compose down -v