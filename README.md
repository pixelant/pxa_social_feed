
# Pixelant #  
[![Build Status](https://travis-ci.org/pixelant/pxa_social_feed.svg?branch=master)](https://travis-ci.org/pixelant/pxa_social_feed)  
  
### Pxa Social Feed ###  
  
Add Facebook, Instagram, Twitter and Youtube feed on the site.  
  
### How to setup ? ###  
  
#### Install extension 

Use composer to get extension.
  

    composer require pixelant/pxa-social-feed

#### Configure extension in TYPO3 backend
There are 4 main steps in order to get it work:  
  
* Configure access tokens and feeds configuration for facebook, instagram, twitter and youtube in BE module.  Read more about this on TYPO3 repository extension documentation.
* Include extension TypoScript.
* Add frontend plugin on page.
* Configure scheduler task.
