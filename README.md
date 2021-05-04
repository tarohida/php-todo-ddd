# php-todo-ddd

## setup

```
$ docker-compose run --rm composer init
Creating php-todo-ddd_composer_run ... done


  Welcome to the Composer config generator



This command will guide you through creating your composer.json config.

Package name (<vendor>/<name>) [root/app]: tarohida/php-todo-app
Description []: php todo app
Author [, n to skip]: Taro Hida <sk8trou@gmail.com>
Minimum Stability []:
Package Type (e.g. library, project, metapackage, composer-plugin) []: project
License []: AGPL-3.0-or-later

Define your dependencies.

Would you like to define your dependencies (require) interactively [yes]? no
Would you like to define your dev dependencies (require-dev) interactively [yes]? no

{
    "name": "tarohida/php-todo-app",
    "description": "php todo app",
    "type": "project",
    "license": "AGPL-3.0-or-later",
    "authors": [
        {
            "name": "Taro Hida",
            "email": "sk8trou@gmail.com"
        }
    ],
    "require": {}
}

Do you confirm generation [yes]? yes
$ docker-compose run --rm composer require --dev phpunit/phpunit
Creating php-todo-ddd_composer_run ... done
Using version ^9.5 for phpunit/phpunit
./composer.json has been updated
Running composer update phpunit/phpunit
Loading composer repositories with package information
Updating dependencies
Lock file operations: 34 installs, 0 updates, 0 removals
  - Locking doctrine/instantiator (1.4.0)
  ...
  - Locking webmozart/assert (1.10.0)
Writing lock file
Installing dependencies from lock file (including require-dev)
Package operations: 34 installs, 0 updates, 0 removals
  - Downloading symfony/polyfill-ctype (v1.22.1)
  ...
  - Installing phpunit/phpunit (9.5.4): Extracting archive
6 package suggestions were added by new dependencies, use `composer suggest` to see details.
Generating autoload files
26 packages you are using are looking for funding.
Use the `composer fund` command to find out more!

$ docker-compose exec php vendor/bin/phpunit ./tests
PHPUnit 9.5.4 by Sebastian Bergmann and contributors.

..                                                                  2 / 2 (100%)

Time: 00:00.021, Memory: 4.00 MB

OK (2 tests, 2 assertions)
```

## add packages

```
docker-compose run --rm composer dump-autoload
docker-compose run --rm composer require slim/slim
docker-compose run --rm composer require monolog/monolog
docker-compose run --rm composer require robmorgan/phinx
docker-compose run --rm composer require eftec/bladeone
docker-compose run --rm composer require ext-pdo
docker-compose run --rm composer require slim/psr7
docker-compose run --rm composer require php-di/php-di 
```

## initialize Phinx

```
$ mkdir -p db/migrations
$ docker-compose exec php ./vendor/bin/phinx init
Phinx by CakePHP - https://phinx.org.

created /var/www/html/phinx.php
```