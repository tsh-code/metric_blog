#!/usr/bin/env bash

serialize_environment_variables()
{
  export -p | sed 's/declare -x/export/g'
}
substitute_environment_variables()
{
  envsubst $(printenv | cut -f1 -d'=' | sed 's/.*/\\\${&}/' | tr '\n' ',')
}

set -e

echo "$1" > /run/docker-entrypoint-command.txt
case "$1" in
  '--start-cgi-server')
    if [ "$APP_UPDATE_DATABASE" == "on" ]
    then
      php bin/console doctrine:database:create --if-not-exists
      php bin/console doctrine:m:m --no-interaction
    fi
    php-fpm --nodaemonize
  ;;

  '--start-http-server')
    substitute_environment_variables < /etc/nginx/sites-available/symfony.conf.template > /etc/nginx/sites-available/symfony.conf
    ln -s /etc/nginx/sites-available/symfony.conf /etc/nginx/sites-enabled/symfony
    nginx -g 'daemon off;'
  ;;

  *)
    echo 'undefined' > /run/docker-entrypoint-command.txt
    exec "$@"
  ;;
esac

exit 0
