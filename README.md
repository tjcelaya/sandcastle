# Laravel 5.4 project template

Laravel 5.4, Dingo API, JWT Auth, some sweet Dockerfiles. Rapid testing = rapid development.

Docker automation depends on the `$SC_ROOT_DIR` environment variable. Usage is primarily restricted to `docker-compose.yml` and can be tweaked from `.env`

## Overview

### Terms

Define the interesting nouns in the system here. If you don't people will see that you don't know what the names of things are and will be scared of reading your code since you can't decide on what to call stuff.

# Developing

## db setup

 - `make db-setup` will erase and recreate the test_db service from scratch, and then generate a bootstrap.sql
 - `make db-reset` will erase the test_db and loads bootstrap.sql

Both will leave the database in the the same state, but the second is much faster

