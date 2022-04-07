build:
	echo "build docker envirnoment"
	docker-compose build
	echo "serve application"
	docker-compose up

tests:
	cd public;./vendor/bin/phpunit tests

enter-app:
	@docker exec -it php_wunder_mobility sh