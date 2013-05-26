***********
Development
***********

To start development you need to :ref:`get the coffeestats code
<section-get-coffeestats>` and setup your database and webserver as described
in the chapter :doc:`deployment`.

Git branches
============

The canonical git repository for coffeestats has some branches that are
relevant:

.. _git-dev-branch:

.. todo::

   say something about the branches

Unit tests
==========

There are some PHPUnit_ tests in devdocs/tests, the script
:file:`devdocs/runtests.sh` can be used to run the tests and to generate a
coverage report in :file:`devdocs/tests/testdocs/`.

On Debian based systems you can install phpunit and all necessary dependencies
by running:

.. code-block:: sh

   sudo aptitude install phpunit

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

.. index:: authentication

.. _web authentication:

Authentication
==============

Authentication is handled via the PHP session. If a page requires an
authenticated user it sends a redirect with a Location header pointing to the
:ref:`login page <login page>`.

.. note::

   Authentication is handled differently by the :ref:`REST API <REST
   authentication>`.

.. index:: on the run authentication

The :ref:`on the run <on the run>` URL is authenticated using the login and a
token that is generated when a user's account is created.

Directory structure
===================

.. index:: directory /

Main directory
--------------

Contains entry points into the site

.. index:: about.php

:file:`about.php`

   :Purpose: about page
   :URL: /about
   :Access: authenticated users

.. index:: action.php

:file:`action.php`

   :Purpose: handler for actions usually sent via mail, redirects to other
      pages after use
   :URL: /action
   :Access: public

.. index:: compare.php

:file:`compare.php`

   :Purpose: future compare functionality
     (see `issue #23 <https://bugs.n0q.org/view.php?id=23>`_)
   :URL: /compare?u={login}
   :Access: authenticated users

.. index:: delete.php

:file:`delete.php`

   :Purpose: confirm deletion of a caffeine submission
   :URL: /delete?c={caffeineid}
   :Access: authenticted users

   .. note::

      Users can delete their own caffeine submissions only

.. index:: explore.php

:file:`explore.php`

   :Purpose: explore the site by seeing other users
   :URL: /explore
   :Access: authenticated users

.. index:: footer.php

:file:`footer.php`

   :Purpose: included by other pages to render the footer, redirects to /index
      immediately
   :URL: /footer
   :Access: public

   .. note::

      :file:`footer.php` should be moved to :ref:`includes directory
      <directory-includes>`.

.. index:: header.php

:file:`header.php`

   :Purpose: included by other pages to render the header, redirects to /index
      immediately
   :URL: /header
   :Access: public

   .. note::

      :file:`header.php` should be moved to :ref:`includes directory
      <directory-includes>`.

.. index:: imprint.php, imprint

:file:`imprint.php`

   :Purpose: the imprint page with legal information
   :URL: /imprint
   :Access: public

.. index:: index.php

:file:`index.php`

   :Purpose: index page, redirects to :ref:`login page <login page>` if user is
      not authenticated
   :URL: /
   :Access: public

.. index:: ontherun.php
.. _on the run:

:file:`ontherun.php`

   :Purpose: page for submitting caffeine on the run
   :URL: /ontherun?u={login}&t={token}
   :Access: valid login and token combination

.. index:: overall.php

:file:`overall.php`

   :Purpose: this page displays charts with aggregated statistics for all users
   :URL: /overall
   :Access: authenticated users

.. index:: profile.php

:file:`profile.php`

   :Purpose: show public user profile information
   :URL: /profile?u={login}
   :Access: public

   :Purpose: show own user profile and allow caffeine entry and deletion
   :URL: /profile
   :Access: authenticated user

   .. note::

      Users can access their own private profile page only

.. index:: public.php

:file:`public.php`

   :Purpose: deprecated just redirects to /profile?u={login}
   :URL: /public?u={login}
   :Access: public

   .. note::

      Could be replaced by web server configuration or dropped entirely

.. index:: selecttimezone.php

:file:`selecttimezone.php`

   :Purpose: allows a user to select a time zone, shown after initial login
   :URL: /selecttimezone
   :Access: authenticated user

   .. note::

      Users can access their own time zone selection page only

.. index:: settings.php

:file:`settings.php`

   :Purpose: allows users to modify their settings
   :URL: /settings
   :Access: authenticated user

   .. note::

      Should integrate time zone selection and change of public flag

.. index:: .gitignore

:file:`.gitignore`

   global ignore file with patterns that should be ignored by `git`_

.. _git: http://www.git-scm.com/

.. index:: directory /auth

Directory auth
--------------

.. index:: login.php, auth/login.php

.. _login page:

:file:`login.php`

.. index:: directory /api

Directory api
-------------

.. index:: directory /css

Directory css
-------------

.. index:: directory /devdocs

Directory devdocs
-----------------

.. index:: directory /fonts

Directory fonts
---------------

.. index:: directory /images

Directory images
----------------

.. index:: directory /includes

.. _directory-includes:

Directory includes
------------------

.. index:: directory /lib

Directory lib
-------------

.. index:: directory /templates

Directory templates
-------------------

.. index:: sass

CSS generation with Sass
========================

We use `Sass`_ to generate our Cascading Style Sheets (CSS) file. Sass is a CSS
generator feeded by a CSS like language. On Debian systems you can install Sass
by running:

.. code-block:: sh

   sudo aptitude install ruby-sass

On other systems with a Ruby Gems installation you can run:

.. code-block:: sh

   gem install sass

During development you can continuosly run :program:`sass` to generate the
:file:`css/caffeine.css`:

.. code-block:: sh

   sass --watch css/caffeine.scss:css/caffeine.css

You can also run :program:`sass` before committing your changes on
:file:`css/caffeine.scss` manually:

.. code-block:: sh

   sass css/caffeine.scss:css/caffeine.css

.. index:: caffeine.scss, caffeine.css

.. warning::

   Please be aware that all changes in :file:`css/caffeine.css` you make
   manually will be overwritten the next time somebody runs Sass. You should
   always modify :file:`css/caffeine.scss` instead.

.. _Sass: http://sass-lang.com/
