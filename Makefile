TEST_DIR="./test"
RUNNER="./vendor/phpunit/phpunit/phpunit"
PENETRATION_TARGET="http://tdt4237.idi.ntnu.no:5010"

test:
	@$(RUNNER) $(TEST_DIR) $(PENETRATION_TARGET)

serve:
	@php composer.phar start

.PHONY: test 
