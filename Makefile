help: ## Show this message
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

clean-code: ## Run PHP CS Fixer
	./vendor/bin/php-cs-fixer --diff -v fix

test: test-code test-qa ## Run code and QA tests

test-code: ## Run code tests
	./vendor/bin/phpunit

test-qa: test-phpcs test-psalm test-phpmd test-phpcpd ## Run QA tests

test-phpcs: ## Run codestyle tests
	./vendor/bin/php-cs-fixer --diff --dry-run --using-cache=no -v fix src

test-psalm: ## Run static analysis tests
	./vendor/bin/psalm --show-info=true --long-progress

test-phpmd: ## Run mess detector tests
	./vendor/bin/phpmd --exclude Tests/Fixtures src/ text phpmd.xml

test-phpcpd: ## Run copy/paste tests
	./vendor/bin/phpcpd --exclude Tests src/

.PHONY: clean-code test test-code test-qa test-phpcs test-psalm test-phpmd test-phpcpd
