# For local builds always to use "latest" as tag per default
ifeq ($(ENV),local)
	TAG:=latest
endif

COMPOSE_DOCKER_CLI_BUILD?=1
DOCKER_BUILDKIT?=1

export COMPOSE_DOCKER_CLI_BUILD
export DOCKER_BUILDKIT

# Container names must match the names in the docker-composer.yml files
DOCKER_SERVICE_NAME_NGINX:=nginx
DOCKER_SERVICE_NAME_PHP_HOST:=php-host
DOCKER_SERVICE_NAME_PHP_FPM:=php-fpm
DOCKER_SERVICE_NAME_PHP_CLI:=php-cli

DOCKER_DIR:=./.docker
DOCKER_ENV_FILE:=$(DOCKER_DIR)/.env
DOCKER_COMPOSE_DIR:=$(DOCKER_DIR)/docker-compose
DOCKER_COMPOSE_FILE:=$(DOCKER_COMPOSE_DIR)/docker-compose.yml
DOCKER_COMPOSE_FILE_LOCAL:=$(DOCKER_COMPOSE_DIR)/docker-compose.local.yml
DOCKER_COMPOSE_FILE_PHP_HOST:=$(DOCKER_COMPOSE_DIR)/docker-compose-php-host.yml
#DOCKER_COMPOSE_PROJECT_NAME:=app-service_$(ENV)
DOCKER_COMPOSE_PROJECT_NAME:=$(COMPOSE_PROJECT_NAME)_$(ENV)

DOCKER_COMPOSE_COMMAND:=ENV=$(ENV) \
 TAG=$(TAG) \
 DOCKER_REGISTRY=$(DOCKER_REGISTRY) \
 DOCKER_NAMESPACE=$(DOCKER_NAMESPACE) \
 APP_USER_ID=$(APP_USER_ID) \
 APP_GROUP_ID=$(APP_GROUP_ID) \
 APP_USER_NAME=$(APP_USER_NAME) \
 docker compose -p $(DOCKER_COMPOSE_PROJECT_NAME) --env-file $(DOCKER_ENV_FILE)

DOCKER_COMPOSE:=$(DOCKER_COMPOSE_COMMAND) -f $(DOCKER_COMPOSE_FILE) -f $(DOCKER_COMPOSE_FILE_LOCAL)
DOCKER_COMPOSE_PHP_HOST:=$(DOCKER_COMPOSE_COMMAND) -f $(DOCKER_COMPOSE_FILE_PHP_HOST)

EXECUTE_IN_ANY_CONTAINER?=
EXECUTE_IN_PHP-CLI_CONTAINER?=
EXECUTE_IN_BASH?=

DOCKER_SERVICE_NAME?=

EXECUTE_IN_CONTAINER?=
ifndef EXECUTE_IN_CONTAINER
	# check if 'make' is executed in a docker container, see https://stackoverflow.com/a/25518538/413531
	# `wildcard $file` checks if $file exists, see https://www.gnu.org/software/make/manual/html_node/Wildcard-Function.html
	# i.e. if the result is "empty" then $file does NOT exist => we are NOT in a container
	ifeq ("$(wildcard /.dockerenv)","")
		EXECUTE_IN_CONTAINER=true
	endif
endif
ifeq ($(EXECUTE_IN_CONTAINER),true)
	EXECUTE_IN_ANY_CONTAINER:=$(DOCKER_COMPOSE) exec -T --user $(APP_USER_NAME) $(DOCKER_SERVICE_NAME)
	EXECUTE_IN_PHP-CLI_CONTAINER:=$(DOCKER_COMPOSE) exec -T --user $(APP_USER_NAME) $(DOCKER_SERVICE_NAME_PHP_CLI)
	EXECUTE_IN_BASH:=$(DOCKER_COMPOSE) exec -it --user $(APP_USER_NAME) $(DOCKER_SERVICE_NAME) bash
endif

##@ [Docker]

.PHONY: docker-clean
docker-clean: ## Remove the .env file for docker
	@rm -f $(DOCKER_ENV_FILE)

.PHONY: validate-docker-variables
validate-docker-variables: .docker/.env
	@$(if $(TAG),,$(error TAG is undefined))
	@$(if $(ENV),,$(error ENV is undefined))
	@$(if $(DOCKER_REGISTRY),,$(error DOCKER_REGISTRY is undefined - Did you run make-init?))
	@$(if $(DOCKER_NAMESPACE),,$(error DOCKER_NAMESPACE is undefined - Did you run make-init?))
	@$(if $(APP_USER_ID),,$(error APP_USER_ID is undefined - Did you run make-init?))
	@$(if $(APP_GROUP_ID),,$(error APP_GROUP_ID is undefined - Did you run make-init?))
	@$(if $(APP_USER_NAME),,$(error APP_USER_NAME is undefined - Did you run make-init?))

.docker/.env:
	@cp $(DOCKER_ENV_FILE).example $(DOCKER_ENV_FILE)

.PHONY:docker-build-image
docker-build-image: validate-docker-variables ## Build all docker images OR a specific image by providing the service name via: make docker-build DOCKER_SERVICE_NAME=<service>
	$(DOCKER_COMPOSE) build $(DOCKER_SERVICE_NAME)

.PHONY: docker-build-php
docker-build-php: validate-docker-variables ## Build the php host image
	$(DOCKER_COMPOSE_PHP_HOST) build $(DOCKER_SERVICE_NAME_PHP_HOST)

.PHONY: docker-build
docker-build: docker-build-php docker-build-image ## Build the php image and then all other docker images

.PHONY: docker-up
docker-up: validate-docker-variables ## Create and start all docker containers. To create/start only a specific container, use DOCKER_SERVICE_NAME=<service>
	$(DOCKER_COMPOSE) up -d $(DOCKER_SERVICE_NAME)

.PHONY: docker-down
docker-down: validate-docker-variables ## Stop and remove all docker containers.
	@$(DOCKER_COMPOSE) down

.PHONY: docker-restart
docker-restart: docker-down docker-up ## Restart all docker containers.

.PHONY: docker-config
docker-config: validate-docker-variables ## List the configuration
	@$(DOCKER_COMPOSE) config

.PHONY: docker-prune
docker-prune: ## Remove ALL unused docker resources, including volumes
	@docker system prune -a -f --volumes

.PHONY: docker-test
docker-test: validate-docker-variables ## Run the infrastructure tests for the docker setup
	bash $(DOCKER_DIR)/docker-test.sh


