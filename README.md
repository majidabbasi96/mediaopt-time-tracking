# Time Tracking Application Prototype

## Introduction

In this project I developed some RESTful APIs with Laravel and Docker in order to handle following concepts for a time tracking application prototype:

- Tracking entring and leaving the office for login and logout
- Upload records as bulk
- Calculate billable hours for a project
- Calculate Peaktime for a prject in an specific day

- Authentication and authorization(Login and Register)
- Manage projects
- Mange worklogs


## Installation and Configuration

In order to run and install the project in your local machine, follow these instructions:

Step 1: Download and install [Docker Descktop](https://www.docker.com/products/docker-desktop) in your local machine based on your OS (If you do not have it).

Step 2: Clone the project from the Githup.

Step 3: Run `cp .env.example .env` command.
This commany will create .env file from  .env.example in your local machine in order to be able to project.

Step 4: Run the following command in order to install the composer packages without needing to install the the composer in your local machine:

    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v $(pwd):/opt \
        -w /opt \
        laravelsail/php80-composer:latest \
        composer install --ignore-platform-reqs

You can find refrence of this command on [Laravel Official Documentation](https://laravel.com/docs/8.x/sail#installing-composer-dependencies-for-existing-projects)

Step 5: Run the following Command:
    ./vendor/bin/sail up
This command will install the Laravel project dependencies based on `docker-compose.yml` file instructions in docker and run the app.

Step 6: Run the following command:
    ./vendor/bin/sail artisan key:generate
This command will generate a key for Laravel project.

Step 7: Run the following command:
    ./vendor/bin/sail artisan migrate
This command will run the database migrations on database instanceof docker.

Step 8: Run the following command command:
    ./vendor/bin/sail artisan db:seed --class=ProjectSeeder
This command will add some sample projects.

Step 9: Run the following command:
    ./vendor/bin/sail artisan db:seed --class=UserSeeder
This command will add some sample users.

Open browser and then go to the `http://localhost/` , you should be able to see the Laravel project runing successfully.


## Check the database on phpMyAdmin

In order to see the database you need to go to the `http://localhost:8080/` and then login on phpMyAdmin with `username: root` and `password: `


## Run Unit and Features Tests

In order to run unit and feaures tests please run following Sail commands:

    ./vendor/bin/sail artisan test

## RESTful APIs

### Worklog Login

This this API you can log starting work.

#### Request

`POST http://localhost/api/work-logs/login`

    {user_id: int, record_date: date, start_time: time}

    Sample Data:
    {user_id: 1, record_date: 2022-01-01, start_time: 10:10:10}

#### Response

    {
        "success": boolean,
        "data": {
            "user_id": int,
            "record_date": date,
            "start_time": time,
            "source": string,
            "project_id": int,
            "updated_at": datetime,
            "created_at": datetime,
            "id": int
        },
        "message": string
    }