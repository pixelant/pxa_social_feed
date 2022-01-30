# Pixelant #

### Pxa Social Feed ###

Add Facebook, Instagram, Twitter, and YouTube feeds to your website.

[Official documentation](https://docs.typo3.org/p/pixelant/pxa-social-feed/master/en-us/)

### How to setup? ###

#### Install extension

Use composer to get extension.

    composer require pixelant/pxa-social-feed

#### Configure extension in TYPO3 backend

There are 4 main steps in order to get it to work:

* Configure access tokens and feeds configuration for Facebook, Instagram, Twitter and YouTube in BE module.  Read more about this on TYPO3 repository extension documentation.
* Include extension TypoScript.
* Add frontend plugin on page.
* Configure scheduler task.
