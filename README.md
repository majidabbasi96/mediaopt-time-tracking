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

### Worklog Logout

This this API you can log leaving work.

#### Request

`POST http://localhost/api/work-logs/logout`

    {user_id: int, end_time: date}

    Sample Data:
    {user_id: 1, end_time: 11:11:11}

#### Response

    {
        "success": boolean,
        "data": {
            "id": int,
            "user_id": int,
            "project_id": int,
            "source": string,
            "record_date": date,
            "start_time": time,
            "end_time": time,
            "duration_in_minute": float,
            "updated_at": datetime,
            "created_at": datetime
        },
        "message": string
    }

### Worklog Bulk Upload

With this API you can upload your reports as bulk.

#### Request

`POST http://localhost/api/work-logs/bulk-upload`

    {file: File}

Note: the columns in CSV file should be like this:

    Column 1: user_id
    Column 2: project_id
    Column 3: record_date
    Column 4: start_time
    Column 5: end_time

#### Response

    {
        "success": boolean,
        "data": [],
        "message": string
    }

### Report Project Billable Hours

With this API you can get the hours that the accountante system should bill for a project.

#### Request

`POST http://localhost/api/reports/projects/billable-hours`

    {project_id: int}

#### Response

    {
        "success": boolean,
        "data": {
            "total_minutes": float,
            "total_hours": string
        },
        "message": string
    }

### Report Peak Time Of Project

With this API you can get peack time of a project in a specific date which most of the teammates worked.

#### Request

`POST http://localhost/api/reports/projects/getpeak-time`

    {project_id: int, record_date: date}

#### Response

    {
        "success": boolean,
        "data": {
            "overlap_time": time,
            "record_count": int
        },
        "message": string
    }

### Admin APIs

#### Authentication and authorization

Register: `POST http://localhost/api/register`

    {name: string, email: string, password: string, confirm_password: string}

Login: `POST http://localhost/api/login`

    {email: string, password: string}

These two APIs will return the following result and you need to send the token as Bearer Token in the Authentication for next manage APIS.

    {
        "success": boolean.
        "data": {
            "token": string,
            "name": string
        },
        "message": string
    }

#### Manage Worklogs

Get List: `GET http://localhost/api/admin/work-logs`

Add New: `POST http://localhost/api/admin/work-logs`

Get Details: `GET http://localhost/api/admin/work-logs/{id}`

Update: `PUT http://localhost/api/admin/work-logs/{id}`

DELETE: `DELETE http://localhost/api/admin/work-logs/{id}`

#### Manage Projects

Get List: `GET http://localhost/api/admin/projects`

Add New: `POST http://localhost/api/admin/projects`

Get Details: `GET http://localhost/api/admin/projects/{id}`

Update: `PUT http://localhost/api/admin/projects/{id}`

DELETE: `DELETE http://localhost/api/admin/projects/{id}`


## Project Important Files and Folders


API Routes: `routes/api.php`

API Controllers: `app/Http/Controllers/API/*.*`

Models: `app/Models/*.*`

CSV Importer Model: `app/Imports/ImportWorklog.php`

Helper Functions: `app/helpers.php`

Database Migrations: `database/migrations/*.*`

Database Seeders: `database/seeders/*.*`

Unit Tests: `tests/Unit/*.*`

Feature Tests: `tests/Feautre/*.*`

Docker Compose: `docker-compose.yml`

README: `README.md`