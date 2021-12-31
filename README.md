## Installation and Configuration

In order to run and install the project in your local machine, follow these instructions:

- Download and install [Docker Descktop](https://www.docker.com/products/docker-desktop) in your local machine based on your OS.
- Close the project from the gitup
- Go to the project folder and run "./vendor/bin/sail composer install" command in terminal
- Run "cp .env.example .env" command
- Run "./vendor/bin/sail artisan key:generate" command
- Run "./vendor/bin/sail artisan migrate"
- Run "./vendor/bin/sail artisan db:seed --class=ProjectSeeder" Command
- Run "./vendor/bin/sail artisan db:seed --class=UserSeeder" Command
- Run "./vendor/bin/sail up" Command

Open browser and then go to the http://localhost/ , you should be able to see the Laravel project runing successfully.
