PHP?=php7.4
COMPOSER=/usr/local/bin/composer

php-info:
	@echo "Default PHP version: $(PHP) (Run with custom PHP version: make install PHP=php8.0).\n";

list: php-info ## List
	@sed -rn 's/^([a-zA-Z_-]+):.*?## (.*)$$/"\1" "\2"/p' < $(MAKEFILE_LIST) | xargs printf "%-20s%s\n"

install: php-info ## Install dependencies (make install PHP=php8.0)
	@if [ ! -d "vendor" ]; then $(PHP) $(COMPOSER) install; fi;

install-force: php-info ## Force install dependencies (make install PHP=php8.0)
	$(PHP) $(COMPOSER) install

update: php-info ## Update dependencies
	@$(PHP) $(COMPOSER) update

test: php-info install ## Run PHPUnit tests
	$(PHP) vendor/bin/phpunit --configuration=phpunit.xml.dist

test-coverage: php-info install ## Run PHPUnit tests with coverage report
	$(PHP) vendor/bin/phpunit --configuration=phpunit.xml.dist --coverage-text --coverage-html=tests/runtime/coverage

mutation-test: php-info install ## Run Infection mutation tests
	$(PHP) vendor/bin/infection --threads=4 --show-mutations -vvv

code-style-fix: php-info install ## Fix code style
	$(PHP) vendor/bin/php-cs-fixer fix --allow-risky=yes --diff

psalm: php-info install ## Run Psalm check
	$(PHP) vendor/bin/psalm --config=psalm.xml.dist --threads=4 --show-snippet=true --show-info=true

run-worker: php-info install ## Run PHP Censor worker
	$(PHP) bin/console php-censor:worker -v

.PHONY: php-info list install install-force update test test-coverage mutation-test code-style-fix psalm run-worker
.DEFAULT_GOAL := list
