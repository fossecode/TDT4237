TEST_DIR="./test"
RUNNER="./vendor/phpunit/phpunit/phpunit"

test:
	@$(RUNNER) $(TEST_DIR)

serve:
	@php composer.phar start

.PHONY: test 
