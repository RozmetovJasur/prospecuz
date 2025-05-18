docker-up: memory
	docker-compose up -d

docker-down:
	docker-compose down

docker-build: memory
	docker-compose up --build -d

test:
	docker-compose exec php-cli vendor/bin/phpunit

memory:
	sudo sysctl -w vm.max_map_count=262144

perm:
	sudo chgrp -R www-data storage bootstrap/cache
	sudo chmod -R ug+rwx storage bootstrap/cache

# sudo docker exec -it banksmart-db psql -U admin -p 5432 -W smartbank_web
# sudo docker-compose exec banksmart-app php artisan db:seed

# psql -h smartmarket-db -U admin smartmarket < smartbazar_15_08_2023.sql
