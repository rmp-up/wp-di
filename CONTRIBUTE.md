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

First Symlink configs to root dir for easier development.
We do not want to pollute the root so they are under "etc/".

```
for L in etc/*; do ln -s $L; done
```

Then build up WordPress:

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
