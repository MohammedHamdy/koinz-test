# Koinz Test

## Installation

First clone this repository, install the dependencies, and setup your .env file.

```
git clone https://github.com/MohammedHamdy/koinz-test.git
cd koinz-test
composer install
cp .env.dev .env
```

Then create the necessary database.

```
create database koinz
```

And run the initial migrations and seeders.

```
php artisan migrate
php artisan db:seed
```

After run the project.

```
php artisan serve
```

