TEST_DIR="./test"
RUNNER="./vendor/phpunit/phpunit/phpunit"
PENETRATION_TARGET="http://tdt4237.idi.ntnu.no:5010"

test:
	@$(RUNNER) $(TEST_DIR) $(PENETRATION_TARGET)

serve:
	@php -S 0.0.0.0:8080 -t web web/index.php

.PHONY: test 
