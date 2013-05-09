*********************************
Development setup for coffeestats
*********************************

Checkout from git
=================

Send credentials to Florian Baumann to get access to the `git repository`_.

.. _git repository: git@o0.n0q.org:coffeestats

Clone your initial copy::

  export PROJECTS=/home/dev/projects
  cd $PROJECTS
  git clone -b dev git@o0.n0q.org:coffeestats

Setup database
==============

Coffeestats uses MySQL, with a separate database.

#. Install MySQL from operating system packages or from source.
#. Create a MySQL database with the credentials in auth/config.php::

    sh> mysql -u root -h localhost -p mysql
    mysql> CREATE DATABASE coffeestats CHARACTER SET 'UTF8';
    mysql> GRANT ALL PRIVILEGES ON coffeestats.* TO
      coffeestats@localhost IDENTIFIED BY 'mysqls3cRet';

#. Import the SQL schema into the database::

    sh> mysql -u coffeestats -h localhost -p coffeestats <
      devdocs/schema.sql

Setup nginx
===========

This section describes the setup of the nginx_ web server with PHP5 in a
FastCGI setup with a local Unix domain socket (idea from the `Linode Wiki`_).

.. _nginx: http://nginx.com/
.. _Linode Wiki: http://library.linode.com/web-servers/nginx/php-fastcgi/debian-6-squeeze

#. Create the necessary directories::

    sh> mkdir -p /srv/www/bin /srv/www/coffeestats/logs

#. Create a FastCGI launcher script (/srv/www/bin/php-fastcgi)::

    #!/bin/sh
    FASTCGI_USER=www-data
    FASTCGI_GROUP=www-data
    SOCKET=/var/run/php-fastcgi/php-fastcgi.socket
    PIDFILE=/var/run/php-fastcgi/php-fastcgi.pid
    CHILDREN=6
    PHP5=/usr/bin/php5-cgi

    /usr/bin/spawn-fcgi -s $SOCKET -P $PIDFILE -C $CHILDREN -u $FASTCGI_USER -g $FASTCGI_GROUP -f $PHP5

#. Create the virtualhost config (/etc/nginx/sites-available/coffeestats)::

    server {
      server_name local.coffeestats.org;
      access_log /srv/www/coffeestats/logs/access.log;
      error_log /srv/www/coffeestats/logs/error.log;
      root /home/dev/projects/coffeestats;

     location / {
        root   /htdocs/$server_name;
        index  index index.php;
        try_files $uri $uri/ $uri.php;
        #auth_basic "Restricted";
        #auth_basic_user_file  /var/www/htdocs/dev.coffeestats.org/htpasswd;
      }

      location ~ \.php$ {
        try_files $uri =404;
        include /etc/nginx/fastcgi_params;
        fastcgi_pass unix:/var/run/php-fastcgi/php-fastcgi.socket;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME /home/dev/projects/coffeestats$fastcgi_script_name;

        fastcgi_param COFFEESTATS_MYSQL_HOSTNAME localhost;
        fastcgi_param COFFEESTATS_MYSQL_USER coffeestats;
        fastcgi_param COFFEESTATS_MYSQL_PASSWORD mysqls3cRet;
        fastcgi_param COFFEESTATS_MYSQL_DATABASE coffeestats;
      }

      # for php files with GET parameters
      location ~ (profile|public|ontherun)$ {
        root           /htdocs/$server_name;
        fastcgi_pass   unix:/var/run/php-fastcgi/php-fastcgi.socket;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  /home/dev/projects/coffeestats$fastcgi_script_name;
        include        fastcgi_params;

        fastcgi_param COFFEESTATS_MYSQL_HOSTNAME localhost;
        fastcgi_param COFFEESTATS_MYSQL_USER coffeestats;
        fastcgi_param COFFEESTATS_MYSQL_PASSWORD mysqls3cRet;
        fastcgi_param COFFEESTATS_MYSQL_DATABASE coffeestats;
      }

    }

#. Enable virtualhost and restart nginx::

    sh> cd /etc/nginx/sites-enabled
    sh> ln -s ../sites-available/coffeestats .
    sh> /etc/init.d/nginx restart

#. Make fastcgi-script executable and start it::

    sh> chmod +x /srv/www/bin/php-fastcgi
    sh> /srv/www/bin/php-fastcgi

#. Grant access to /home/dev/projects/coffeestats to the www-data user
#. Setup DNS or a /etc/hosts entry to point local.coffeestats.org to the local host::

    sh> echo '127.0.0.1 local.coffeestats.org' >> /etc/hosts

#. Open http://local.coffeestats.org/ in a browser of your choice
