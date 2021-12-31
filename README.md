## Installation and Configuration

In order to run and install the project in your local machine, follow these instructions:

- Download and install [Docker Descktop](https://www.docker.com/products/docker-desktop) in your local machine based on your OS.

- Close the project from the gitup

- Run "cp .env.example .env" command

- Run the following command in order to install the composer packages without needing to install the the composer in your local

docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/opt \
    -w /opt \
    laravelsail/php80-composer:latest \
    composer install --ignore-platform-reqs

- Run "./vendor/bin/sail up" Command

- Run "./vendor/bin/sail artisan key:generate" command

- Run "./vendor/bin/sail artisan migrate"

- Run "./vendor/bin/sail artisan db:seed --class=ProjectSeeder" Command

- Run "./vendor/bin/sail artisan db:seed --class=UserSeeder" Command

Open browser and then go to the http://localhost/ , you should be able to see the Laravel project runing successfully.
