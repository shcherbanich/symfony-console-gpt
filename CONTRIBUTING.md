# Contributing

Welcome to `symfony-console-gpt`! We're glad you're interested in contributing.

_Please follow the guidelines below to ensure your contributions align with our coding standards and practices._

## Installation

To install the project and run the tests, you need to clone it first:

```sh
$ git clone https://github.com/shcherbanich/symfony-console-gpt.git
```

You will then need to run a [Composer](https://getcomposer.org/) installation:

```sh
$ cd BetterReflection
$ curl -s https://getcomposer.org/installer | php
$ php composer.phar update
```

## Testing

```sh
$ php composer.phar run-script test
```

## Code Style

We follow the [PSR-12 coding standard](https://www.php-fig.org/psr/psr-12/) to maintain consistent and readable code across the project. Before submitting any code changes, please make sure your code adheres to this standard.

Run the script before pushing changes:

```sh
$ php composer.phar run-script phpcbf
```
