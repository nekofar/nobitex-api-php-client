# Changelog

All notable changes to this project will be documented in this file.

## [2.0.6] - 2022-03-21

### Documentation

- Improve the dependabot configuration file

### Miscellaneous Tasks

- Add `webmozart/assert:^1.10` package
- Add `phpstan/phpstan-webmozart-assert:^0.12.16` package
- Cleanup scripts on composer configs
- Bump actions/cache from 2.1.6 to 2.1.7
- Update github funding configs
- Solve github funding broken link issue

### Ci

- Add cache action for caching composer packages
- Update `actions/checkout` from v2.x.x to v3.0.0
- Update `codecov/codecov-action` to version 2.1.0
- Bump `shivammathur/setup-php` to 2.17.1
- Update `dependabot` prefixes on configuration
- Bump `actions/cache` from 2.x.x to 3.0.0

### Revert

- Add `phpstan/phpstan-webmozart-assert:^0.12.16` package
- Add `webmozart/assert:^1.10` package

## [2.0.5] - 2021-09-19

### Miscellaneous Tasks

- Cleanup configuration file
- Update extra branch alias

### Testing

- Remove empty test case files

## [2.0.4] - 2021-09-19

### Ci

- Chnage php version of code style job on static workflow

## [2.0.3] - 2021-09-19

### Miscellaneous Tasks

- Update composer scripts and descriptions
- Normalize composer configs
- Add the config file for phpmd

### Ci

- Enable running static tests on push

## [2.0.2] - 2021-09-19

### Documentation

- Update version on installation

## [2.0.1] - 2021-09-18

### Ci

- Remove travis config file

## [2.0.0] - 2021-09-18

### Documentation

- Add target branch to dependabot configuration file
- Add commit message scop to dependabot configuration file
- Improve the dependabot configuration file

### Miscellaneous Tasks

- Add non feature branches to the configs
- Remove homepage and issues links
- Update `nekofar/dev-tools` from ^1.0 to ^1.1
- Replace ruleset by nekofar coding standard
- Add `selective/array-reader` package ^1.1
- Ignore some errors on phpstan configs
- Remove `selective/array-reader` package

### Refactor

- Add missing strict type declarations
- Replace short typehints by long ones
- Use the full qualifiers for referencing in docblock
- Remove concurrent typehints from docblock
- Convert computations to yoda style
- Automatically solve all psr 12 issues
- Automatically solve all psr 2 issues
- Automatically solve all psr 12 issues
- Add missing strict type declarations
- Automatically solve all psr 2 issues
- Docblock typehints referenced via a fully qualified name
- Remove unused uses from classes
- Replace empty usages by in array
- Replace typehints that should not be referenced via a fully qualified name
- Replace usages of isset function
- Remove useless if conditions
- Solve some issues related to type casts
- Replace param tags on docblock by typehints
- Specify type hint for items of its traversabl
- Solve issue related to missing spaces around type casts
- Solve issue related space and orders on docblock
- Use generic type hint syntax instead array type hint syntax on docblock
- Multi-line function calls must have a trailing comma after the last parameter
- Solve issue related to properties that does not specify type hint for its items
- Use early exits whenever thats possible
- Add missing visibility for constants
- Correct number of lines between methods
- Mark nullable params with null operator
- Add missing throw tags for invalid argument exceptions
- Some minor improvements over basic auth
- Solve some issues related to traits and tests
- Replace property exists by isset again
- Solve some issues related to the profile
- Solve some issues related to the transaction and withdraw
- Solve some issues related to the typehints
- Solve some of long line issues
- Customize phpcs ruleset due to project structure limits temporary
- Solve some issues related to the typehints
- Replace unsafe usage of new static
- Solve some issues related to the types

### Styling

- Solve white spaces issues between lines
- Remove extra whitespace and lines

### Testing

- Replace dynamic assert calls by statics
- Add missing return type hints for anonymous functions
- Add missing return type hints for test methods
- Solve json encode return type issues
- Remove some of redundant tests
- Replace not false by not empty assert for get market order

### Revert

- Add non feature branches to the configs

## [1.0.8] - 2021-09-16

### Documentation

- Add new dependabot configuration file

### Miscellaneous Tasks

- Upgrade phpstan/phpstan package to 0.12.99
- Replace required dev packages by `nekofar/dev-tools`
- Remove useless includes from phpstan config file

## [1.0.7] - 2021-09-09

### Documentation

- Add funding configuration file
- Update badge icons and links

### Miscellaneous Tasks

- Change minimum php version to 7.3
- Change minimum php version to 7.3
- Add configuration file
- Remove composer lock file
- Cleanup git ignored files list
- Add the phpstan and rules
- Upgrade php-http/httplug package to ^2.0
- Upgrade php-http/curl-client package to ^2.0
- Add support for php version ^8.0
- Add pestphp/pest package ^1.18
- Upgrade php-http/message package to ^1.12

### Refactor

- Migrate from travis to github workflow

### Ci

- Add new workflows for testing and analysis
- Disable run static workflow on push
- Change test runner from phpunit to pest
- Remove php version 8.1 from tests matrix

## [1.0.6] - 2021-07-05

### Styling

- Add missing comma on basic authentication method

## [1.0.5] - 2021-07-05

### Bug Fixes

- Solve missing captcha issue on authentication

### Miscellaneous Tasks

- Change minimum php version to 7.3
- 1.0.5

## [1.0.4] - 2021-07-05

### Bug Fixes

- Remove version from composer config

### Miscellaneous Tasks

- 1.0.4

## [1.0.3] - 2021-07-05

### Miscellaneous Tasks

- Upgrade netresearch/jsonmapper package to v4.0.0
- Upgrade php-http/discovery package to 1.14.0
- Upgrade php-http/message package to 1.11.1
- Upgrade dealerdirect/phpcodesniffer-composer-installer package to v0.7.1
- Upgrade escapestudios/symfony2-coding-standard package to 3.12.0
- Upgrade guzzlehttp/psr7 package to 2.0.0
- Upgrade phpunit/phpunit package to 9.5.6
- Upgrade squizlabs/php_codesniffer package to 3.6.0
- Add the config file for standard version
- 1.0.3

## [1.0.2] - 2020-10-16

### Miscellaneous Tasks

- Update composer lock dependencies
- Normalize composer config
- Bump version to v1.0.2

<!-- generated by git-cliff -->
