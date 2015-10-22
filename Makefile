RUNNER="./vendor/phpunit/phpunit/phpunit"
PENETRATION_TARGET="http://tdt4237.idi.ntnu.no:5010"
PENETRATION_TEST_DIR="./test/penetration/"
UNIT_TEST_DIR="./test/unit/"

unit-test:
	@$(RUNNER) $(UNIT_TEST_DIR)

penetration-test:
	@$(RUNNER) $(PENETRATION_TEST_DIR) $(PENETRATION_TARGET)

test: unit-test

serve:
	@php -S 0.0.0.0:8080 -t web web/index.php

.PHONY: test serve
