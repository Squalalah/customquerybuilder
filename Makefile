.PHONY: install update

composer.lock: composer.json
	@composer update

test:
	@.\vendor\bin\phpunit tests/ --coverage-clover build/log/clover.xml --coverage-html build
	@php -f tests/coverage-checker.php -- build/log/clover.xml 100

lint:
	.\vendor\bin\php-cs-fixer fix -v
	.\vendor\bin\phpstan analyse -c phpstan.neon --xdebug