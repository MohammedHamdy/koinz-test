#!/bin/sh

set -e

host="$1"
shift
cmd="$@"

until mysql -uroot -p"${MYSQL_ROOT_PASSWORD}" -h"${host}" -e 'SELECT 1'; do
  >&2 echo "MySQL is unavailable - sleeping"
  sleep 1
done

>&2 echo "MySQL is up - executing command"
exec $cmd
