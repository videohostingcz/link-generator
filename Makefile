
devel: install-dev

qa: test phpstan coding-standard

install-dev:
	COMPOSER_ALLOW_SUPERUSER=1 composer install --dev

test: install-dev
	./vendor/bin/phpunit tests/*

phpstan: install-dev
	test -d .tmp/phpstan || COMPOSER_ALLOW_SUPERUSER=1 composer create-project phpstan/phpstan .tmp/phpstan -q
	.tmp/phpstan/phpstan analyse src tests --level=9

coding-standard:
	test -d .tmp/coding-standard || COMPOSER_ALLOW_SUPERUSER=1 composer create-project nette/coding-standard .tmp/coding-standard -q
	.tmp/coding-standard/ecs check src tests --preset php74

coding-standard-fix:
	.tmp/coding-standard/ecs fix src tests --preset php74
