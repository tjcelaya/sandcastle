DC := docker-compose
DCR := docker-compose run --rm --no-deps
SHELL := /bin/bash 

.PHONY: list
list:
	@$(MAKE) -pRrq -f $(lastword $(MAKEFILE_LIST)) : 2>/dev/null |\
		awk -v RS= -F: '/^# File/,/^# Finished Make data base/ {if ($$1 !~ "^[#.]") {print $$1}}' |\
		sort |\
		egrep -v -e '^[^[:alnum:]]' -e '^$@$$' |\
		xargs
up:
	docker-compose up -d nginx || echo "did you forget to build?"

build:
	docker-compose build

# quick-test fails on first error
# test resets the database to a cached bootstrap 
# clean-test wipes and recreates the bootstrap before testing
test:
	make db-reset-to-bootstrap
	$(DCR) test

clean-test:
	make db-setup
	$(DCR) test

quick-test:
	$(DCR) ftest && (osascript -e 'display notification with title "🦄"') || (osascript -e 'display notification with title "💩"')



# setup deletes the current bootstrap, then cleans the db and generates a new bootstrap
db-setup:
	>./ops/bootstrap.sql
	# need to wipe this to prevent reset from loading the artifact from prepare
	make db-clean
	make db-prepare

# reset makes sure the database is ready for migration or testing without requiring a new container to boot 
db-reset db-reset-to-bootstrap:
	$(DC) up -d test_db
	docker exec -i `docker-compose ps -q test_db` env MYSQL_PWD=test_secret \
		mysql \
		-h127.0.0.1 \
		-uroot \
		test_sc < ops/bootstrap.sql

db-clean:
	$(DC) kill test_db
	$(DC) rm -vf test_db
	$(DC) up -d test_db
	$(DCR) await_test_db

# prepare assumes a reset and generates a new bootstrap sql dump
db-prepare:
	$(DCR) xtest php artisan migrate:refresh -vvv
	$(DCR) test php artisan db:seed -vvv
	$(DC) exec test_db env MYSQL_PWD=test_secret \
		mysqldump \
		--databases test_sc \
		-h127.0.0.1 \
		-uroot \
		> ops/bootstrap.sql # password in env because of "unredirectable" warning about plaintext
	# the following was an integrity check value that was used to identify database states
	# while ignoring volatile values like timestamps
	#
	# $(DCR) test php artisan db:hash | xargs touch

dump-autoload:
	$(DCR) composer dump-autoload

routes:
	$(DCR) fpm php artisan route:list
	$(DCR) fpm php artisan api:routes

status: 
	docker-compose ps

quickly:
	# $(DCR) xtest php vendor/bin/phpunit --stop-on-error --stop-on-failure
	$(DCR) xtest php vendor/bin/phpunit --stop-on-error --stop-on-failure /app/tests/Functional/Api/V1/Controllers/IssueManagementControllerTest
