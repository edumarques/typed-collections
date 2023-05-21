# Executables (local)
DOCKER_COMP = docker-compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP_CONT) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        = help build up start down logs sh composer vendor sf cc

## 👷 Makefile
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## 🐳 Docker
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub
	@$(DOCKER_COMP) up

up-d: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: build up-d ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show logs
	@$(DOCKER_COMP) logs

logs-f: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

ps: ## Show containers' statuses
	@$(DOCKER_COMP) ps

sh: ## Connect to the PHP FPM container
	@$(PHP_CONT) sh

## ✅ Code Quality
phpcs: ## Run PHP Code Sniffer
	@$(PHP_CONT) ./vendor/bin/phpcs

phpcs-fix: ## Run PHP Code Sniffer (fix)
	@$(PHP_CONT) ./vendor/bin/phpcbf

phpstan: ## Run PHPStan
	@$(PHP_CONT) ./vendor/bin/phpstan

lint: phpcs phpstan ## Run PHP Code Sniffer and PHPStan

test: ## Run tests
	@$(PHP) vendor/bin/phpunit ${args}

test-cov: ## Run tests and generate coverage report
	@$(DOCKER_COMP) exec php vendor/bin/phpunit --coverage-clover coverage/clover/clover.xml --coverage-html coverage/html

## 🧙 Composer
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer
