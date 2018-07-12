#!/bin/bash
composer install
php bin/console doctrine:migrations:migrate