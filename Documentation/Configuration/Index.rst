.. include:: ../Includes.txt


.. _configuration:

=============
Configuration
=============

Target group: **Developers, Integrators**


Setup tokens
^^^^^^^^^^^^

**Before create configuration, it's required to have access token.**
To create new ``Token`` go **Social Feed** backend module, switch to "Tokens" tab and press "Add new" button.

In opened form, choose **Type** of token you want to create:

- Facebook
- Instagram
- Twitter
- Youtube

Facebook token
""""""""""""""

You should be logged in to facebook account.

.. important::

   Maximum life time of Facebook access token is 3 month. After that it need to be generate again manually in BE.

**Before adding access token** we need to add **App Id** and **App Secret**.

.. figure:: ../Images/AdministratorManual/FacebookToken.png
   :class: with-shadow
   :alt: Backend view

   Facebook token

To get those go to apps page https://developers.facebook.com/apps/ and open your App. If you don't have any yet, create it.
Copy ID and Secret from app settings.

.. figure:: ../Images/AdministratorManual/AppIdSecret.png
   :class: with-shadow
   :alt: Backend view

   App Id and Secret


After Id and Secret were added you can generate access token.

**But first add redirect url** to field "Valid OAuth Redirect URIs" of "Facebook login" settings in your app.

There is a button "Copy fallback URL for facebook settings." to copy url that need to insert in "Facebook login" settings.

.. figure:: ../Images/AdministratorManual/CopyAccessToken.png
   :class: with-shadow
   :alt: Backend view

   Redirect url

.. figure:: ../Images/AdministratorManual/FacebookLogin.png
   :class: with-shadow
   :alt: Backend view

   Facebook login

After redirect URL was added in the Valid OAuth redirect URIs **press "Generate access token"** button. Follow instructions.

**If everything went well you should get "Access token" that will be saved automatically.**

.. figure:: ../Images/AdministratorManual/GotToken.png
   :class: with-shadow
   :alt: Backend view

   Token ready

Instagram token
"""""""""""""""

**Instagram is now using Facebook graph API.**

Setup of Instagram token is same as for Facebook with only difference that you **need to have Instagram Bussines account** and **Facebook page connected to that account**.

Read documentation section "Before You Start"

`Official documentation <https://developers.facebook.com/docs/instagram-api/getting-started>`_

After you managed to get Facebook page connected to your Instagram Bussines account, **do same steps as for Facebook token**

Twitter token
"""""""""""""

`Authentication documentation <https://developer.twitter.com/en/docs/basics/authentication/overview>`_

Twitter token require next values:

- Api Key
- Api Secret
- Access Token
- Access Token Secret

.. figure:: ../Images/AdministratorManual/TwitterToken.png
   :class: with-shadow
   :alt: Backend view

   Twitter Token


You can find it in you Twitter App settings.
https://developer.twitter.com/en/apps

.. figure:: ../Images/AdministratorManual/TwitterAccess.png
   :class: with-shadow
   :alt: Backend view

   Twitter keys and tokens


Youtube token
"""""""""""""

Youtube token require only Api Key.

`Developers documentation <https://developers.google.com/youtube/v3/getting-started>`_

Source Configuration
^^^^^^^^^^^^^^^^^^^^

After token is ready it's possible to create new configuration that will be used by scheduler task to fetch social posts.

Configuration has next fields:

- Name - just a name, could be anything.
- Social ID (feed source) - social account account ID. It's possible to set it after configuration was saved.
- Maximum items - how many items should task fetch at once. **Extension won't store more this amount of post items in TYPO3**
- Storage - where to save posts.
- Token - what token to use to access social network account.


.. tip::

   For Facebook and Instagram tokens you will be able to choose from what page to fetch posts. For Instagram account you need to select
   connected page to that account.

   For Twitter or Youtube you need to enter accounts IDs.

Scheduler task
^^^^^^^^^^^^^^

After "Configuration" was created you can **create new scheduler task that will use this configuration** to fetch posts from social network.

Optionally you can add **Receiver email** and **Sender email** if want to get notifications about import errors or **Facebook/Instagram "Access Token" expire warning**.

.. important::
    Both receiver and sender should be valid email addresses.


.. figure:: ../Images/AdministratorManual/Scheduler.png
   :class: with-shadow
   :alt: Backend view

   Scheduler task
