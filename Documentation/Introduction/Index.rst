.. include:: ../Includes.txt


.. _introduction:

============
Introduction
============

Extension is about to help you to show Facebook, Instagram, Twitter or Youtube feed on the site.

.. _what-it-does:

What does it do?
================

The ``pxa_social_feed`` use scheduler task to fetch/update posts from social networks, check if your hosting provider has correct cron job setup to run TYPO3 scheduler manager.

Extension use social networks official API solution to fetch posts.
**This means that you need to be able to access accounts** in order to get all access key/token.


.. important::

   After latest changes to Facebook/Instagram graph API you **can't obtain access token that is valid more than 3 month**,
   it'll need to be update manually.
   It's possible to setup email reminder in scheduler task settings.

   **Facebook and Instagram require** your site to run with SSL.

