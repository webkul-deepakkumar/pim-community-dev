#!/bin/sh

# We need the TESTFILES var in $1
TESTFILES=$@

fail=0
for TESTFILE in $TESTFILES; do
    echo $TESTFILE
    uuid=$(uuidgen)
    docker-compose exec -T fpm ./vendor/bin/phpunit -c app --coverage-php var/coverage/${uuid}_phpunit.cov --log-junit var/tests/phpunit/phpunit_${uuid}.xml --filter $TESTFILE
    fail=$(($fail + $?))
done

return $fail
