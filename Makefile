TEST_DIR="./test"
RUNNER="./vendor/phpunit/phpunit/phpunit"

test:
	@$(RUNNER) $(TEST_DIR)

.PHONY: test 
