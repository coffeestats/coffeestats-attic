nginx:
  pkg:
    - installed
  service:
    - running
    - require:
      - pkg: nginx

mysql-server:
  pkg:
    - installed
  service:
    - running
    - name: mysql
    - require:
      - pkg: mysql-server

python-mysqldb:
  pkg:
    - installed

php5-fpm:
  service:
    - running
    - require:
      - pkg: php5-fpm

php-packages:
  pkg.installed:
    - names:
        - php5-cli
        - php5-fpm
        - php5-cgi
        - php5-mysql
  file.directory:
    - name: /etc/php5
    - mode: 0755
    - user: root
    - group: root
    - requires:
      - pkg.installed: php5-cli

/home/vagrant/csdev.sh:
  file.managed:
    - user: vagrant
    - group: vagrant
    - mode: 0644
    - template: jinja
    - source: salt://coffeestats/csdev.sh

coffeestats-db:
  mysql_database.present:
    - name: {{ pillar['database']['database'] }}
    - require:
      - service.running: mysql
      - pkg.installed: python-mysqldb
  mysql_user.present:
    - host: localhost
    - name: {{ pillar['database']['user'] }}
    - password: {{ pillar['database']['password'] }}
    - require:
      - service.running: mysql
      - pkg.installed: python-mysqldb
  mysql_grants.present:
    - grant: all privileges
    - database: {{ pillar['database']['database'] }}.*
    - user: {{ pillar['database']['user'] }}
    - host: localhost
    - require:
      - mysql_database.present: {{ pillar['database']['database'] }}
      - mysql_user.present: {{ pillar['database']['user'] }}
  cmd.run:
    - name: . /home/vagrant/csdev.sh; ./php-database-migration/migrate --up
    - cwd: /vagrant/devdocs
    - user: vagrant
    - group: vagrant
    - require:
      - file: /home/vagrant/csdev.sh
      - mysql_grants.present: coffeestats-db

/etc/php5/fpm/pool.d/coffeestats.conf:
  file.managed:
    - user: root
    - group: root
    - mode: 0644
    - source: salt://coffeestats/php-fpm.conf
    - watch_in:
      - service: php5-fpm

/etc/nginx/sites-available/default:
  file.managed:
    - user: root
    - group: root
    - mode: 0644
    - template: jinja
    - source: salt://coffeestats/nginx.conf
    - require:
      - file: /etc/php5/fpm/pool.d/coffeestats.conf
    - watch_in:
      - service: nginx
