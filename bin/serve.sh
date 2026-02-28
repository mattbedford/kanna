#!/bin/bash
php -d pcov.enabled=0 -S localhost:8080 -t public/
