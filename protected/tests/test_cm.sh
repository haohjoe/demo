#!/bin/bash

APPLICATION_ENV='newdev'
MODULE_NAME='cm'

export APPLICATION_ENV MODULE_NAME
phpunit --colors --coverage-html ~/data/coverage/"$MODULE_NAME" --testsuite "$MODULE_NAME" "$1"
