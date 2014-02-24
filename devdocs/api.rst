********************
Coffeestats REST API
********************

Coffeestats provides a REST API to be used by third party applications.

Base URI
========

The API is hosted at /api/v1 and provides several resources that are described
in detail :ref:`below <section-resources>`.

.. code-block:: sh

   curl http://coffeestats.org/api/v1/$Resource

.. index:: authentication

.. _rest authentication:

Authentication
==============

For authentication the username and on-the-run token are used. You can see both used as GET Parameters for
the bookmarkable on-the-run link on your profile page.

.. code-block:: sh

   curl -X POST -d "u=user&t=yourtokenhere" https://coffeestats.org/api/v1/$Resource

This is of course an incomplete example. See :ref:`below <section-resources>` for detailed ressources

.. _section-resources:

Resources
=========


random-users
------------

You can query a set of random users from the api

.. code-block:: sh

  curl -X POST -d "u=user&t=yourtokenhere" https://coffeestats.org/api/v1/random-users |python -mjson.tool
  [
    {
        "coffees": "42",
        "location": "baz",
        "mate": "0",
        "name": "foobar",
        "profile": "https://coffeestats.org/profile?u=foobar",
        "username": "foobar"
    },
   [...]

Returns 5 random users by default. Required POST parameters:

* u=$user
* t=$token


add-drink
---------

You can add a drink i.e. a mate to your account. There are two additional parameters requried. beverage and time.

.. code-block:: sh

   curl -X POST -d "u=user&t=yourtokenhere&beverage=mate&time=2014-02-24 19:46:30" https://coffeestats.org/api/v1/add-drink |python -mjson.tool
   {
    "success": true
   }

Time format has to be `%F %H:%M:%s`. See below for example.

Required POST parameters:

* u=$user
* t=$token
* beverage=(mate|coffee)
* time=2014-02-24 19:46:30
