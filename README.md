# Simple Hash Service based on Laravel

## Installation & Testing

```bash
git clone git@github.com:cy6erninja/hash-service-php.git
cd hash-service-php
composer setup
php artisan test --testsuite=Feature
php artisan serve

curl --location 'http://127.0.0.1:8000/hash' \
--header 'Content-Type: application/json' \
--data '{
    "data": "hello"
}'

curl --location 'http://127.0.0.1:8000/hash/aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d'
```
