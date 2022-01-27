#!/bin/bash

git push origin main
ssh root@164.90.174.17 "cd /var/www/rest_api_prod; bash scripts/build.sh"
