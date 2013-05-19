*********************************
Development setup for coffeestats
*********************************

Checkout from git
=================

Send credentials to Florian Baumann to get access to the `git repository`_.

.. _git repository: git@o0.n0q.org:coffeestats

Clone your initial copy:

.. code-block:: sh

  export PROJECTS=/home/dev/projects
  cd $PROJECTS
  git clone -b dev git@o0.n0q.org:coffeestats

Setup database
==============

Coffeestats uses MySQL, with a separate database.

#. Install MySQL from operating system packages or from source.
#. Create a MySQL database with the credentials in auth/config.php:

    .. code-block:: sh

        mysql -u root -h localhost -p mysql

    .. code-block:: sql

        CREATE DATABASE coffeestats CHARACTER SET 'UTF8';
        GRANT ALL PRIVILEGES ON coffeestats.* TO
          coffeestats@localhost IDENTIFIED BY 'mysqls3cRet';

#. Define environment variables COFFEESTATS_MYSQL_HOSTNAME,
   COFFEESTATS_MYSQL_DATABASE, COFFEESTATS_MYSQL_USER and
   COFFEESTATS_MYSQL_PASSWORD:

    .. code-block:: sh

        export COFFEESTATS_MYSQL_HOSTNAME=localhost
        export COFFEESTATS_MYSQL_DATABASE=coffeestats
        export COFFEESTATS_MYSQL_USER=coffeestats
        export COFFEESTATS_MYSQL_PASSWORD=mysqls3cRet

#. Use php-database-migrations to setup the database schema:

    .. code-block:: sh

        cd devdocs
        ./php-database-migration/migrate --up

If you already have a coffeestats database schema that is from before the
introduction of schema migrations you will have to fake the initial schema
migration before running the update:

.. code-block:: sh

    cd devdocs
    ./php-database-migration/migrate --fake=20130517133223
    ./php-database-migration/migrate --up


Setup nginx
===========

This section describes the setup of the nginx_ web server with PHP5 in a
FastCGI setup with a local Unix domain socket (idea from the `Linode Wiki`_).

.. _nginx: http://nginx.com/
.. _Linode Wiki: http://library.linode.com/web-servers/nginx/php-fastcgi/debian-6-squeeze

#. Create the necessary directories:

    .. code-block:: sh

        mkdir -p /srv/www/bin /srv/www/coffeestats/logs

#. Create a FastCGI launcher script (/srv/www/bin/php-fastcgi):

    .. code-block:: sh

        #!/bin/sh
        FASTCGI_USER=www-data
        FASTCGI_GROUP=www-data
        SOCKET=/var/run/php-fastcgi/php-fastcgi.socket
        PIDFILE=/var/run/php-fastcgi/php-fastcgi.pid
        CHILDREN=6
        PHP5=/usr/bin/php5-cgi

    .. code-block:: sh

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

        if (!-f $request_filename) {
          rewrite ^/api/([^/]+)/(.*)\.php$ /api/api-$1.php?q=$2 last;
          break;
        }

        if (!-d $request_filename) {
          rewrite ^/api/([^/]+)/(.*)\.php$ /api/api-$1.php?q=$2 last;
          break;
        }

        include /etc/nginx/fastcgi_params;
        fastcgi_pass unix:/var/run/php-fastcgi/php-fastcgi.socket;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;

        fastcgi_param COFFEESTATS_MYSQL_HOSTNAME localhost;
        fastcgi_param COFFEESTATS_MYSQL_USER coffeestats;
        fastcgi_param COFFEESTATS_MYSQL_PASSWORD mysqls3cRet;
        fastcgi_param COFFEESTATS_MYSQL_DATABASE coffeestats;
        fastcgi_param COFFEESTATS_RECAPTCHA_PUBLICKEY yourcustomrecaptchapublickey;
        fastcgi_param COFFEESTATS_RECAPTCHA_PRIVATEKEY yourcustomrecaptchaprivatekey;
        fastcgi_param COFFEESTATS_PIWIK_SITEID piwiksiteid;
        fastcgi_param COFFEESTATS_PIWIK_HOST piwik.example.org;
        fastcgi_param COFFEESTATS_MAIL_FROM_ADDRESS no-reply@coffeestats.org;
        fastcgi_param COFFEESTATS_SITE_SECRET somerandomstring;
        fastcgi_param COFFEESTATS_SITE_NAME coffeestats.org-development;
        fastcgi_param COFFEESTATS_SITE_ADMINMAIL team@coffeestats.org;
      }

      # for php files with GET parameters
      location ~ (profile|public|ontherun|action|delete)$ {
        root           /htdocs/$server_name;
        fastcgi_pass   unix:/var/run/php-fastcgi/php-fastcgi.socket;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name.php;
        include        fastcgi_params;

        fastcgi_param COFFEESTATS_MYSQL_HOSTNAME localhost;
        fastcgi_param COFFEESTATS_MYSQL_USER coffeestats;
        fastcgi_param COFFEESTATS_MYSQL_PASSWORD mysqls3cRet;
        fastcgi_param COFFEESTATS_MYSQL_DATABASE coffeestats;
        fastcgi_param COFFEESTATS_RECAPTCHA_PUBLICKEY yourcustomrecaptchapublickey;
        fastcgi_param COFFEESTATS_RECAPTCHA_PRIVATEKEY yourcustomrecaptchaprivatekey;
        fastcgi_param COFFEESTATS_PIWIK_SITEID piwiksiteid;
        fastcgi_param COFFEESTATS_PIWIK_HOST piwik.example.org;
        fastcgi_param COFFEESTATS_MAIL_FROM_ADDRESS no-reply@coffeestats.org;
        fastcgi_param COFFEESTATS_SITE_SECRET somerandomstring;
        fastcgi_param COFFEESTATS_SITE_NAME coffeestats.org-development;
        fastcgi_param COFFEESTATS_SITE_ADMINMAIL team@coffeestats.org;
      }
    }

#. Enable virtualhost and restart nginx:

    .. code-block:: sh

        cd /etc/nginx/sites-enabled
        ln -s ../sites-available/coffeestats .
        /etc/init.d/nginx restart

#. Make fastcgi-script executable and start it:

    .. code-block:: sh

        chmod +x /srv/www/bin/php-fastcgi
        /srv/www/bin/php-fastcgi

#. Grant access to /home/dev/projects/coffeestats to the www-data user
#. Setup DNS or a /etc/hosts entry to point local.coffeestats.org to the local host:

    .. code-block:: sh

        echo '127.0.0.1 local.coffeestats.org' >> /etc/hosts

#. Open http://local.coffeestats.org/ in a browser of your choice

Available Settings
==================

Coffeestats is configured by settings in the environment of the PHP processes.
For FastCGI/nginx these variables are configured by using `fastcgi_param
directives`_ as in the example above. You can accomplish the same effect for
Apache httpd with its `SetEnv directive`_.

.. _fastcgi_param directives: http://nginx.org/en/docs/http/ngx_http_fastcgi_module.html#fastcgi_param

.. _SetEnv directive: http://httpd.apache.org/docs/current/mod/mod_env.html#setenv

The setting name constants are defined in includes/common.php. The same file
contains a convenience method to retrieve settings from the server provided
environment.

The following sections lists the available settings and their meaning, for
example values have a look at the example nginx configuration above.

MySQL settings
--------------

``COFFEESTATS_MYSQL_HOSTNAME``
    hostname of the MySQL database to use


``COFFEESTATS_MYSQL_USER``
    user name for the MySQL database connection


``COFFEESTATS_MYSQL_PASSWORD``
    password for the MySQL database connection


``COFFEESTATS_MYSQL_DATABASE``
    name of the MySQL database to use


ReCAPTCHA settings
------------------

Coffeestats uses Google's ReCAPTCHA at registration time to make it harder to
do malicious automatic registrations. You have to get a key pair for the
ReCAPTCHA API from https://www.google.com/recaptcha/admin/create.


``COFFEESTATS_RECAPTCHA_PUBLICKEY``
    ReCAPTCHA API public key


``COFFEESTATS_RECAPTCHA_PRIVATEKEY``
    ReCAPTCHA API private key


Piwik settings
--------------

Coffeestats can use `Piwik`_ to track visitors. The Piwik functionality is
optional and is activated by defining ``COFFEESTATS_PIWIK_SITEID``.


``COFFEESTATS_PIWIK_HTTP_URL``
    address of a `Piwik`_ server for HTTP access


``COFFEESTATS_PIWIK_HTTPS_URL``
    address of a `Piwik`_ server for HTTPS access


``COFFEESTATS_PIWIK_SITEID``
    Piwik server's siteid for the coffeestats instance


.. _Piwik: http://piwik.org/


General settings
----------------

``COFFEESTATS_MAIL_FROM_ADDRESS``
    email address as defined in `RFC-2822`_ section 3.4 for mails sent from
    coffeestats


``COFFEESTATS_SITE_NAME``
    visible name of your coffeestats installation (i.e. for emails)


``COFFEESTATS_SITE_SECRET``
    site specific secret that is used to encrypt values. It is important to
    make this a unique value per site and keep it secret.


.. _RFC-2822: http://www.rfc-editor.org/rfc/rfc2822.txt


Unit tests
==========

There are some PHPUnit_ tests in devdocs/tests, the script
``devdocs/runtests.sh`` can be used to run the tests and to generate a coverage
report in ``devdocs/tests/testdocs/``.


.. _PHPUnit: http://phpunit.de/

Database migrations
===================

Coffeestats uses a database schema migration tool that is based on
php-database-migration_ by Alexandre GUIDET. If a database change is required
you have to perform the following steps:

#. Move to devdocs directory:

    .. code-block:: sh

        cd devdocs

#. Generate a new change SQL script:

    .. code-block:: sh

        ./php-database-migration/migrate --generate "My change description"

    This command generates the SQL script and writes the name of the generated
    file to the terminal, i.e.::

        migration: migrations/20130517145814_My change description.sql

#. Edit the generated SQL file using your editor of choice. Put forward and
   backward migration SQL code into the file. If no backward migration is
   possible you should write an appropriate SQL comment into the file

#. Make sure that your migration SQL works properly. It is suggested to test
   your SQL statements on a copy of your real development database

#. Run the migration against your database (requires the database environment
   variables to be set like shown above):

    .. code-block:: sh

        ./php-database-migration/migrate --up

#. Commit your new migration code to git and provide a meaningful commit
   comment:

    .. code-block:: sh

        git add migrations
        git commit


.. _php-database-migration: https://github.com/alwex/php-database-migration
