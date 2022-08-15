.. include:: /Includes.rst.txt


.. _introduction:

============
Introduction
============

This Extension helps you show Facebook, Instagram, Twitter or YouTube feeds on your website.

.. _what-it-does:

What does it do?
================

The ``pxa_social_feed`` extension uses a scheduler task to fetch/update posts from social networks. You should check if your hosting provider has correct cron job setup to run TYPO3 scheduler manager.

This Extension uses social networks official API solution to fetch posts.
**This means that you need to be able to access accounts** in order to get all access key/token.


.. important::

   After latest changes to Facebook/Instagram graph API you **can't obtain access tokens that are valid for more than 3 months**,
   it'll need to be updated manually.
   It's possible to setup an email reminder in scheduler task settings.

   **Facebook and Instagram require** your site to run with SSL.

