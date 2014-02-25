export COFFEESTATS_MYSQL_HOSTNAME=localhost
export COFFEESTATS_MYSQL_DATABASE={{ pillar['database']['database'] }}
export COFFEESTATS_MYSQL_USER={{ pillar['database']['user'] }}
export COFFEESTATS_MYSQL_PASSWORD={{ pillar['database']['password'] }}
