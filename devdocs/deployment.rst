**********
Deployment
**********

Requirements
============

Coffeestats requires the following software to run. The documentation assumes a
UNIX like operating system and you should know how to perform shell commands,
how to gain superuser (e.g. root) permissions on your system and how to use a
text editor.

* PHP
* `MySQL`_ >= 5.5
* You need `git`_ to get a copy of the coffeestats code
* Web server with FastCGI support

Coffeestats uses `PHP`_ and requires a PHP enabled web server. The tested
setups use either `php-fpm`_ or ``php-cgi`` in a FastCGI setup.

.. _MySQL: http://www.mysql.com/
.. _PHP: http://www.php.net/
.. _git: http://www.git-scm.com/
.. _php-fpm: http://php-fpm.org/

.. _note-envscript:

.. topic:: Environment script

   Some of the commands below expect a set of environment variables to be set.
   The documentation below expects that you have a file set you can source from
   your shell. The script could look like the following:

   .. code-block:: bash

      #!/bin/sh
      export COFFEESTATS_MYSQL_HOSTNAME=localhost
      export COFFEESTATS_MYSQL_DATABASE=coffeestats
      export COFFEESTATS_MYSQL_USER=coffeestats
      export COFFEESTATS_MYSQL_PASSWORD=mysqls3cRet

   The further documentation refers to this script as :file:`~/csenv.sh`.

.. _section-get-coffeestats:

Get coffeestats
===============

If you are reading this on the web and not from inside an existing coffeestats
checkout you will have to get the coffeestats code before continuing.

The canonical coffeestats code is hosted in a `git`_ repository by Florian
Baumann. To get access to this repository you will have to send your
credentials (ssh public key in openssh format) to him.

You can clone your initial copy with the following commands:

.. code-block:: sh

   export PROJECTS=/home/dev/projects
   cd $PROJECTS
   git clone -b dev git@o0.n0q.org:coffeestats

This command sequence performs a checkout of the branch :ref:`dev
<git-dev-branch>` after cloning the repository.

If you want to update your local repository to the latest code you just use

.. code-block:: sh

   git pull

in your coffeestats directory.

.. note::

   If you use some GUI tool you will have to specify the repository URI as
   git+ssh://git@o0.n0q.org:coffeestats and might have to specify the
   :ref:`dev <git-dev-branch>` branch manually.

.. _section-install-php:

Install PHP
===========

You need a PHP >= 5.3.7 installation with support for `mysqli`_ and `PDO`_. You
need to enable command line and CGI support. If you would like to use
`php-fpm`_ you will have to enable it too.

On Debian based systems you can get all of these requirements by performing:

.. code-block:: sh

   sudo aptitude install php5-cli php5-cgi php5-mysql php5-fpm

.. _mysqli: http://www.php.net/manual/en/book.mysqli.php
.. _PDO: http://www.php.net/manual/en/book.pdo.php

MySQL setup
===========

You should use a separate MySQL database for coffeestats. The following steps
have to be performed to initialize the database:

#. Install MySQL from either an operating system package or from source if you
   do not already have a MySQL server available. On a `Debian`_ based system
   you would do:

   .. code-block:: bash

      sudo aptitude install mysql-server mysql-client

#. Create a MySQL database for coffeestats and grant permissions to a new
   database user.

   Connect to the MySQL server [#fnphpmyadmin]_:

   .. code-block:: bash

      mysql -u root -h localhost -p mysql

   .. note::

      You will have to enter the password for your MySQL root user.

   .. note::

      Use the same parameters (username, password, database name, host name)
      that you specified in the environment script described
      :ref:`above <note-envscript>` for the following SQL statements.

   Create the new empty database:

   .. code-block:: mysql

      CREATE DATABASE coffeestats CHARACTER SET 'UTF8';

   Grant permissions to the coffeestats database to a new database user:

   .. code-block:: mysql

      GRANT ALL PRIVILEGES ON coffeestats.* TO
        coffeestats@localhost IDENTIFIED BY 'mysqls3cRet'

#. Coffeestats uses an adapted version of `php-database-migration`_ to handle
   changes to its database schema and to migrate data. For an initial setup of
   your database you run:

   .. code-block:: sh

      . ~/csdev.sh
      cd $PROJECTS/coffeestats/devdocs
      ./php-database-migration/migrate --up

.. _Debian: http://www.debian.org/
.. _php-database-migration: https://github.com/alwex/php-database-migration

Web server setup
================

While you can use any web server that supports PHP in FastCGI mode we recommend
to use the fast and lightweight `nginx`_ web server. If you already have `Apache
httpd`_ installed you can use this too. The following subsections provide
instructions how to setup both variants.

.. _nginx: http://nginx.org/
.. _Apache httpd: http://httpd.apache.org/

.. topic:: host name entry

   For the documented setup it is assumed that you have a proper DNS entry for
   local.coffeestats.org that points to your local host. If you do not have
   control over your network's DNS resolver you can help yourself by adding the
   following line to your :file:`/etc/hosts` file::

      127.0.0.1 local.coffeestats.org

.. topic:: file permissions

   You will have to make sure that your web server of choice has read
   permissions to your coffeestats checkout. This implies that it has at least
   execute permissions on the directory hierarchy above. You can achieve this
   with ACLs:

   .. code-block:: sh

      setfacl -m u:www-data:--x /home/dev
      find /home/dev/coffeestats -type d \
        -exec setfacl -d -m u:www-data:r-x {} \;
        -exec setfacl -m u:www-data:r-x \;
      find /home/dev/coffeestats -type f \
        -exec setfacl -m u:www-data:r-- {} \;

   Or by granting the required permissions to everybody:

   .. code-block:: sh

      chmod o+x /home/dev
      find /home/dev/coffeestats -type d -exec chmod o+rx {} \;
      find /home/dev/coffeestats -type r -exec chmod o+r {} \;

   The ACL approach is safer and should be preferred if your system supports
   ACLs.

Setup php-fpm
-------------

After installing PHP as described :ref:`above <section-install-php>` you have
everything ready to use php-fpm.

You should define an own pool of `php-fpm`_ workers for use by coffeestats. On
Debian based systems you would define this in a file inside
:file:`/etc/php5/fpm/pool.d`. For example you could have a file named
:file:`coffeestats.conf`:

.. code-block:: ini

   [coffeestats]
   user = youruser
   group = yourgroup
   ; use this if you want to use a UNIX domain socket
   listen = /var/run/php5-fpm-coffeestats.sock
   listen.owner = www-data
   listen.group = www-data
   ; use this instead if you want to use a TCP socket
   ; listen = 9000
   pm = dynamic
   pm.max_children = 5
   pm.start_servers = 2
   pm.min_spare_servers = 1
   pm.max_spare_servers = 3
   chdir = /

.. note::

   Replace ``youruser``, ``yourgroup`` and ``www-data`` with appropriate values
   for your system. ``youruser`` and ``yourgroup`` describe your own login that
   owns the local coffeestats working copy.

nginx
-----

This setup assumes that you will use `nginx`_ with `php-fpm`_. To install nginx
on a Debian based system you just run:

.. code-block:: sh

   sudo aptitude install nginx

You need to create a virtual host configuration for local.coffeestats.org. On
Debian based systems you will put a file named :file:`local.coffeestats.org`
into the :file:`/etc/nginx/sites-available/` directory:

.. code-block:: nginx

   server {
     listen 80;
     server_name local.coffeestats.org;

     root /home/dev/projects/coffeestats;
     access_log /var/log/nginx/coffeestats-access.log;
     error_log /var/log/nginx/coffeestats-error.log;

     location / {
       index index.php;
       try_files $uri $uri/ $uri.php?$args;
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
       fastcgi_pass unix:/var/run/php5-fpm-coffeestats.sock;
       fastcgi_index index.php;
       fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;

       fastcgi_param COFFEESTATS_MYSQL_HOSTNAME localhost;
       fastcgi_param COFFEESTATS_MYSQL_USER coffeestats;
       fastcgi_param COFFEESTATS_MYSQL_PASSWORD mysqls3cRet;
       fastcgi_param COFFEESTATS_MYSQL_DATABASE coffeestats;
       fastcgi_param COFFEESTATS_RECAPTCHA_PUBLICKEY yourrecaptchapublickey;
       fastcgi_param COFFEESTATS_RECAPTCHA_PRIVATEKEY yourrecaptchaprivatekey;
       fastcgi_param COFFEESTATS_PIWIK_SITEID piwiksiteid;
       fastcgi_param COFFEESTATS_PIWIK_HOST piwik.example.org;
       fastcgi_param COFFEESTATS_MAIL_FROM_ADDRESS no-reply@coffeestats.org;
       fastcgi_param COFFEESTATS_SITE_SECRET somerandomstring;
       fastcgi_param COFFEESTATS_SITE_ADMINMAIL team@coffeestats.org;
       fastcgi_param COFFEESTATS_SITE_NAME "coffeestats.org development";
     }
   }

.. note::

   The parameters prefixed with ``COFFEESTATS_`` used in the ``fastcgi_param``
   directives above are explained in the section
   :ref:`section-available-settings` below.

After you setup the file you have to enable it in your nginx configuration. On
Debian systems you put a symbolic link into :file:`/etc/nginx/sites-enabled`:

.. code-block:: sh

   cd /etc/nginx/sites-enabled
   sudo ln -s ../sites-available/local.coffeestats.org

You can now restart nginx by running:

.. code-block:: sh

   sudo /etc/init.d/nginx restart

You should now be able to open http://local.coffeestats.org/ in a browser of
your choice.

Apache httpd with mod-fastcgi
-----------------------------

This setup assumes that you will use `Apache httpd`_ with `php-fpm`_ and
`mod-fastcgi`_. To install Apache httpd and mod-fastcgi on a Debian based
system you just run:

.. code-block:: sh

   sudo aptitude install apache2 apache2-mpm-worker libapache2-mod-fastcgi

.. note::

   If you want to use an existing Apache httpd (i.e. something like XAMPP or
   MAMP) you need to make sure that you have mod-fastcgi available

.. _mod-fastcgi: http://www.fastcgi.com/mod_fastcgi/docs/mod_fastcgi.html

You need to create a virtual host configuration for local.coffeestats.org. On
Debian based systems you will put a file named :file:`local.coffeestats.org`
into the :file:`/etc/apache2/sites-available/` directory:

.. code-block:: apacheconf

   <Directory /home/dev/projects/coffeestats>
     DirectoryIndex index.php
     Options -Indexes
     AllowOverride none
     Order deny,allow
     Allow from all
   </Directory>

   <VirtualHost 127.0.0.1:80>
     ServerName local.coffeestats.org
     DocumentRoot /home/dev/projects/coffeestats

     FastCGIExternalServer /usr/sbin/php5-fpm -socket /var/run/php5-fpm-coffeestats.sock
     AddHandler php-script .php
     Action php-script /usr/sbin/php5-fpm.cgi
     ScriptAlias /usr/sbin/php5-fpm.cgi /usr/sbin/php5-fpm

     RewriteEngine on

     RewriteCond %{REQUEST_URI} ^/api/([^/]+)/(.*)$
     RewriteCond %{REQUEST_FILENAME} !-f
     RewriteCond %{REQUEST_FILENAME} !-d
     RewriteRule ^(.*)$ /api/api-%1.php?q=%2 [L,QSA]

     RewriteCond %{REQUEST_FILENAME} !-f
     RewriteCond %{DOCUMENT_ROOT}%{REQUEST_FILENAME}.php -f
     RewriteRule ^/(.*) /$1.php [L,QSA]

     SetEnv COFFEESTATS_MYSQL_HOSTNAME localhost
     SetEnv COFFEESTATS_MYSQL_USER coffeestats
     SetEnv COFFEESTATS_MYSQL_PASSWORD mysqls3cRet
     SetEnv COFFEESTATS_MYSQL_DATABASE coffeestats

     SetEnv COFFEESTATS_PIWIK_HOST piwik.example.org
     SetEnv COFFEESTATS_PIWIK_SITEID piwiksiteid

     SetEnv COFFEESTATS_SITE_SECRET somerandomstring
     SetEnv COFFEESTATS_SITE_NAME "coffeestats.org development"
     SetEnv COFFEESTATS_SITE_ADMINMAIL "team@coffeestats.org"
     SetEnv COFFEESTATS_MAIL_FROM_ADDRESS "no-reply@cofeestats.org"

     SetEnv COFFEESTATS_RECAPTCHA_PUBLICKEY yourrecaptchapublickey
     SetEnv COFFEESTATS_RECAPTCHA_PRIVATEKEY yourrecaptchaprivatekey

     CustomLog /var/log/apache2/coffeestats-access.log combined
     ErrorLog /var/log/apache2/coffeestats-error.log
   </VirtualHost>

.. note::

   The SetEnv directive parameters prefixed with ``COFFEESTATS_`` are explained
   in the section :ref:`section-available-settings` below.

After you setup the file you have to enable it in your Apache httpd
configuration. On Debian systems you enable it using:

.. code-block:: sh

   sudo a2ensite local.coffeestats.org

You can now restart Apache httpd by running:

.. code-block:: sh

   sudo /etc/init.d/apache2 restart

You should now be able to open http://local.coffeestats.org/ in a browser of
your choice.

.. _section-available-settings:

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


``COFFEESTATS_PIWIK_HOST``
    hostname of a `Piwik`_ server


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


``COFFEESTATS_SITE_ADMINMAIL``
    email address where emails to the administrators are sent to.


.. _RFC-2822: http://www.rfc-editor.org/rfc/rfc2822.txt


.. rubric:: Footnotes

.. [#fnphpmyadmin] You may also use a tool like
      `PHPMyAdmin <http://www.phpmyadmin.net/>`_ to execute the SQL statements
