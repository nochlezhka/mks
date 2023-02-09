THIS_DIR := $(dir $(abspath $(firstword $(MAKEFILE_LIST))))

lint:
	docker run -v $(THIS_DIR)/src:/src -v $(THIS_DIR)/.clj-kondo:/.clj-kondo --rm borkdude/clj-kondo clj-kondo --lint src
