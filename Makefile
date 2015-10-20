TEST_DIR="./test"
RUNNER="./vendor/phpunit/phpunit/phpunit"

test:
	@$(RUNNER) $(TEST_DIR)

serve:
	@php -S 0.0.0.0:8080 -t web web/index.php

.PHONY: test 
