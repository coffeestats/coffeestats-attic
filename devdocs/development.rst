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

CSS generation with SASS
========================

.. todo::

   document SASS
