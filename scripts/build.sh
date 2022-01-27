#!/bin/bash

git pull origin main

composer install
php bin/console cache:clear
php bin/console cache:warmup
