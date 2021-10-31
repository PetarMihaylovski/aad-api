## Setup Guide
1) I would assume you have php, composer and laravel installed, im sorry.
2) ```npm install``` from the root to install dependencies
3) ```composer install``` from the root to install php dependencies
4) spin up the docker compose file


## Spin the docker-compose file

0) make sure you have nothing running on port 80 and port 3306.
1) make sure mysql server or apache are not running on your machine as well
2) ```vendor/bin/sail``` is the laravel's docker abstraction. you can execute any type of laravel or docker commands for the running container using this command
3) to spin the container up use ```vendor/bin/sail up -d```. this will run the docker-compose file, will init the mysql volume, spin the mysql container and finally spin the api container
4) ```vendor/bin/sail artisan storage:link``` creates a storage link, so you can successfully fetch images from the storage directory 
5) call ```http://localhost/api/shops``` to verify everything is running properly.
6) in case of any errors when the container is already spinning, but you still cannot access endpoints clear the cache with the following commands
* ```vendor/bin/sail artisan route:clear ```
*  ```vendor/bin/sail artisan config:clear ``` 
* ```vendor/bin/sail artisan cache:clear ```
