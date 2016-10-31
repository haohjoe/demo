#!/bin/bash

CASE_DIR=$PWD/../modules/

APPLICATION_ENV="testing"
STATUS=0
for MODULE_NAME in `ls $CASE_DIR`
do
export APPLICATION_ENV MODULE_NAME
echo -e "\n========= $APPLICATION_ENV --> $MODULE_NAME =========\n"
phpunit --coverage-text --testsuite "$MODULE_NAME"
STATUS=$(( $STATUS + $? ))
done
exit $STATUS
