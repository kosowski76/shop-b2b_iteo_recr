##@ [Application: Commands]

# @see https://stackoverflow.com/a/43076457
.PHONY: restart-php-fpm
restart-php-fpm: ## Restart the php-fpm service
	"$(MAKE)" execute-in-container DOCKER_SERVICE_NAME=$(DOCKER_SERVICE_NAME_PHP_FPM) COMMAND="sudo kill -USR2 1"

.PHONY: restart-workers
restart-workers: ## Restart all workers
	$(EXECUTE_IN_WORKER_CONTAINER) supervisorctl restart all

.PHONY: stop-workers
stop-workers: ## Stop all workers
	$(EXECUTE_IN_WORKER_CONTAINER) supervisorctl stop worker:*

.PHONY: start-workers
start-workers: ## start all workers
	$(EXECUTE_IN_WORKER_CONTAINER) supervisorctl start worker:*

.PHONY: execute-in-container
execute-in-container: ## Execute a command in a container. E.g. via "make execute-in-container DOCKER_SERVICE_NAME=php-fpm COMMAND="echo 'hello'"
	@$(if $(DOCKER_SERVICE_NAME),,$(error DOCKER_SERVICE_NAME is undefined))
	@$(if $(COMMAND),,$(error COMMAND is undefined))
	$(EXECUTE_IN_ANY_CONTAINER) $(COMMAND)

.PHONY: execute-in-bash
execute-in-bash: ## Execute a command in a bash. E.g. via "make execute-in-bash DOCKER_SERVICE_NAME=php-fpm
	@$(if $(DOCKER_SERVICE_NAME),,$(error DOCKER_SERVICE_NAME is undefined))
	$(EXECUTE_IN_BASH)

.PHONY: enable-xdebug
enable-xdebug: ## Enable xdebug in the given container specified by "DOCKER_SERVICE_NAME". E.g. "make enable-xdebug DOCKER_SERVICE_NAME=php-fpm"
	"$(MAKE)" execute-in-container APP_USER_NAME="root" DOCKER_SERVICE_NAME=$(DOCKER_SERVICE_NAME) COMMAND="sed -i 's/.*zend_extension=xdebug.so/zend_extension=xdebug.so/' '/etc/php/8.2/mods-available/xdebug.ini'"

.PHONY: disable-xdebug
disable-xdebug: ## Disable xdebug in the given container specified by "DOCKER_SERVICE_NAME". E.g. "make disable-xdebug DOCKER_SERVICE_NAME=php-fpm"
	"$(MAKE)" execute-in-container APP_USER_NAME="root" DOCKER_SERVICE_NAME=$(DOCKER_SERVICE_NAME) COMMAND="sed -i 's/.*zend_extension=xdebug.so/;zend_extension=xdebug.so/' '/etc/php/8.2/mods-available/xdebug.ini'"

.PHONY: enable-opcache
enable-opcache: ## Enable opcache in the given container specified by "DOCKER_SERVICE_NAME". E.g. "make enable-opcache DOCKER_SERVICE_NAME=php-fpm"
	"$(MAKE)" execute-in-container APP_USER_NAME="root" DOCKER_SERVICE_NAME=$(DOCKER_SERVICE_NAME) COMMAND="sed -i 's/.*zend_extension=opcache.so/zend_extension=opcache.so/' '/etc/php/8.2/mods-available/opcache.ini'"

.PHONY: disable-opcache
disable-opcache: ## Disable opcache in the given container specified by "DOCKER_SERVICE_NAME". E.g. "make disable-opcache DOCKER_SERVICE_NAME=php-fpm"
	"$(MAKE)" execute-in-container APP_USER_NAME="root" DOCKER_SERVICE_NAME=$(DOCKER_SERVICE_NAME) COMMAND="sed -i 's/.*zend_extension=opcache.so/;zend_extension=opcache.so/' '/etc/php/8.2/mods-available/opcache.ini'"
