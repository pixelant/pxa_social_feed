.. include:: /Includes.rst.txt


.. _for-editors:

===========
For Editors
===========

Target group: **Editors**

How to get feed visible on page.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Add plugin to page
""""""""""""""""""

Create a new content element and choose **Social feed**


.. figure:: /Images/UserManual/NewElementWizard.png
   :class: with-shadow
   :alt: Backend view

   New content element

Configure plugin
""""""""""""""""

There are plugin options you need to setup first:

- Load type: *Standard* - just render as regular plugin. *Ajax* - load feed with ajax.
- Presentation: Two options with what JS library to use to render feed.
- Appearance of feed items: *Card* or *Dynamic* partial.
- Amount of feeds on page: *limit how many items to show at once*
- Load likes count: *show/hide likes count*
- Choose configuration for plugin: *what feed source to show*

.. tip::

   Standard loading vs Ajax

``Standard`` loading will force page to be no-cache for browser, this is not good in case you want to have plugin on main page.
Use ``Ajax`` instead, which will use cache able plugin action and load feed with no-cache request.

.. important::

   You should choose at least one feed source

.. figure:: /Images/UserManual/PluginOptions.png
   :class: with-shadow
   :alt: Backend view

   New content element



