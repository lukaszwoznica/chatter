# :speech_balloon: Chatter

[![Live Demo](https://img.shields.io/website?down_message=offline&label=demo&up_message=online&url=https%3A%2F%2Fchatter-laravel.herokuapp.com)](#live-demo)
[![Current Version](https://img.shields.io/github/v/tag/lukaszwoznica/chatter?label=version)](https://github.com/lukaszwoznica/chatter/tags)
[![Tests Status](https://img.shields.io/github/workflow/status/lukaszwoznica/chatter/Laravel?label=tests&logo=github)](https://github.com/lukaszwoznica/chatter/actions/workflows/laravel.yml)

## Overview

A real-time chat application built with Laravel, Vue.js and Pusher Channels.

## Table of Contents

* [Main Technologies](#main-technologies)
* [Features](#features)
* [Installation](#installation)
    * [Via Laravel Sail](#via-laravel-sail)
    * [Via Local Composer](#via-local-composer)
* [Environment Configuration](#environment-configuration)
    * [Database](#database)
    * [Queue connection - Redis](#queue-connection---redis)
    * [Pusher Channels](#pusher-channels)
    * [File storage - AWS S3](#file-storage---aws-s3)
    * [Email service](#email-service)
    * [OAuth 2.0 providers - Google & Facebook](#oauth-20-providers---google--facebook)
    * [Google Maps Platform](#google-maps-platform)
* [Live Demo](#live-demo)

## Main Technologies

* Laravel 8
* Vue.js 3
* MySQL 8
* Sass (SCSS)
* Pusher Channels
* Laravel Echo
* Redis
* Laravel Horizon

## Features

* One-to-one chat
* User authentication:
    * Register and login
    * Reset forgotten password
    * Login with Google and Facebook accounts
* User profile:
    * Edit user profile
    * Profile pictures (avatars)
* Contacts:
    * User search
    * Real-time users online/offline status
* Chat:
    * Messages pagination with infinite scrolling
    * User typing indicator
    * Message read status
    * Chat sounds
    * Share user's current location
    * Emoji picker
* Responsive design

## Installation

### Via Laravel Sail

> Laravel Sail is a lightweight command-line interface for interacting with
> Laravel's default Docker development environment. Sail provides a great starting point for building a Laravel
> application using PHP, MySQL, and Redis.


Before you start, you need to install Docker on your machine to use Laravel Sail. To do this, follow the instructions in
the
[_official Laravel documentation_](https://laravel.com/docs/8.x/installation#your-first-laravel-project) for your
operating system. Once you've installed Docker, set up the project by following these steps.

1. Clone the repository and open the project directory.

```bash
git clone git@github.com:lukaszwoznica/chatter.git && cd chatter
```

2. Install application dependencies using a small Docker container.

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php80-composer:latest \
    composer install --ignore-platform-reqs
```

3. Run Laravel Sail containers.

```bash
vendor/bin/sail up -d
```

4. Create an environment configuration file and set required variables (see
   the [Environment Configuration section](#environment-configuration) for a description of how to configure your
   environment variables).

```bash
cp .env.example .env
```

5. Generate laravel application key.

```bash
vendor/bin/sail artisan key:generate
```

6. Install Node.js dependencies and compile frontend assets.

```bash
vendor/bin/sail npm install
```

7. Run database migrations and populate it with some dummy data.

```bash
vendor/bin/sail artisan migrate:fresh --seed
```

8. Run Laravel Horizon to start processing queued jobs.

```bash
vendor/bin/sail artisan horizon
```

9. Additionally, you can run PHPUnit tests to make sure everything is working fine.

```bash
vendor/bin/sail test
```

10. At this point, the application should be ready to go at http://localhost/.

### Via Local Composer

If you don't want to use Laravel Sail, you can install the application without using Docker containers. In this case,
you must have PHP, Composer, Node.js and PhpRedis extension installed locally on your computer. You also need to take
care of the database (it does not have to be MySQL, but it has to
be [supported by Laravel](https://laravel.com/docs/8.x/database#introduction)), and the Redis database for queues
(optional - more information in the [Environment Configuration section](#environment-configuration)). If you meet the
above requirements, you can proceed to the application installation process, which is slightly different from the one
described earlier.

1. Clone the repository and open the project directory.

```bash
git clone git@github.com:lukaszwoznica/chatter.git && cd chatter
```

2. Install Composer dependencies.

```bash
composer install
```

3. The next steps are the same as steps 3-9 when installing with Laravel Sail. The only difference is that you don't
   need to use the `vendor/bin/sail` script in your commands.

```bash
cp .env.example .env # Set environment variables at this stage 
php artisan key:generate
npm install
php artisan migrate:fresh --seed
php artisan horizon
php artisan test
```   

4. Start Laravel's local development server.

```bash
php artisan serve
```

5. At this point, the application should be ready to go at http://localhost:8000/.

## Environment Configuration

This section describes how to configure environment variables in the .env file.

### Database

If you are using Laravel Sail, you don't need to change anything in the database configuration. Otherwise, you need to
set up the database connection. The default database configuration is as follows.

```
DB_CONNECTION=mysql # Database driver
DB_HOST=mysql 
DB_PORT=3306
DB_DATABASE=chatter
DB_USERNAME=root
DB_PASSWORD=null
```

### Queue connection - Redis

By default, Redis is set as the queue driver. If you are using Laravel Sail, you don't have to configure anything.
Otherwise, you need to set up your Redis database connection. Alternatively, you can set the queue driver to `sync`.
When using this driver, queued jobs will be executed immediately (synchronously) within the current process.

```
QUEUE_CONNECTION=redis # Queue driver

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Pusher Channels

Another service that needs to be configured is Pusher. It allows you to broadcast events through the WebSocket
connection so that the application works in real
time. [Sign up on the Pusher website](https://dashboard.pusher.com/accounts/sign_up) and create a new Channels
application.
**Remember to turn on the client events in app settings**. Then fill in the following variables with the credentials of
the newly created application (App Keys tab in Pusher dashboard).

```
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=
```

Chatter uses Pusher Channels webhooks to determine user activity status. Therefore, you need to enable them in the
Channels' app dashboard. To do this, select the Webhook tab and add a new channel existence webhook. However, you will
need the public URL for Chatter for this. To obtain it in a local development environment, you can use tunnel
applications such as [ngrok](https://ngrok.com/) or [Expose](https://expose.dev/). Then set the webhook URL as follows.

```
https://your-public-url/webhooks/pusher
```

### File storage - AWS S3

By default, this app uses Amazon S3 to store uploaded files. If you also want to use it, you need
to [register an AWS account](https://aws.amazon.com/) and create a new bucket in the S3 service. Then set the
appropriate environment variables in the .env file. To learn how to create an S3 bucket, see
the [official AWS S3 documentation](https://docs.aws.amazon.com/AmazonS3/latest/userguide/GetStartedWithS3.html).

```
MEDIA_DISK=s3 # Disk for storing uploaded files

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=
AWS_BUCKET=
```

If you don't want to use AWS S3 you can use the `public` disk and store files locally. To make uploaded files accessible
from the web, you should create a symbolic link from `public/storage` to `storage/app/public`. You can do this with the
command below.

```bash
vendor/bin/sail artisan storage:link
php artisan storage:link # if not using Laravel Sail
```

### Email Service

The application has a functionality related to sending e-mails containing a link to reset the user's password. Laravel
Sail has a configured MailHog service. MailHog intercepts emails sent by application during local development and
provides a convenient web interface so that you can preview email messages in your browser. You may access the MailHog
web interface at http://localhost:8025.

However, if you want emails to be sent normally, you need to configure some mailing service. You can use API-based
drivers such as Mailgun, Postmark, Amazon SES or any SMTP server. You must then modify the following environment
variables accordingly.

```
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=chatter@chatter.xyz
MAIL_FROM_NAME="${APP_NAME}"
```

### OAuth 2.0 providers - Google & Facebook

Chatter allows you to authenticate with OAuth providers such as Google and Facebook. For this functionality to work
properly, you need to create [Google](https://developers.google.com/identity/protocols/oauth2/web-server#enable-apis)
and [Facebook](https://developers.facebook.com/docs/facebook-login/web/) OAuth 2.0 credentials and put them as the
values of the following variables.

```
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=

FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
```

### Google Maps Platform

The last service that needs to be configured for the application to work properly is Google Maps API. You need to create
an API key according to the [instructions here](https://developers.google.com/maps/documentation/javascript/get-api-key)
and then set it as the value of the environment variable below.

```
MIX_GOOGLE_API_KEY=
```

## Live demo

A working live demo of Chatter is available here: https://chatter-laravel.herokuapp.com

### :information_source: Notes

* In this demo, the database is automatically refreshed every two hours.

* You can create a new user account or log in using the following credentials:

| Email | Password |
|---|---|
| user@chatter.xyz | ChatterPass123 |
