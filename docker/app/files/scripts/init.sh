#!/bin/bash

# TODO

cd /var/www/symfony/app
./console doctrine:migrations:migrate
