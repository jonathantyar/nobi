# Nobi Investment - API

This is an test aplication for recruitment to NOBI. This application implemented Nobi Investment System.

## Installation

This project follow the same install requirement from laravel can be found here [laravel](https://laravel.com/docs/8.x/installation).

### Install

```bash
# made sure composer already installed
composer install

# prepare the database credentials
cp .env.example .env

# generate key and doing migration
php artisan key:generate
php artisan migrate

# run test to made sure everything working on
php artisan test
```

### Requirement docs

For postman collection and mysql dump are prepared in these [directory](https://github.com/jonathantyar/nobi/blob/main/docs)

```bash
# this will generate the pre request data from dump
php artisan db:seed
```

### Prepare the pre request data

To install the app it can be done either by using mysql dump or using db:seed from laravel.

```bash
# this will generate the pre request data from dump
php artisan db:seed
```

### API Documentation

This project using scribe to generate the docs and postman collection. For postman collection it can be found in these [file](https://github.com/jonathantyar/nobi/blob/main/public/docs/collection.json). It's contains the example of success responses.

```bash
# http://127.0.0.1:8000/docs endpoint for full documentation from scribe

php artisan serve
```
