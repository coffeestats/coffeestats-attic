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

.. index:: unit tests

.. _unit tests:

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

.. _database migrations:

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

The :ref:`on the run <on the run>` URI is authenticated using the login and a
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
   :URI: /about
   :Access: authenticated users

.. index:: action.php

.. _URI /action:

:file:`action.php`

   :Purpose: handler for actions usually sent via mail, redirects to other
      pages after use
   :URI: /action
   :Access: public

.. index:: compare.php

:file:`compare.php`

   :Purpose: future compare functionality
     (see `issue #23 <https://bugs.n0q.org/view.php?id=23>`_)
   :URI: /compare?u={login}
   :Access: authenticated users

.. index:: delete.php

:file:`delete.php`

   :Purpose: confirm deletion of a caffeine submission
   :URI: /delete?c={caffeineid}
   :Access: authenticted users

   .. note::

      Users can delete their own caffeine submissions only

.. index:: explore.php

:file:`explore.php`

   :Purpose: explore the site by seeing other users
   :URI: /explore
   :Access: authenticated users

.. index:: footer.php

:file:`footer.php`

   :Purpose: included by other pages to render the footer, redirects to /index
      immediately
   :URI: /footer
   :Access: public

   .. note::

      :file:`footer.php` should be moved to :ref:`includes directory
      <directory-includes>`.

.. index:: header.php

:file:`header.php`

   :Purpose: included by other pages to render the header, redirects to /index
      immediately
   :URI: /header
   :Access: public

   .. note::

      :file:`header.php` should be moved to :ref:`includes directory
      <directory-includes>`.

.. index:: imprint.php, imprint

:file:`imprint.php`

   :Purpose: the imprint page with legal information
   :URI: /imprint
   :Access: public

.. index:: index.php

:file:`index.php`

   :Purpose: index page, redirects to :ref:`login page <login page>` if user is
      not authenticated
   :URI: /
   :Access: public

.. index:: ontherun.php
.. _on the run:

:file:`ontherun.php`

   :Purpose: page for submitting caffeine on the run
   :URI: /ontherun?u={login}&t={token}
   :Access: valid login and token combination

.. index:: overall.php

:file:`overall.php`

   :Purpose: this page displays charts with aggregated statistics for all users
   :URI: /overall
   :Access: authenticated users

.. index:: profile.php

.. _profile page:

:file:`profile.php`

   :Purpose: show public user profile information
   :URI: /profile?u={login}
   :Access: public

   :Purpose: show own user profile and allow caffeine entry and deletion
   :URI: /profile
   :Access: authenticated user

   .. note::

      Users can access their own private profile page only

.. index:: public.php

:file:`public.php`

   :Purpose: deprecated just redirects to /profile?u={login}
   :URI: /public?u={login}
   :Access: public

   .. note::

      Could be replaced by web server configuration or dropped entirely

.. index:: selecttimezone.php

:file:`selecttimezone.php`

   :Purpose: allows a user to select a time zone, shown after initial login
   :URI: /selecttimezone
   :Access: authenticated user

   .. note::

      Users can access their own time zone selection page only

.. index:: settings.php

:file:`settings.php`

   :Purpose: allows users to modify their settings
   :URI: /settings
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

.. index:: changepassword.php, auth/changepassword.php

:file:`changepassword.php`

   :Purpose: change a user's password after a password reset request
      this is triggered by a call to :ref:`URI /action <URI /action>`
   :URI: /auth/changepassword
   :Access: public

   .. note::

      The user will be redirected to the :ref:`login page <login page>` if the
      URI is accessed outside its normal usage pattern.

.. index:: lock.php, auth/lock.php

:file:`lock.php`

   :Purpose: make sure that the user is logged in
   :URI: /auth/lock.php
   :Access: public

   .. note::

      This file should not be accessible on its own and should be moved to the
      :ref:`directory-includes`

.. index:: login.php, auth/login.php

.. _login page:

:file:`login.php`

   :Purpose: ask the user for login and password and perform authentication
   :URI: /auth/login
   :Access: public

.. index:: logout.php, auth/logout.php

:file:`logout.php`

   :Purpose: terminates a user's session and redirects to the :ref:`login page
      <login page>`. Does nothing but redirect for anonymous users.
   :URI: /auth/logout
   :Access: public

.. index:: passwordreset.php, auth/passwordreset.php

:file:`passwordreset.php`

   :Purpose: starts the password reset workflow by asking the user to enter an
      email address. If the email address is associated with a known user an
      email with a password reset link is sent by calling
      :php:func:`send_reset_password_link` from :file:`includes/common.php`
   :URI: /auth/passwordreset
   :Access: public

.. index:: register.php, auth/register.php

:file:`register.php`

   :Purpose: allows a user to register an account for coffeestats. The user is
      asked for a username, a password, an email address and some optional
      information (firstname, lastname, location). If the input is successfully
      validated the user gets an email with a validation link that is generated
      by :php:func:`send_mail_activation_link` from :file:`includes/common.php`
   :URI: /auth/register
   :Access: public

.. index:: directory /api

Directory api
-------------

.. index:: api-v1.php, api/api-v1.php

:file:`api-v1.php`

   :Purpose: implements the REST API described in :doc:`api`
   :URI: /api/v1/{parameters}
   :Access: mixed

.. index:: directory /css

.. _css directory:

Directory css
-------------

.. index:: caffeine.css, css/caffeine.css

:file:`caffeine.css`

   :Purpose: generated Cascading Style Sheet file
   :URI: /css/caffeine.css
   :Access: public

.. index:: caffeine.scss, css/caffeine.scss

:file:`caffeine.scss`

   :Purpose: source `Sass` file for generating :file:`caffeine.css`
      see :ref:`css generation` below for details.
   :URI: /css/caffeine.scss
   :Access: public

.. index:: directory /devdocs

Directory devdocs
-----------------

.. note::

   The devdocs hierarchy contains development related files and should be
   protected by the web server in production.

.. index:: api.rst, devdocs/api.rst

:file:`api.rst`

   Documentation for API, used to generate :doc:`api`

.. index:: conf.py, devdocs/conf.py

:file:`conf.py`

   Sphinx_ configuration file

.. _Sphinx: http://sphinx-doc.org/

.. index:: deployment.rst, devdocs/deployment.rst

:file:`deployment.rst`

   Deployment instructions, used to generate :doc:`deployment`

.. index:: development.rst, devdocs/development.rst

:file:`development.rst`

   Development guide, used to generate :doc:`this <development>`

.. index:: index.rst, devdocs/index.rst

:file:`index.rst`

   Documentation index, used to generate :doc:`index`

.. index:: Makefile, devdocs/Makefile

:file:`Makefile`

   Makefile generated by :program:`sphinx-quickstart` and used to generate
   documentation in various formats

.. index:: runtests.sh, devdocs/runtests.sh

:file:`runtests.sh`

   Shell script to run :ref:`unit tests`.

.. index:: directory /devdocs/migrations

Directory devdocs/migrations
----------------------------

This directory contains the SQL files used for :ref:`database migrations`.

.. index:: directory /devdocs/php-database-migration

Directory devdocs/php-database-migration
----------------------------------------

This directory contains the modified version of the `php-database-migration
tool <php-database-migration>`_ the main file is :file:`migrate` and is used as
documented in the section :ref:`database migrations`.

.. _php-database-migration: https://github.com/alwex/php-database-migration

.. index:: directory /devdocs/tests

Directory devdocs/tests
-----------------------

.. index:: bootstrap.php, devdocs/tests/bootstrap.php

:file:`bootstrap.php`

   This file contains bootstrapping code for phpunit (see the description of
   the --bootstrap parameter at
   http://www.phpunit.de/manual/current/en/textui.html)

.. index:: ValidationTest.php, devdocs/tests/ValidationTest.php

:file:`ValidationTest.php`

   This file contains the unit tests for :file:`includes/validation.php`.

.. index:: directory /fonts

Directory fonts
---------------

This directory contains the fonts that are included as web fonts:

* :file:`DroidSans-Bold.ttf` and :file:`DroidSans.ttf`

  The Droid Sans font in bold and normal width.

* :file:`Harabara.ttf`

  The Harabara font.

Both fonts are referenced from the (s)css file in the :ref:`css directory`.

.. index:: directory /images

Directory images
----------------

This directory contains the images used on the site.

.. index:: directory /includes

.. _directory-includes:

Directory includes
------------------

This directory contains code that is meant to be included and used by other
files.

.. index:: charting.php, includes/charting.php

File :file:`charting.php`
^^^^^^^^^^^^^^^^^^^^^^^^^

This file contains helper functions to format chart data. All functions in this
file operate on associative arrays of the following structure:

.. code-block:: php

   $assocarray = array(
      'label1' => array(
         0 => 10,
         1 => 8,
         ...),
      'label2' => array(
         0 => 0,
         1 => 5,
         ...),
      ...);

.. php:function:: extractlabels(&$assocarray: array) -> array

   Extract the labels from the given array.

   :param array &$assocarray: associative array containing labels and data lists
   :returns: formatted list of labels

.. php:function:: extractdata(&$assocarray: array, $field: int) -> array

   Extract the specified data row from the given array.

   :param array &$assocarray: associative array containing labels and data lists
   :param int $field: integer value specifying the interesting data
   :returns: formatted list of values for the interesting field

.. php:function:: scalesteps(&$dataarray: array) -> int

   Find the maximum value from the data rows in the given array.

   :param array &$dataarray: associative array containing labels and data lists
   :returns: maximum value from all the values in the input array

This file also contains a set of JavaScript function definitions in addition to
the above PHP functions.

.. js:function:: drawBarChart(canvasid, data, scaleSteps)

   This function draws a bar chart with a calculated integer scale using the
   given data onto the specified canvas element.

   :param string canvasid: DOM id of a HTML5 canvas
   :param array data: data array as expected by the `chartjs`_ bar chart
      constructor
   :param scaleSteps: maximum scale value used

.. js:function:: drawLineChart(canvasid, data, scaleSteps)

   This function draws a line chart with a calculated integer scale using the
   given data onto the specified canvas element.

   :param string canvasid: DOM id of a HTML5 canvas
   :param array data: data array as expected by the `chartjs`_ line chart
      constructor
   :param scaleSteps: maximum scale value used

.. _chartjs: http://www.chartjs.org/

.. index:: common.php, includes/common.php

File :file:`common.php`
^^^^^^^^^^^^^^^^^^^^^^^

This file contains functions to perform most of the functionality of
coffeestats.

.. index:: flash message system

.. rubric:: Flash message system

The flash message system implements a way to notify a user of performed
actions, success messages, errors and warnings in a uniform way. Multiple
categories of flash messages can be used to display these on different parts of
the page.

The usual way to implement flash messages is to call the :php:func:`flash`
function at the origin of the notification:

.. code-block:: php

   <?php
   flash('demonstrate flash', FLASH_SUCCESS);
   ?>

and to use the :php:func:`render_flash` function in the HTML template code:

.. code-block:: php

   <html>
     <head><title>Flash message demo</title></head>
     <body>
       <h1>Flash message demo</h1>
       <?php render_flash('system'); ?>
     </body>
   </html>

which will render to the following HTML:

.. code-block:: html

   <html>
     <head><title>Flash message demo</title></head>
     <body>
       <h1>Flash message demo</h1>
       <ul class="flash-messages" id="system-flash">
         <li class="flash-success">demonstrate flash
           <a href="#" class="close">X</a></li>
       </ul>
     </body>
   </html>

and can be styled using CSS and handled with JavaScript if necessary.

.. php:const:: FLASH_INFO

   indicate a flash message of severity INFO

.. php:const:: FLASH_SUCCESS

   indicate a flash message of severity SUCCESS

.. php:const:: FLASH_ERROR

   indicate a flash message of severity ERROR

.. php:const:: FLASH_WARNING

   indicate a flash message of severity WARNING

.. php:function:: flash($message: string, [$level=FLASH_INFO: string, $category='system': string])

   Post a flash message to the flash message system.

   :param string $message: the message to be displayed
   :param string $level: level/severity of the flash message, defaults to
      :php:const:`FLASH_INFO`
   :param string $category: the category of the flash message, defaults to 'system'

.. php:function:: peek_flash([$category='system': string]) -> boolean

   Check the availability of flash messages in the given category.

   :param string $category: the category of the flash message, defaults to 'system'
   :return: :php:const:`TRUE` if there are available flash messages for the given category

.. php:function:: pop_flash([$category='system': string]) -> string

   Get the first message of the given category from the flash message stack.

   :param string $category: the category of the flash message, defaults to 'system'
   :return: the message or :php:const:`NULL` if the stack is empty

.. php:function:: render_flash($category: string)

   Render the HTML code for all flash messages in the given category.

   :param string $category: the category of the flash messages

.. rubric:: HTTP and error helper functions

.. php:function:: redirect_to($url: string, [$permanent=FALSE: boolean])

   Perform an HTTP redirect to the given URL.

   :param string $url: target of the redirect
   :param boolean $permanent: whether the redirect should be marked as
      permanent (HTTP status code 301 Redirect Permanently)

.. php:function:: errorpage($title: string, $text: string, [$http_status=NULL: string])

   Render an error page with a common look and feel.

   :param string $title: the page title, rendered as headline
   :param string $text: some text explaining the error
   :param string $http_status: code and text for the Status header, no Status
      header is created if this is :php:const:`NULL`

.. rubric:: Settings handling

.. index:: settings constants, setting names

.. php:const:: MAIL_FROM_ADDRESS

   setting name constant for mail from address setting (refers to
   :envvar:`COFFEESTATS_MAIL_FROM_ADDRESS`)

.. php:const:: MYSQL_DATABASE

   setting name constant for MySQL database name (refers to
   :envvar:`COFFEESTATS_MYSQL_DATABASE`)

.. php:const:: MYSQL_HOSTNAME

   setting name constant for MySQL database server hostname (refers to
   :envvar:`COFFEESTATS_MYSQL_HOSTNAME`)

.. php:const:: MYSQL_PASSWORD

   setting name constant for MySQL database password (refers to
   :envvar:`COFFEESTATS_MYSQL_PASSWORD`)

.. php:const:: MYSQL_USER

   setting name constant for MySQL database user (refers to
   :envvar:`COFFEESTATS_MYSQL_USER`)

.. php:const:: PIWIK_HOST

   setting name constant for the Piwik server hostname (refers to
   :envvar:`COFFEESTATS_PIWIK_HOST`)

.. php:const:: PIWIK_SITE_ID

   setting name constant for the Piwik site id (refers to
   :envvar:`COFFEESTATS_PIWIK_SITEID`)

.. php:const:: RECAPTCHA_PRIVATEKEY

   setting name constant for the `ReCAPTCHA`_ API private key (refers to
   :envvar:`COFFEESTATS_RECAPTCHA_PRIVATEKEY`)

.. php:const:: RECAPTCHA_PUBLICKEY

   setting name constant for the `ReCAPTCHA`_ API public key (refers to
   :envvar:`COFFEESTATS_RECAPTCHA_PUBLICKEY`)

.. php:const:: SITE_SECRET

   setting name constant for the site secret (refers to
   :envvar:`COFFEESTATS_SITE_SECRET`)

.. php:const:: SITE_NAME

   setting name constant for the site name (refers to
   :envvar:`COFFEESTATS_SITE_NAME`)

.. php:const:: SITE_ADMINMAIL

   setting name constant for the site administrator mail address (refers to
   :envvar:`COFFEESTATS_SITE_ADMINMAIL`)

.. php:global:: $ACTION_TYPES

   Map that maps action names to numeric action type identifiers.

.. php:global:: $ENTRY_TYPES

   Map from numeric caffeine entry type constants to human readable names.

.. index:: retrieve settings, settings, setting system

.. php:function:: get_setting($setting_name: string, [$mandatory=TRUE: boolean]) -> string

   Get the setting with the given name from the process' environment.

   :param string $setting_name: one of the setting constant names above
   :param string $mandatory: create an errorpage if the parameter is not
      available and this parameter is :php:const:`TRUE`
   :returns: the setting value or :php:const:`NULL` if $mandatory is
      :php:const:`FALSE` and the setting is not defined

.. rubric:: URI helper functions

.. php:function:: baseurl() -> string

   Get the base URI for constructing links to this coffeestats installation.

   :returns: a base URI with correct protocol, hostname and port specification

.. php:function:: public_url($username: string) -> string

   Get the absolute URI of the given user's public :ref:`profile page <profile
   page>`.

   :param string $username: a user login
   :returns: an absolute URI to the user's public profile page

.. php:function:: on_the_run_url($profileuser: string, $profiletoken: string) -> string

   Get the absolute URI of the given user's :ref:`on-the-run page <on the run>`.

   :param string $profileuser: a user login
   :param string $profiletoken: a corresponding on-the-run token (see :ref:`web
      authentication`)
   :returns: an absolute URI to the user's on-the-run page

.. php:function:: profilelink($username: string) -> string

   Get the HTML code for a link to the user's profile page.

   :param string $username: a user login
   :returns: properly escaped HTML a tag

.. rubric:: helper functions

.. php:function:: random_chars($charset: string, $charcount: int) -> string

   Generate a randomly chosen string with the given count of characters from
   the given character set.

   :param string $charset: string with characters to choose from
   :param int $charcount: number of characters to be chosen
   :returns: string of length $charcount

.. php:function:: hash_password($password: string) -> string

   Hash a given password with a random salt and the blowfish algorithm.

   :param string $password: the clear text password
   :returns: hashed password value

.. rubric:: mail and action related functions

.. php:function:: send_system_mail($to: string, $subject: string, $body: string, [&$files=NULL: array)

   Send a mail from the address defined in setting
   :php:const:`MAIL_FROM_ADDRESS` to the given recipient address. Use the given
   subject and body, and attach the given files if there are any.

   :param string $to: an email address
   :param string $subject: the mail subject
   :param string $body: the mail body text or text of the first (text/plain)
      MIME body part
   :param array &$files: reference to an array describing the files that should
      be attached to the mail the array has the following structure:

      .. code-block:: php

         $files = array(
            array(
               'realfile' => 'filename on filesystem',
               'filename' => 'filename in email',
               'description' => 'description in email',
               'content-type' => 'MIME content type'),
            ...);

.. php:function:: send_caffeine_mail($to: string, &$files: array)

   Send a mail with template the caffeine usage exports in the given files to
   the given email address:

   :param string $to: an email address
   :param array &$files: a reference to an array in the form accepted by
      :php:func:`send_system_mail`

.. php:function:: generate_actioncode($data: string) -> string

   Generate a random action code based on the value of the setting
   :php:const:`SITE_SECRET`, a random number and the given data.

   :param string $data: data that will be used for the given action
   :returns: MD5 hash of random data

.. php:function:: create_action_entry($cuid: int, $action_type: string, $data: string) -> string

   Create an entry in the actions database table. Uses
   :php:func:`generate_actioncode` to generate an action code.

   :param int $cuid: user id of the user for whom the action is meant
   :param string $action_type: one of the keys in :php:global:`$ACTION_TYPES`
   :returns: the action code for the generated action or :php:const:`FALSE` if a
      wrong action type was passed into the function

.. php:function:: get_action_url($actioncode: string) -> string

   Get the absolute action URI for the given action code.

   :param string $actioncode: an action code (i.e. from a call to
      :php:func:`create_action_entry`)
   :returns: an absolute URI to the :ref:`action page <URI /action>`

.. php:function:: fill_mail_template($templatename: string, $placeholders: array) -> string

   Fill the given mail template in :ref:`directory templates` with the given
   set of place holders.

   A template :file:`templates/hello.txt` like the following:

   .. code-block:: text

      Hello @planet@

   and a call to this function:

   .. code-block:: php

      <?php
      $placeholders = array('planet' => 'world');
      print fill_mail_template('hello', $placeholders);
      ?>

   would generate the output ``Hello world``.

   :param string $templatename: file basename (without directory or file
      extension) that is relative to the templates directory
   :param array $placeholders: associative array mapping place holder names to
      their corresponding values
   :returns: the template text with replaced place holder strings

   .. note::

      The function has no functionality to check for any place holders missing
      in the given place holder array.

.. php:function:: send_mail_activation_link($email: string)

   Send a mail with an activation link to the given email address. This
   function uses the template :file:`templates/activate_mail.txt`.

   :param string $email: email address

.. php:function:: send_reset_password_link($email: string)

   Send a password reset link to the given email address. This function uses
   the template :file:`templates/reset_password.txt`.

   :param string $email: email address

.. php:function:: send_change_email_link($email: string, $uid: int)

   Send an email change confirmation link to the given email address to confirm
   the change of the given user's email address. This function uses the
   template file :file:`templates/mail.txt`.

   :param string $email: email address
   :param int $uid: user id

.. php:function:: send_user_deletion($user: string, $id: int)

   Send a message containing a user's deletion request to the site
   administrator address specified in setting :php:const:`SITE_ADMINMAIL`.
   This function uses the template :file:`templates/delete_user.txt`.

.. php:function:: format_timezone($timezone: string) -> string

   Format a time zone value for output.

   :param string $timezone: time zone value or :php:const:`NULL`
   :returns: the empty string or a formatted time zone value

.. php:function:: register_coffee($uid: int, $coffeetime: string, $timezone: string)

   Register a new coffee. Uses :php:func:`find_recent_caffeine` and
   :php:func:`create_caffeine` from :file:`includes/queries.php`.

   :param int $uid: user id
   :param string $coffeetime: a string with a datetime specification
   :param string $timezone: a time zone name

.. php:function:: register_mate($uid: int, $matetime: string, $timezone: string)

   Register a new mate. Uses :php:func:`find_recent_caffeine` and
   :php:func:`create_caffeine` from :file:`includes/queries.php`.

   :param int $uid: user id
   :param string $matetime: a string with a datetime specification
   :param string $timezone: a time zone name

.. php:function:: get_entrytype($entrytype: int) -> string

   Return a human readable name for the given numeric caffeinated drink type.
   This function uses :php:global:`$ENTRY_TYPES`.

   :param int $entrytype: numeric drink type
   :returns: human readable caffeinated drink name or 'unknown'

.. php:function:: load_user_profile($loginid: int) -> array

   Load user profile information for the given user id.

   :param int $loginid: user id
   :returns: associative array with the keys 'login', 'firstname', 'lastname',
      'location', 'email' and 'timezone'

.. _ReCAPTCHA: https://www.google.com/recaptcha/

.. index:: jsvalidation.php, includes/jsvalidation.php

File :file:`jsvalidation.php`
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. index:: queries.php, includes/queries.php

File :file:`queries.php`
^^^^^^^^^^^^^^^^^^^^^^^^

.. index:: validation.php, includes/validation.php

File :file:`validation.php`
^^^^^^^^^^^^^^^^^^^^^^^^^^^


.. index:: directory /lib

Directory lib
-------------

.. index:: directory /templates

.. _directory templates:

Directory templates
-------------------

.. index:: sass

.. _css generation:

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
