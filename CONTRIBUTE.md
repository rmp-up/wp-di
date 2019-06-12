# Contribution



## Use the container if you like

```bash
cp .env.dist .env # if not already done
docker-compose down # just to be sure
docker-compose up -d
docker-compose exec php71 bash
```

Hint: Now there is a ".docker" container containing the database files.

## Setup dev environment

Inside the container or on your dev-host:

```bash
composer install
vendor/bin/wp --allow-root core download
vendor/bin/wp --allow-root config create
vendor/bin/wp --allow-root core install --skip-email
vendor/bin/wp --allow-root core update --minor
```

## Testing

```bash
vendor/bin/phpunit
```