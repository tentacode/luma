.PHONY: help

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

install: ## Installing project dependencies
	composer install
	php artisan key:generate

init: ## Initializing app
	php artisan luma:create-developer

test: ## All tests
	bin/phpunit
	bin/phpstan
	bin/phpcs
