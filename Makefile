# Define the default shell
OS?=undefined
ifeq ($(OS),Windows_NT)
	# Windows requires the .exe extension, otherwise the entry is ignored
	# @see https://stackoverflow.com/a/60318554/413531
    SHELL := bash.exe
    # make sure that MinGW / MSYSY does not automatically convert paths starting with /
    export MSYS_NO_PATHCONV=1
else
    SHELL := bash
endif

.SHELLFLAGS := -euo pipefail -c
MAKEFLAGS += --warn-undefined-variables
MAKEFLAGS += --no-builtin-rules

-include .make/.env

ARGS?=

DEFAULT_GOAL := help
help:
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n"} /^[a-zA-Z0-9_-]+:.*?##/ { printf "  \033[36m%-40s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

include .make/*.mk

##@ [Make]

.PHONY: make-init
make-init: ENVS= ## Initializes the local .makefile/.env file with ENV variables for make
make-init:
	@cp .make/.env.example .make/.env
	@for variable in $(ENVS); do \
	  echo $$variable | tee -a .make/.env; \
	  done
	@echo "You can update your .make/.env file with your local settings"
